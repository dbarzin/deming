<?php

namespace App\Http\Controllers;

use App\Exports\RiskExport;
use App\Models\Action;
use App\Models\Measure;
use App\Models\Risk;
use App\Models\User;
use App\Services\RiskScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Gestion du registre des risques (ISO 27001 §6.1.2 / §8.2)
 *
 * Pattern URL calqué sur Deming existant (/bob/store, /bob/save, etc.)
 * Pas de Route::resource() — routes explicites dans ROUTES.php
 */
class RiskController extends Controller
{
    public function __construct(private RiskScoringService $scoringService)
    {
    }

    // =========================================================================
    // INDEX
    // =========================================================================

    public function index(Request $request): View
    {
        $user  = Auth::user();
        $query = Risk::with(['owner'])->orderByDesc('updated_at');

        if ($user->role === 3) {
            $query->ownedBy($user->id);
        }

        if ($request->filled('status') && array_key_exists($request->status, Risk::STATUS_LABELS)) {
            $query->byStatus($request->status);
        }

        if ($request->filled('owner') && $user->role !== 3) {
            $query->ownedBy((int) $request->owner);
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->filled('threshold') && is_numeric($request->threshold)) {
            $thresholds = $this->scoringService->config()->risk_thresholds;
            $idx = (int) $request->threshold;
            if (isset($thresholds[$idx])) {
                $min = $idx > 0 ? ($thresholds[$idx - 1]['max'] + 1) : 1;
                $max = $thresholds[$idx]['max'];
                $query->whereRaw('probability * impact >= ?', [$min]);
                if ($max) {
                    $query->whereRaw('probability * impact <= ?', [$max]);
                }
            }
        }


        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        // Persister les filtres en session (comme Deming le fait pour bob/index)
        session([
            'risk_status'  => $request->input('status'),
            'risk_owner'   => $request->input('owner'),
            'risk_overdue' => $request->input('overdue', '0'),
        ]);

        $risks   = $query->paginate(50)->withQueryString();
        $owners  = User::query()->orderBy('name')->get();
        $filters = $request->only(['status', 'owner', 'overdue', 'search']);
        $scoringConfig = $this->scoringService->config();

        return view('risks.index', compact('risks', 'owners', 'filters', 'scoringConfig'));
    }

    // =========================================================================
    // CREATE / STORE
    // =========================================================================

