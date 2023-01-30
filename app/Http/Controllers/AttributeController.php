<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\Exports\AttributesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = DB::table('attributes')->orderBy('id')->get();
        return view('attributes.index')
            ->with('attributes', $attributes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('attributes.create');
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
                'name' => 'required|min:1|max:30',
                'values' => 'required',
            ]
        );

        $domain = new Attribute();
        $domain->title = request('name');
        $domain->description = request('value');
        $domain->save();
        return redirect('/attributes');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $tag)
    {
        return view('attributes.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $tag)
    {
        return view('attributes.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $tag)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:30',
                'values' => 'required',
            ]
        );
        $domain->name = request('name');
        $domain->values = request('values');
        $domain->save();
        return redirect('/attributes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $tag)
    {
        $tag->delete();
        return redirect('/attributes');
    }

    public function export()
    {
        return Excel::download(new AttributesExport(), 'attributes.xlsx');
    }
}
