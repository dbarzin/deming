<?php

namespace App\Http\Controllers;

use App\Services\MonitoringService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Monitoring dashboard (Analysis menu).
     *
     * @return \Illuminate\View\View
     */
    public function index(MonitoringService $monitoring)
    {
        $user = Auth::user();

        // Only for admin, user and auditor roles
        abort_if(
            ! $user->isAdmin() && ! $user->isUser() && ! $user->isAuditor(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $data = $monitoring->compute($user->seesAllData() ? null : $user->id);

        return view('radar.monitoring', $data);
    }
}
