<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index()
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $auditLogs = AuditLog::all();

        return response()->json($auditLogs);
    }

    public function store(Request $request)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $auditLog = AuditLog::query()->create($request->all());

        return response()->json($auditLog, 201);
    }

    public function show(AuditLog $auditLog)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($auditLog);
    }

    public function update(Request $request, AuditLog $auditLog)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $auditLog->update($request->all());

        return response()->json();
    }

    public function destroy(AuditLog $auditLog)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $auditLog->delete();

        return response()->json();
    }
}
