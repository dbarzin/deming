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
        // redirect user to controls list
        if (Auth::User()->role === 5) {
            return redirect('/bob/index');
        }

        // count active domains
        $active_domains_count = DB::table('controls')
            ->select('measures.domain_id')
            ->join('control_measure', 'controls.id', '=', 'control_id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->whereIn('status', [0,1])
            ->distinct()
            ->get()
            ->count();

        // count all measures
        $controls_count = DB::table('measures')
            ->count();

        // count active controls
        $active_measures_count = DB::table('controls')
            //->whereNull('realisation_date')
            ->whereIn('status', [0,1])
            ->count();

        // count controls made
        $controls_made_count = DB::table('controls')
            //->whereNotNull('realisation_date')
            ->where('status', 2)
            ->count();

        // count control never made
        $controls_never_made = DB::table('controls as c1')
            ->leftJoin('controls as c2', 'c2.next_id', '=', 'c1.id')
            ->whereNull('c1.realisation_date')
            ->whereNull('c2.id')
            ->count();

        // Last controls made by measures
        $active_controls =
        DB::table('controls as c1')
            ->select(['c1.id', 'measures.id', 'domains.title', 'c1.realisation_date', 'c1.score'])
            ->join('controls as c2', 'c2.id', '=', 'c1.next_id')
            ->join('control_measure', 'control_measure.control_id', '=', 'c1.id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->join('domains', 'domains.id', '=', 'measures.domain_id')
            ->whereNull('c2.realisation_date')
            ->orderBy('c1.id')
            ->get();

        // Get controls todo
        $controls_todo =
            DB::table('controls as c1')
                ->select([
                    'c1.id',
                    'c1.name',
                    'c1.scope',
                    'c1.plan_date',
                    'c1.status',
                    'c2.id as prev_id',
                    'c2.realisation_date as prev_date',
                    'c2.score as score',
                ])
                ->leftjoin('controls as c2', 'c1.id', '=', 'c2.next_id')
                ->whereIn('c1.status', [0,1])
                ->where('c1.plan_date', '<', Carbon::today()->addDays(30)->format('Y-m-d'))
                ->orderBy('c1.plan_date')
                ->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'control_id',
                'measure_id',
                'clause',
            ])
            ->leftjoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls_todo->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach ($controls_todo as $control) {
            $control->measures = $measuresByControlId->get($control->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->measure_id,
                    'clause' => $controlMeasure->clause,
                ];
            });
        }

        // planed controls this month
        $planed_controls_this_month_count = DB::table('controls')
            ->where(
                [
                    ['realisation_date','=',null],
                    ['plan_date','>=', (new Carbon('first day of this month'))->toDateString()],
                    ['plan_date','<', (new Carbon('first day of next month'))->toDateString()],
                ]
            )
            ->count();
        $request->session()->put('planed_controls_this_month_count', $planed_controls_this_month_count);

        // late controls
        $late_controls_count = DB::table('controls')
            ->where(
                [
                    ['realisation_date','=',null],
                    ['plan_date','<', Carbon::today()->format('Y-m-d')],
                ]
            )
            ->count();
        $request->session()->put('late_controls_count', $late_controls_count);

        // Count number of action plans
        $action_plans_count =
                DB::table('actions')
                    ->where('status',0)
                    ->count();

        $request->session()->put('action_plans_count', $action_plans_count);

        // Get all controls
        $controls = DB::table('controls')
            ->select(['id', 'score', 'realisation_date', 'plan_date'])
            ->get();

        // return
        return view('welcome')
            ->with('active_domains_count', $active_domains_count)
            ->with('active_controls', $active_controls)
            ->with('controls_count', $controls_count)
            ->with('active_measures_count', $active_measures_count)
            ->with('controls_made_count', $controls_made_count)
            ->with('controls_never_made', $controls_never_made)

            ->with('controls_todo', $controls_todo)
            ->with('active_controls', $active_controls)
            ->with('action_plans_count', $action_plans_count)
            ->with('late_controls_count', $late_controls_count)

            ->with('controls', $controls)
        ;
    }

    public function test()
    {
        $domain = DB::table('domains')->first();

        return view('test')
            ->with('domain', $domain);
    }
}
