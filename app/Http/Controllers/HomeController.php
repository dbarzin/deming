<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;

use App\Exports\MeasurementsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Control;
use App\Domain;
use App\Measure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

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
        // get all domains
        $domains = DB::table('domains')->get();

        // count active domains
        $active_domains_count = DB::table('controls')
            ->select(
                'domain_id',
                DB::raw('max(controls.id)'))
            ->whereNull('realisation_date')
            ->groupBy('domain_id')
            ->get()
            ->count();

        // count all controls
        $controls_count = DB::table('measures')            
            ->count();

        // count active mesures
        $active_measures_count = DB::table('controls')
            ->whereNull('realisation_date')
            ->count();

        // count controls made
        $controls_made_count = DB::table('controls')
            ->whereNotNull('realisation_date')
            ->count();

        // count control never made
        $controls_never_made = DB::select( 
            DB::raw("
                select domain_id 
                from controls c1 
                where realisation_date is null and 
                not exists (
                    select * 
                    from controls c2 
                    where realisation_date is not null and c1.measure_id=c2.measure_id);"
                ));

        // Last controls made by measures
        $active_controls = DB::select("
                select
                    c2.id,
                    c2.measure_id,
                    domains.title, 
                    c2.realisation_date, 
                    c2.score
                from 
                    (
                    select 
                        measure_id,
                        max(id) as id
                    from 
                        controls
                    where
                        realisation_date is not null
                    group by measure_id
                    ) as c1,                    
                    controls c2,
                    domains
                where
                    c1.id=c2.id and domains.id=c2.domain_id
                order by id;");
        //dd($status);

        // get controls todo
        $controls_todo=DB::select(
            DB::raw("select
                c1.id,
                c1.measure_id,
                c1.name,
                c1.clause,
                c1.domain_id,
                c1.plan_date,
                c2.id as prev_id,
                c2.realisation_date as prev_date,
                c2.score as score
            from
                controls c1 left join controls c2 on c2.next_id=c1.id
            where (c1.realisation_date is null) and (c1.plan_date < NOW() + INTERVAL 30 DAY)
            order by c1.plan_date"
            ));

        /*
        $controls_todo = DB::table('controls')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<",(new Carbon('first day of next month'))->toDateString()]
                ]
            )
            ->get();
        */
        // dd($plannedMeasurements);

        // planed controls this month
        $planed_controls_this_month_count = DB::table('controls')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date",">=", (new Carbon('first day of this month'))->toDateString()],
                    ["plan_date","<", (new Carbon('first day of next month'))->toDateString()]
                ]
            )
            ->count();
        $request->session()->put("planed_controls_this_month_count", $planed_controls_this_month_count);

        // late controls
        $late_controls_count=DB::table('controls')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<", Carbon::today()->toDateString()],
                    ]
            )
            ->count();
        $request->session()->put("late_controls_count", $late_controls_count);

        // Count number of action plans
        $action_plans_count=
            count(DB::select("
                select
                    c2.measure_id,
                    c2.id,
                    c2.clause,
                    c2.name,
                    c2.plan_date
                from
                    controls c2,
                    (
                    select max(id) as id
                    from controls
                    where realisation_date is not null
                    group by measure_id
                    ) as c1
                where
                    c1.id = c2.id and
                    (c2.score=1 or c2.score=2);"));

        //dd($action_plans_count);

        $request->session()->put("action_plans_count", $action_plans_count);

        // return 
        return view("welcome")
            ->with('active_domains_count',$active_domains_count)
            ->with('active_controls', $active_controls)
            ->with('domains', $domains)
            ->with('controls_count', $controls_count)
            ->with('active_measures_count', $active_measures_count)
            ->with('controls_made_count', $controls_made_count)
            ->with('controls_never_made', $controls_never_made)

            ->with('controls_todo', $controls_todo)
            ->with('active_controls', $active_controls)            
            ->with('action_plans_count',$action_plans_count)
            ->with('late_controls_count',$late_controls_count);
    }
}
