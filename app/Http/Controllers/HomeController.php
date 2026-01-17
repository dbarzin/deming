<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        abort_if(Auth::User()->isAPI(),Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Fetch counts and data using optimized queries
        $activeDomainsCount = $this->getActiveDomainsCount();
        $controlsCount = $this->getControlsCount();
        $activeMeasuresCount = $this->getActiveMeasuresCount();
        $controlsMadeCount = $this->getControlsMadeCount();
        $controlsNeverMade = $this->getControlsNeverMade();
        $planedControlsThisMonthCount = $this->getPlanedControlsThisMonthCount();
        $lateControlsCount = $this->getLateControlsCount();
        $actionPlansCount = $this->getActionPlansCount();

        $activeControls = $this->getActiveControls();
        $controlsTodo = $this->getControlsTodo();
        $expandedControls = $this->getExpandedControls();

        // Store counts in session
        $request->session()->put([
            'planed_controls_this_month_count' => $planedControlsThisMonthCount,
            'late_controls_count' => $lateControlsCount,
            'action_plans_count' => $actionPlansCount,
        ]);

        // Return view with data
        return view('welcome', [
            'active_domains_count' => $activeDomainsCount,
            'controls_count' => $controlsCount,
            'active_measures_count' => $activeMeasuresCount,
            'controls_made_count' => $controlsMadeCount,
            'controls_never_made' => $controlsNeverMade,
            'active_controls' => $activeControls,
            'controls_todo' => $controlsTodo,
            'action_plans_count' => $actionPlansCount,
            'late_controls_count' => $lateControlsCount,
            'controls' => $expandedControls,
        ]);
    }

    private function getActiveDomainsCount()
    {
        $query = DB::table('controls')
            ->join('control_measure', 'controls.id', '=', 'control_id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereIn('status', [0, 1])
            ->distinct('measures.domain_id')
            ->count('measures.domain_id');
    }
    private function getControlsCount()
    {
        $query = DB::table('measures');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->whereExists(function($q) use ($userId) {
                $q->select(DB::raw(1))
                    ->from('control_measure')
                    ->whereColumn('control_measure.measure_id', 'measures.id')
                    ->where(function($subQuery) use ($userId) {
                        $subQuery->whereExists(function($subQ) use ($userId) {
                            $subQ->select(DB::raw(1))
                                ->from('control_user')
                                ->whereColumn('control_user.control_id', 'control_measure.control_id')
                                ->where('control_user.user_id', $userId);
                        })
                            ->orWhereExists(function($subQ) use ($userId) {
                                $subQ->select(DB::raw(1))
                                    ->from('control_user_group')
                                    ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                                    ->whereColumn('control_user_group.control_id', 'control_measure.control_id')
                                    ->where('user_user_group.user_id', $userId);
                            });
                    });
            });
        }

        return $query->count();
    }

    private function getActiveMeasuresCount()
    {
        $query = DB::table('controls');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereIn('status', [0, 1])
            ->count();
    }

    private function getControlsMadeCount()
    {
        $query = DB::table('controls');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            })
            ->whereIn('status', [1, 2]);
        }
        else
            $query = $query->where('status', 2);
        return $query->count();
    }

    private function getControlsNeverMade()
    {
        $query = DB::table('controls as c1')
            ->leftJoin('controls as c2', 'c2.next_id', '=', 'c1.id');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'c1.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'c1.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereNull('c1.realisation_date')
            ->whereNull('c2.id')
            ->count();
    }

    private function getPlanedControlsThisMonthCount()
    {
        $query = DB::table('controls');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereNull('realisation_date')
            ->whereBetween('plan_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();
    }


    private function getLateControlsCount()
    {
        $query = DB::table('controls');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereNull('realisation_date')
            ->where('plan_date', '<', Carbon::today())
            ->count();
    }

    private function getActionPlansCount()
    {
        $query = DB::table('actions');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->whereExists(function($q) use ($userId) {
                $q->select(DB::raw(1))
                    ->from('action_user')
                    ->whereColumn('action_user.action_id', 'actions.id')
                    ->where('action_user.user_id', $userId);
            });
        }

        return $query->where('status', 0)
            ->count();
    }

    private function getActiveControls()
    {
        $query = DB::table('controls as c1')
            ->select(['c1.id', 'measures.id', 'domains.title', 'c1.realisation_date', 'c1.score'])
            ->join('controls as c2', 'c2.id', '=', 'c1.next_id')
            ->join('control_measure', 'control_measure.control_id', '=', 'c1.id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->join('domains', 'domains.id', '=', 'measures.domain_id');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'c1.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'c1.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        return $query->whereNull('c2.realisation_date')
            ->orderBy('c1.id')
            ->get();
    }
    private function getControlsTodo()
    {
        $query = DB::table('controls as c1')
            ->select([
                'c1.id', 'c1.name', 'c1.scope', 'c1.plan_date', 'c1.status',
                'c2.id as prev_id', 'c2.realisation_date as prev_date', 'c2.score as score',
            ])
            ->leftJoin('controls as c2', 'c1.id', '=', 'c2.next_id');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'c1.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'c1.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            })
                // Uniquement les contrôle à faire
                ->where('c1.status', '=', 0);
        }
        else
            // Pour les contrôles à faire et à valider
            $query = $query->whereIn('c1.status', [0, 1]);

        $controlsTodo = $query
            ->where('c1.plan_date', '<', Carbon::today()->addDays(30))
            ->orderBy('c1.plan_date')
            ->get();

        // Fetch related control measures in a single query
        $controlMeasures = DB::table('control_measure')
            ->select(['control_id', 'measure_id', 'clause'])
            ->leftJoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controlsTodo->pluck('id'))
            ->orderBy('clause')
            ->get()
            ->groupBy('control_id');

        // Map over controlsTodo to add measures
        $controlsTodo->map(function ($control) use ($controlMeasures) {
            $control->measures = $controlMeasures->get($control->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->measure_id,
                    'clause' => $controlMeasure->clause,
                ];
            });
            return $control;
        });

        return $controlsTodo;
    }
    private function getExpandedControls()
    {
        $query = DB::table('controls')
            ->select('id', 'score', 'realisation_date', 'plan_date', 'periodicity');

        // Filtrer uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();
            $query->where(function($q) use ($userId) {
                $q->whereExists(function($subQ) use ($userId) {
                    $subQ->select(DB::raw(1))
                        ->from('control_user')
                        ->whereColumn('control_user.control_id', 'controls.id')
                        ->where('control_user.user_id', $userId);
                })
                    ->orWhereExists(function($subQ) use ($userId) {
                        $subQ->select(DB::raw(1))
                            ->from('control_user_group')
                            ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                            ->whereColumn('control_user_group.control_id', 'controls.id')
                            ->where('user_user_group.user_id', $userId);
                    });
            });
        }

        $controls = $query->get();

        return $controls->flatMap(function ($control) {
            $expanded = collect([$control]);

            if ($control->realisation_date === null && $control->periodicity > 0 && $control->periodicity <= 12) {
                for ($i = 1; $i <= 12 / $control->periodicity; $i++) {
                    $repeatedControl = clone $control;
                    $repeatedControl->id = null;
                    $repeatedControl->score = null;
                    $repeatedControl->observations = null;
                    $repeatedControl->realisation_date = null;
                    $repeatedControl->plan_date = Carbon::parse($control->plan_date)->addMonthsNoOverflow($i * $control->periodicity);
                    $expanded->push($repeatedControl);
                }
            }

            return $expanded;
        });
    }
}
