<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Control;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function index()
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activities = Control::all();

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control = Control::create($request->all());

        if ($request->has('measures')) $control->measures()->sync($request->input('measures', []));
        if ($request->has('actions')) $control->actions()->sync($request->input('actions', []));
        if ($request->has('documents')) $control->documents()->sync($request->input('documents', []));
        if ($request->has('users')) $control->users()->sync($request->input('users', []));
        if ($request->has('groups')) $control->groups()->sync($request->input('groups', []));

        return response()->json($control, 201);
    }

    public function show(Control $control)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($control);
    }

    public function update(Request $request, Control $control)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control->update($request->all());

        if ($request->has('measures')) $control->measures()->sync($request->input('measures', []));
        if ($request->has('actions')) $control->actions()->sync($request->input('actions', []));
        if ($request->has('documents')) $control->documents()->sync($request->input('documents', []));
        if ($request->has('users')) $control->users()->sync($request->input('users', []));
        if ($request->has('groups')) $control->groups()->sync($request->input('groups', []));

        return response()->json();
    }

    public function destroy(Control $control)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control->measures()->detach();
        $control->delete();

        return response()->json();
    }
}
