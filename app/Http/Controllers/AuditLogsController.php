<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $logs = DB::table('audit_logs')
            ->select(
                'audit_logs.id',
                'description',
                'subject_type',
                'subject_id',
                'users.name',
                'user_id',
                'host',
                'audit_logs.created_at'
            )
            ->join('users', 'users.id', '=', 'user_id')
            ->orderBy('audit_logs.id', 'desc')
            ->paginate(100);

        return view('logs.index', ['logs' => $logs]);
    }

    public function show(int $id)
    {
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get audit Log
        $auditLog = AuditLog::find($id);

        // Control not found
        abort_if($auditLog === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        return view('logs.show', compact('auditLog'));
    }

    public function history(int $id)
    {
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get audit Log
        $auditLog = AuditLog::find($id);

        abort_if($auditLog === null, 400, '400 log not found');

        // Get the list
        $auditLogs =
            DB::table('audit_logs')
                ->select(
                    'audit_logs.id',
                    'description',
                    'subject_type',
                    'subject_id',
                    'users.name',
                    'user_id',
                    'host',
                    'properties',
                    'audit_logs.created_at'
                )
                ->join('users', 'users.id', '=', 'user_id')
                ->where('subject_id', $auditLog->subject_id)
                ->where('subject_type', $auditLog->subject_type)
                ->orderBy('audit_logs.id')
                ->get();

        abort_if($auditLogs->isEmpty(), 404, 'Not found');

        // JSON decode all properties
        foreach ($auditLogs as $auditLog) {
            $auditLog->properties = json_decode(trim(stripslashes($auditLog->properties), '"'));
        }

        return view('logs.history', compact('auditLogs'));
    }
}
