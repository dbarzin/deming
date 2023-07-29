<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DomainController extends Controller
{
    public function index()
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activities = Domain::all();

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain = Domain::create($request->all());

        return response()->json($domain, 201);
    }

    public function show(Domain $domain)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($domain);
    }

    public function update(Request $request, Domain $domain)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->update($request->all());

        return response()->json();
    }

    public function destroy(Domain $domain)
    {
        abort_if(Auth::User()->role !== 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->delete();

        return response()->json();
    }
}
