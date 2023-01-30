<?php

namespace App\Http\Controllers;

use App\Domain;
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
    public function show(Domain $tag)
    {
        return view('domains.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $tag)
    {
        return view('domains.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $tag)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:30',
                'values' => 'required',
            ]
        );
        $tag->name = request('name');
        $tag->values = request('values');
        $Domain->save();
        return redirect('/domains');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $tag
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $tag)
    {
        // dd($Domain);
        $tag->delete();
        return redirect('/domains');
    }

    public function export()
    {
        return Excel::download(new DomainsExport(), 'domains.xlsx');
    }
}
