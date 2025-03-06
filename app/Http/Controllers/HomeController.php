<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Redirect user to controls list if role is 5
        if (Auth::user()->role === 5) {
            return redirect('/bob/index');
        }

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
        return DB::table('controls')
            ->join('control_measure', 'controls.id', '=', 'control_id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->whereIn('status', [0, 1])
            ->distinct('measures.domain_id')
            ->count('measures.domain_id');
    }

    private function getControlsCount()
    {
        return DB::table('measures')->count();
    }

    private function getActiveMeasuresCount()
    {
        return DB::table('controls')
            ->whereIn('status', [0, 1])
            ->count();
    }

    private function getControlsMadeCount()
    {
        return DB::table('controls')
            ->where('status', 2)
            ->count();
    }

    private function getControlsNeverMade()
    {
        return DB::table('controls as c1')
            ->leftJoin('controls as c2', 'c2.next_id', '=', 'c1.id')
            ->whereNull('c1.realisation_date')
            ->whereNull('c2.id')
            ->count();
    }

    private function getPlanedControlsThisMonthCount()
    {
        return DB::table('controls')
            ->whereNull('realisation_date')
            ->whereBetween('plan_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();
    }

    private function getLateControlsCount()
    {
        return DB::table('controls')
            ->whereNull('realisation_date')
            ->where('plan_date', '<', Carbon::today())
            ->count();
    }

    private function getActionPlansCount()
    {
        return DB::table('actions')
            ->where('status', 0)
            ->count();
    }

    private function getActiveControls()
    {
        return DB::table('controls as c1')
            ->select(['c1.id', 'measures.id', 'domains.title', 'c1.realisation_date', 'c1.score'])
            ->join('controls as c2', 'c2.id', '=', 'c1.next_id')
            ->join('control_measure', 'control_measure.control_id', '=', 'c1.id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->join('domains', 'domains.id', '=', 'measures.domain_id')
            ->whereNull('c2.realisation_date')
            ->orderBy('c1.id')
            ->get();
    }

    private function getControlsTodo()
    {
        // Fetch the controls todo with necessary joins
        $controlsTodo = DB::table('controls as c1')
            ->select([
                'c1.id', 'c1.name', 'c1.scope', 'c1.plan_date', 'c1.status',
                'c2.id as prev_id', 'c2.realisation_date as prev_date', 'c2.score as score',
            ])
            ->leftJoin('controls as c2', 'c1.id', '=', 'c2.next_id')
            ->whereIn('c1.status', [0, 1])
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
        $controls = DB::table('controls')
            ->select('id', 'score', 'realisation_date', 'plan_date', 'periodicity')
            ->get();

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
