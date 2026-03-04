<?php

namespace App\Http\Controllers;

use App\Exports\DomainsExport;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DomainController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $userId = Auth::id();

        if (Auth::user()->isAuditee()) {
            $domains = DB::table('domains')
                ->select(
                    'domains.id',
                    'domains.framework',
                    'domains.title',
                    'domains.description',
                    DB::raw('(
                    SELECT COUNT(DISTINCT m.id)
                    FROM measures m
                    WHERE m.domain_id = domains.id
                    AND EXISTS (
                        SELECT 1
                        FROM control_measure cm
                        WHERE cm.measure_id = m.id
                        AND (
                            EXISTS (
                                SELECT 1
                                FROM control_user cu
                                WHERE cu.control_id = cm.control_id
                                AND cu.user_id = ?
                            )
                            OR EXISTS (
                                SELECT 1
                                FROM control_user_group cug
                                INNER JOIN user_user_group uug
                                    ON uug.user_group_id = cug.user_group_id
                                WHERE cug.control_id = cm.control_id
                                AND uug.user_id = ?
                            )
                        )
                    )
                ) AS measures_count')
                )
                ->addBinding([$userId, $userId], 'select')
                ->havingRaw('measures_count > 0')
                ->orderBy('domains.title')
                ->get();
        } else {
            $domains = DB::table('domains')
                ->select(
                    'domains.id',
                    'domains.framework',
                    'domains.title',
                    'domains.description',
                    DB::raw('COUNT(measures.id) AS measures_count')
                )
                ->leftJoin('measures', 'measures.domain_id', '=', 'domains.id')
                ->groupBy('domains.id', 'domains.framework', 'domains.title', 'domains.description')
                ->orderBy('domains.title')
                ->get();
        }

        return view('domains.index', compact('domains'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        // Only for administrator role
        abort_if(!Auth::User()->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('domains.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Only for administrator role
        abort_if(!Auth::User()->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate(
            $request,
            [
                'framework' => 'required|min:1|max:32',
                'title' => 'required|min:1|max:32',
                'description' => 'required|max:255',
            ]
        );

        $domain = new Domain();
        $domain->framework = request('framework');
        $domain->description = request('description');
        $domain->title = request('title');
        $domain->save();

        return redirect('/domains');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return View
     */
    public function show(int $id)
    {
        $domain = Domain::query()->find($id);

        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return View
     */
    public function edit(int $id)
    {
        // Only for administrator role
        abort_if(!Auth::User()->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain = Domain::find($id);

        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Domain $domain
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Domain $domain)
    {
        // Only for administrator role
        abort_if(!Auth::User()->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate(
            $request,
            [
                'framework' => 'required|min:1|max:32',
                'title' => 'required|min:1|max:32',
                'description' => 'required|max:255',
            ]
        );

        $domain->framework = request('framework');
        $domain->title = request('title');
        $domain->description = request('description');
        $domain->save();

        return redirect('/domains/' . $domain->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Domain $domain
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Domain $domain)
    {
        // Only for administrator role
        abort_if(!Auth::User()->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Has measures ?
        if (DB::table('measures')
            ->where('domain_id', $domain->id)
            ->join('control_measure', 'measures.id', 'control_measure.measure_id')
            ->exists()) {
            return back()
                ->withErrors(['msg' => 'There are measures associated with this framework !'])
                ->withInput();
        }

        // Delete measures
        DB::table('measures')->where('domain_id', $domain->id)->delete();

        // Delete domain
        $domain->delete();

        return redirect('/domains');
    }

    /*
     * Export the Domains in Excel
     *
     */
    public function export(): BinaryFileResponse
    {
        abort_if(
            !Auth::User()->isAdmin(),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new DomainsExport(), 'domains.xlsx');
    }
}
