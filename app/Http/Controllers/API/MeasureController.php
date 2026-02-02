<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Measure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MeasureController extends Controller
{
    public function index()
    {
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $measures = Measure::all();

        return response()->json($measures);
    }

    public function store(Request $request)
    {
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $measure = Measure::query()->create($request->all());
        if ($request->has('controls')) {
            $measure->controls()->sync($request->input('controls', []));
        }

        return response()->json($measure, 201);
    }

    public function show(Measure $measure)
    {
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($measure);
    }

    public function update(Request $request, Measure $measure)
    {
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $measure->update($request->all());
        if ($request->has('controls')) {
            $measure->controls()->sync($request->input('controls', []));
        }

        return response()->json();
    }

    public function destroy(Measure $measure)
    {
        abort_if(Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $measure->delete();

        return response()->json();
    }
}
