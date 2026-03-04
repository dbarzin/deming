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
        abort(Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function show(AuditLog $log)
    {
        abort_if(!Auth::User()->isAPI(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response()->json($log);
    }

    public function update(Request $request, AuditLog $log)
    {
        abort(Response::HTTP_UNAUTHORIZED, '401 Unauthorized');
    }

    public function destroy(AuditLog $log)
    {
        abort(Response::HTTP_UNAUTHORIZED, '401 Unauthorized');
    }
}
