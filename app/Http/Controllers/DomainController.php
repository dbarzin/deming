<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Exports\DomainsExport;
use Illuminate\Http\Request;
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
            ->select('domains.id', 'domains.title', 'domains.description', DB::raw('COUNT(measures.id) AS cnt'))
            ->leftJoin('measures', 'measures.domain_id', '=', 'domains.id')
            ->groupBy('domains.id')
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
        $this->validate(
            $request,
            [
                'title' => 'required|min:1|max:30',
                'description' => 'required',
            ]
        );

        $tag = new Domain();
        $tag->title = request('title');
        $tag->description = request('description');
        $tag->save();
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
        $this->validate(
            $request,
            [
                'title' => 'required|min:1|max:30',
                'description' => 'required',
            ]
        );
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
        $domain->delete();
        return redirect('/domains');
    }

    public function export()
    {
        return Excel::download(new DomainsExport(), 'domains.xlsx');
    }
}
