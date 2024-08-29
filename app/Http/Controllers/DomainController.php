<?php

namespace App\Http\Controllers;

use App\Exports\DomainsExport;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = DB::table('domains')->orderBy('id')->get();

        $domains = DB::table('domains')
            ->select('domains.id', 'domains.framework', 'domains.title', 'domains.description', DB::raw('COUNT(measures.id) AS measures'))
            ->leftJoin('measures', 'measures.domain_id', '=', 'domains.id')
            ->groupBy('domains.id')
            ->orderBy('domains.title')
            ->get();

        return view('domains.index')
            ->with('domains', $domains);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('domains.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

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
     * @param  \App\Domain $Domain
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $domain = Domain::find($id);

        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain = Domain::find($id);

        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

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
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Has measures ?
        abort_if(DB::table('measures')
            ->where('domain_id', $domain->id)
            ->exists(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $domain->delete();

        return redirect('/domains');
    }

    public function export()
    {
        return Excel::download(new DomainsExport(), 'domains.xlsx');
    }
}
