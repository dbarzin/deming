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

        $attribute = new Attribute();
        $attribute->name = request('name');
        $attribute->values = request('values');
        $attribute->save();
        return redirect('/attributes');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attribute $attribute
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attribute)
    {
        return view('attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attribute $attribute
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute)
    {
        return view('attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Attribute              $attribute
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:30',
                'values' => 'required',
            ]
        );
        $attribute->name = request('name');
        $attribute->values = request('values');
        $attribute->save();
        return redirect('/attributes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attribute $attribute
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect('/attributes');
    }

    public function export()
    {
        return Excel::download(new AttributesExport(), 'attributes.xlsx');
    }
}