    public function create(): View
    {
        $users         = User::query()->orderBy('name')->get();
        $measures      = Measure::query()->orderBy('name')->get();
        $actions       = Action::query()->orderBy('name')->get();
        $statuses      = Risk::STATUS_LABELS;
        $scoringConfig = $this->scoringService->config();

        return view('risks.create',
            compact('users', 'measures', 'actions', 'statuses', 'scoringConfig'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRisk($request);

        $risk = Risk::query()->create($validated);

        if (empty($validated['next_review_at'])) {
            $risk->next_review_at = now()->addMonths((int) $risk->review_frequency);
            $risk->saveQuietly();
        }

        $this->syncRelations($risk, $request);
        $this->warnBusinessRules($risk);

        return redirect('/risk/show/' . $risk->id)
            ->with('success', __('Risque créé avec succès.'));
    }

    // =========================================================================
    // SHOW
    // =========================================================================

    public function show(int $id): View
    {
        $risk = Risk::query()->findOrFail($id);
        $this->authorizeView($risk);

        $risk->load(['owner', 'measures', 'actions']);

        $scoringConfig = $this->scoringService->config();

        return view('risks.show', compact('risk', 'scoringConfig'));
    }

    // =========================================================================
    // EDIT / UPDATE  (route POST /risk/save, comme /bob/save)
    // =========================================================================

    public function edit(int $id): View
    {
        $risk     = Risk::query()->findOrFail($id);
        $users    = User::query()->orderBy('name')->get();
        $measures = Measure::query()->orderBy('name')->get();
        $actions  = Action::query()->orderBy('name')->get();
        $statuses = Risk::STATUS_LABELS;
        $scoringConfig = $this->scoringService->config();

        $risk->load(['measures', 'actions']);

        return view('risks.edit',
            compact('risk', 'users', 'measures', 'actions', 'statuses', 'scoringConfig'));
    }

    public function update(Request $request): RedirectResponse
    {
        $risk      = Risk::query()->findOrFail($request->input('id'));
        $validated = $this->validateRisk($request);

        $frequencyChanged = (int) $validated['review_frequency'] !== $risk->review_frequency;
        if ($frequencyChanged && empty($validated['next_review_at'])) {
            $validated['next_review_at'] = now()->addMonths((int) $validated['review_frequency']);
        }

        $risk->update($validated);
        $risk->invalidateScoringCache();

        $this->syncRelations($risk, $request);
        $this->warnBusinessRules($risk);

        return redirect('/risk/show/' . $risk->id)
            ->with('success', __('Risque mis à jour.'));
    }

    // =========================================================================
    // DELETE  (route GET /risk/delete/{id}, comme /bob/delete/{id})
    // =========================================================================

    public function destroy(int $id): RedirectResponse
    {
        if (Auth::user()->role !== 1) {
            abort(403);
        }

        Risk::query()->findOrFail($id)->delete();

        return redirect('/risk/index')
            ->with('success', __('Risque supprimé.'));
    }

    // =========================================================================
    // MATRIX
    // =========================================================================

    public function matrix(): View
    {
        $risks = Risk::with('owner')->get();

        $matrix = $this->scoringService->buildMatrix($risks);
        $xAxis  = $this->scoringService->matrixXAxis();
        $yAxis  = $this->scoringService->matrixYAxis();

        $scoringConfig = $this->scoringService->config();
        $thresholds    = $scoringConfig->risk_thresholds;

        $stats = [
            'critical'  => $risks->filter(fn($r) => $r->risk_level === 'critical')->count(),
            'high'      => $risks->filter(fn($r) => $r->risk_level === 'high')->count(),
            'medium'      => $risks->filter(fn($r) => $r->risk_level === 'medium')->count(),
            'low'       => $risks->filter(fn($r) => $r->risk_level === 'low')->count(),
            'total'     => $risks->count(),
            'overdue'   => $risks->filter(fn($r) => $r->is_overdue)->count(),
            'by_status' => $risks->groupBy('status')->map->count(),

            'by_level' => collect($thresholds)
                ->mapWithKeys(fn($t, $i) => [
                    $i => $risks->filter(function ($r) use ($thresholds, $i) {
                        $score = $r->probability * $r->impact;
                        $min   = $i > 0 ? ($thresholds[$i - 1]['max'] + 1) : 1;
                        $max   = $thresholds[$i]['max'];
                        return $max ? ($score >= $min && $score <= $max) : $score >= $min;
                    })->count(),
                ]),
        ];

        $scoringConfig = $this->scoringService->config();

        return view('risks.matrix', compact('matrix', 'stats', 'scoringConfig', 'xAxis', 'yAxis'));
    }

    // =========================================================================
    // Privé
    // =========================================================================

    private function validateRisk(Request $request): array
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'owner_id'            => ['nullable', 'exists:users,id'],
            'probability'         => ['required', 'integer', 'min:1'],
            'probability_comment' => ['nullable', 'string'],
            'impact'              => ['required', 'integer', 'min:1'],
            'impact_comment'      => ['nullable', 'string'],
            'exposure'            => ['nullable', 'integer', 'min:0'],
            'vulnerability'       => ['nullable', 'integer', 'min:1'],
            'status'              => ['required', 'in:' . implode(',', array_keys(Risk::STATUS_LABELS))],
            'status_comment'      => ['nullable', 'string'],
            'review_frequency'    => ['required', 'integer', 'min:1', 'max:60'],
            'next_review_at'      => ['nullable', 'date'],
            'measure_ids'         => ['nullable', 'array'],
            'measure_ids.*'       => ['exists:measures,id'],
            'action_ids'          => ['nullable', 'array'],
            'action_ids.*'        => ['exists:actions,id'],
        ]);

        // Laravel retourne les champs numériques sous forme de string depuis le POST.
        // Carbon::addMonths() et les comparaisons requièrent des int.
        foreach (['probability', 'impact', 'review_frequency'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }
        foreach (['exposure', 'vulnerability', 'owner_id'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = (int) $data[$field];
            }
        }

        return $data;
    }

    private function syncRelations(Risk $risk, Request $request): void
    {
        $risk->measures()->sync($request->input('measure_ids', []));
        $risk->actions()->sync($request->input('action_ids', []));
    }

    private function warnBusinessRules(Risk $risk): void
    {
        if ($risk->requiresMeasures() && $risk->measures()->count() === 0) {
            session()->flash('warning', __('Un risque "Mitigé" doit avoir au moins un contrôle lié.'));
        }
        if ($risk->requiresActions() && $risk->actions()->count() === 0) {
            session()->flash('warning', __('Un risque "Non accepté" doit avoir au moins un plan d\'action lié.'));
        }
    }

    private function authorizeView(Risk $risk): void
    {
        if (Auth::user()->role === 3 && $risk->owner_id !== Auth::id()) {
            abort(403);
        }
    }

    public function export()
    {
        // For administrators and users only
        abort_if(
            !Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return Excel::download(
            new RiskExport(),
            trans('cruds.risk.plural') .
            '-' .
            now()->format('Y-m-d Hi') .
            '.xlsx'
        );
    }

}