<?php

namespace App\Http\Controllers\API;

use App\Domain;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDomainRequest;
use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Http\Resources\Admin\DomainResource;
use Gate;
use Illuminate\Http\Response;

class DomainController extends Controller
{
    public function index()
    {
        Log::debug("DomainController.index start");
        abort_if(Auth::User()->role!==4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $activities = Domain::all();

        Log::debug("DomainController.index Done.");
        return response()->json($activities);
    }

    public function store(StoreDomainRequest $request)
    {
        abort_if(Auth::User()->role!==4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain = Domain::create($request->all());

        return response()->json($domain, 201);
    }

    public function show(Domain $domain)
    {
        abort_if(Auth::User()->role!==4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DomainResource($domain);
    }

    public function update(UpdateDomainRequest $request, Domain $domain)
    {
        abort_if(Auth::User()->role!==4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->update($request->all());

        return response()->json();
    }

    public function destroy(Domain $domain)
    {
        abort_if(Auth::User()->role!==4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $domain->delete();

        return response()->json();
    }

    public function massDestroy(MassDestroyDomainRequest $request)
    {
        Domain::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
