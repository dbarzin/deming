<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;

use App\Exports\MeasurementsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Control;
use App\Domain;
use App\Measurement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $active_domains_count = DB::table('measurements')
            ->select(
                'domain_id',
                DB::raw('max(measurements.id)'))
            ->whereNull('realisation_date')
            ->groupBy('domain_id')
            ->get()
            ->count();

        // count all controls
        $controls_count = DB::table('controls')            
            ->count();

        // count active controls
        $active_controls_count = DB::table('measurements')
            ->whereNull('realisation_date')
            ->count();

        // count mesurements made
        $measurements_made_count = DB::table('measurements')
            ->whereNotNull('realisation_date')
            ->count();

        // count measurement never made
        $measurements_never_made = DB::select( 
            DB::raw("
                select domain_id 
                from measurements m1 
                where realisation_date is null and 
                not exists (
                    select * 
                    from measurements m2 
                    where realisation_date is not null and m1.control_id=m2.control_id);"
                ));
        //dd($measurements_never_made);

        // Last measurements made by controls
        $active_measurements = DB::select("
                select
                    m2.id,
                    m2.control_id,
                    domains.title, 
                    m2.realisation_date, 
                    m2.score
                from 
                    (
                    select 
                        control_id,
                        max(id) as id
                    from 
                        measurements
                    where
                        realisation_date is not null
                    group by control_id
                    ) as m1,                    
                    measurements m2,
                    domains
                where
                    m1.id=m2.id and domains.id=m2.domain_id
                order by id;");
        //dd($status);

        // get planned controls
        $measurements_todo = DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<",(new Carbon('first day of next month'))->toDateString()]
                ]
            )
            ->get();
        // dd($plannedMeasurements);

        // planed measurements this month
        $planed_measurements_this_month_count = DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date",">=", (new Carbon('first day of this month'))->toDateString()],
                    ["plan_date","<", (new Carbon('first day of next month'))->toDateString()]
                ]
            )
            ->count();
        $request->session()->put("planed_measurements_this_month_count", $planed_measurements_this_month_count);

        // late measurements
        $late_measurements_count=DB::table('measurements')
            ->where(
                [
                    ["realisation_date","=",null],
                    ["plan_date","<", Carbon::today()->toDateString()],
                    ]
            )
            ->count();
        $request->session()->put("late_measurements_count", $late_measurements_count);

        // Count number of action plans
        $action_plans_count=
            count(DB::select("
                select
                    m2.control_id,
                    m2.id,
                    m2.clause,
                    m2.name,
                    m2.plan_date
                from
                    measurements m2,
                    (
                    select max(id) as id
                    from measurements
                    where realisation_date is not null
                    group by control_id
                    ) as m1
                where
                    m1.id = m2.id and
                    (m2.score=1 or m2.score=2);"));

        //dd($action_plans_count);

        $request->session()->put("action_plans_count", $action_plans_count);

        // return 
        return view("welcome")
            ->with('active_domains_count',$active_domains_count)
            ->with('active_measurements', $active_measurements)
            ->with('domains', $domains)
            ->with('controls_count', $controls_count)
            ->with('active_controls_count', $active_controls_count)
            ->with('measurements_made_count', $measurements_made_count)
            ->with('measurements_never_made', $measurements_never_made)

            ->with('measurements_todo', $measurements_todo)
            ->with('active_measurements', $active_measurements)            
            ->with('action_plans_count',$action_plans_count)
            ->with('late_measurements_count',$late_measurements_count);
    }
}
