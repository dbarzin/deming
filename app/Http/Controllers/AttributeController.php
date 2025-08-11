<?php

namespace App\Http\Controllers;

use App\Exports\AttributesExport;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
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
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('attributes.create');
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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:30',
                'values' => "required|regex:/^(#[\p{L}\p{M}\p{N}'_-]+ *)*$/u|max:4000",
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
     * @param  \App\Models\Attribute $attribute
     *
     * @return \Illuminate\View\View
     */
    public function show(Attribute $attribute)
    {
        return view('attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attribute $attribute
     *
     * @return \Illuminate\View\View
     */
    public function edit(Attribute $attribute)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Attribute    $attribute
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Attribute $attribute)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:30',
                'values' => "required|regex:/^(#[\p{L}\p{M}\p{N}'_-]+ *)*$/u|max:4000",
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
     * @param  \App\Models\Attribute $attribute
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Attribute $attribute)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attribute->delete();

        return redirect('/attributes');
    }

    public function export()
    {
        return Excel::download(new AttributesExport(), 'attributes.xlsx');
    }
}
