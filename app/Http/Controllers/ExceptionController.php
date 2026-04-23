<?php

namespace App\Http\Controllers;

use App\Models\Exception;
use App\Models\Measure;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Gestion des exceptions (issue #590)
 *
 * Accès : Admin (role=1) et User (role=2) uniquement.
 * Auditee (role=3) et Auditor (role=4) n'ont pas accès.
 *
 * Workflow :
 *   0 Brouillon  → peut être soumise par le créateur
 *   1 Soumise    → peut être approuvée ou refusée par l'Admin
 *   2 Approuvée  → état terminal (archivage possible)
 *   3 Refusée    → état terminal
 *   4 Expirée    → géré par le scheduler quand end_date est dépassée
 *
 * Routes (calquées sur le pattern Deming) :
 *   GET  /exception/index
 *   GET  /exception/create
 *   POST /exception/store
 *   GET  /exception/show/{id}
 *   GET  /exception/edit/{id}
 *   POST /exception/save
 *   GET  /exception/delete/{id}
 *   POST /exception/submit
 *   POST /exception/approve
 *   POST /exception/reject
 */
class ExceptionController extends Controller
{
    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request): View
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $query = Exception::with(['measure', 'createdBy'])
            ->orderByDesc('updated_at');

        // Filtre sur le statut
        if ($request->filled('status') && array_key_exists($request->status, Exception::STATUS_LABELS)) {
            $query->where('status', $request->status);
        }

        // Filtre sur la mesure liée
        if ($request->filled('measure_id')) {
            $query->where('measure_id', (int) $request->measure_id);
        }

        // Filtre sur les exceptions expirées
        if ($request->boolean('expired')) {
            $query->where('end_date', '<', now());
        }

        // Persistance des filtres en session (pattern Deming)
        session([
            'exception_status'   => $request->input('status'),
            'exception_measure'  => $request->input('measure_id'),
            'exception_expired'  => $request->input('expired', '0'),
        ]);

        $exceptions  = $query->paginate(50)->withQueryString();
        $measures    = Measure::query()->orderBy('name')->get();
        $filters     = $request->only(['status', 'measure_id', 'expired']);

        return view('exceptions.index', compact('exceptions', 'measures', 'filters'));
    }

    // =========================================================================
    // CREATE / STORE
    // =========================================================================

    public function create(): View
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // On ne propose que les mesures ayant au moins un contrôle non conforme
        $measures = Measure::query()->orderBy('name')->get();
        $statuses = Exception::STATUS_LABELS;

        return view('exceptions.create', compact('measures', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $validated = $this->validateException($request);
        $validated['created_by'] = Auth::id();
        $validated['status']     = Exception::STATUS_DRAFT;

        $exception = Exception::query()->create($validated);

        return redirect('/exception/show/' . $exception->id)
            ->with('success', __('Exception créée avec succès.'));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function show(int $id): View
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $exception = Exception::with(['measure', 'createdBy', 'submittedBy', 'approvedBy'])
            ->findOrFail($id);

        return view('exceptions.show', compact('exception'));
    }

    // =========================================================================
    // EDIT / UPDATE  (POST /exception/save, comme /bob/save)
    // =========================================================================

    public function edit(int $id): View
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $exception = Exception::query()->findOrFail($id);

        // Seul un brouillon ou un refus peut être modifié
        abort_if(
            !in_array($exception->status, [Exception::STATUS_DRAFT, Exception::STATUS_REJECTED]),
            Response::HTTP_FORBIDDEN,
            'Une exception soumise ou approuvée ne peut pas être modifiée.'
        );

        $measures = Measure::query()->orderBy('name')->get();
        $statuses = Exception::STATUS_LABELS;

        return view('exceptions.edit', compact('exception', 'measures', 'statuses'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $exception = Exception::query()->findOrFail($request->input('id'));

        abort_if(
            !in_array($exception->status, [Exception::STATUS_DRAFT, Exception::STATUS_REJECTED]),
            Response::HTTP_FORBIDDEN,
            'Une exception soumise ou approuvée ne peut pas être modifiée.'
        );

        $validated = $this->validateException($request);

        // Une exception refusée et ré-éditée repasse en brouillon
        if ($exception->status === Exception::STATUS_REJECTED) {
            $validated['status']       = Exception::STATUS_DRAFT;
            $validated['approved_by']  = null;
            $validated['approved_at']  = null;
            $validated['approval_comment'] = null;
        }

        $exception->update($validated);

        return redirect('/exception/show/' . $exception->id)
            ->with('success', __('Exception mise à jour.'));
    }

    // =========================================================================
    // DELETE  (GET /exception/delete/{id}, admin uniquement)
    // =========================================================================

    public function destroy(int $id): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        Exception::query()->findOrFail($id)->delete();

        return redirect('/exception/index')
            ->with('success', __('Exception supprimée.'));
    }

    // =========================================================================
    // WORKFLOW : SOUMETTRE  (POST /exception/submit)
    // =========================================================================

    public function submit(Request $request): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $exception = Exception::query()->findOrFail($request->input('id'));

        abort_if(
            $exception->status !== Exception::STATUS_DRAFT,
            Response::HTTP_FORBIDDEN,
            'Seul un brouillon peut être soumis à validation.'
        );

        $exception->update([
            'status'       => Exception::STATUS_SUBMITTED,
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        return redirect('/exception/show/' . $exception->id)
            ->with('success', __('Exception soumise à validation.'));
    }

    // =========================================================================
    // WORKFLOW : APPROUVER  (POST /exception/approve, admin uniquement)
    // =========================================================================

    public function approve(Request $request): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $request->validate([
            'id'               => ['required', 'exists:exceptions,id'],
            'approval_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $exception = Exception::query()->findOrFail($request->input('id'));

        abort_if(
            $exception->status !== Exception::STATUS_SUBMITTED,
            Response::HTTP_FORBIDDEN,
            'Seule une exception soumise peut être approuvée.'
        );

        $exception->update([
            'status'           => Exception::STATUS_APPROVED,
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'approval_comment' => $request->input('approval_comment'),
        ]);

        return redirect('/exception/show/' . $exception->id)
            ->with('success', __('Exception approuvée.'));
    }

    // =========================================================================
    // WORKFLOW : REFUSER  (POST /exception/reject, admin uniquement)
    // =========================================================================

    public function reject(Request $request): RedirectResponse
    {
        abort_if(
            !Auth::User()->isAdmin(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $request->validate([
            'id'               => ['required', 'exists:exceptions,id'],
            'approval_comment' => ['required', 'string', 'max:2000'],
        ]);

        $exception = Exception::query()->findOrFail($request->input('id'));

        abort_if(
            $exception->status !== Exception::STATUS_SUBMITTED,
            Response::HTTP_FORBIDDEN,
            'Seule une exception soumise peut être refusée.'
        );

        $exception->update([
            'status'           => Exception::STATUS_REJECTED,
            'approved_by'      => Auth::id(),
            'approved_at'      => now(),
            'approval_comment' => $request->input('approval_comment'),
        ]);

        return redirect('/exception/show/' . $exception->id)
            ->with('success', __('Exception refusée.'));
    }

    // =========================================================================
    // Privé
    // =========================================================================

    private function validateException(Request $request): array
    {
        return $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'justification'         => ['nullable', 'string'],
            'compensating_controls' => ['nullable', 'string'],
            'measure_id'            => ['nullable', 'exists:measures,id'],
            'start_date'            => ['nullable', 'date'],
            'end_date'              => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
    }
}
