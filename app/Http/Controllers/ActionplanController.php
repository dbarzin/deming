<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\ControlsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Control;
use App\Domain;
use App\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionplanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $actions=DB::select(
            DB::raw(
                "
                SELECT
                m1.id as id, 
                m1.control_id as control_id, 
                m1.clause as clause,
                m1.name as name, 
                m1.plan_date as plan_date, 
                m1.score as score,
                m1.realisation_date as realisation_date,
                max(m1.realisation_date) as realisation_date, 
                m1.score as score,
                m2.plan_date as next_date,
                m2.id as next_id
                FROM measurements m1
                LEFT JOIN measurements m2 on ( 
                    m1.id<>m2.id and m1.control_id = m2.control_id and m2.realisation_date is null)
                WHERE (m1.score=1 or m1.score=2)
                GROUP BY control_id order by plan_date"
            )
        );

        // return
        return view("actions.index")
            ->with("actions", $actions);
    }

    /**
     * Save a Action plan
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $id = (int) request("id");

        // save measurement
        $measurement = Measurement::find($id);
        $measurement->action_plan = request("action_plan");
        $measurement->plan_date=request("plan_date");
        $measurement-> update();

        return redirect("/actions");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        // measurement where score==null and control_id=$control_id
        $action=DB::select(
            DB::raw(
                "
                SELECT
                m1.id as id, 
                m1.control_id as control_id, 
                m1.clause as clause,
                m1.name as name, 
                m1.objective as objective, 
                m1.observations as observations, 
                m1.action_plan as action_plan, 
                m1.plan_date as plan_date, 
                m1.score as score,
                m1.realisation_date as realisation_date,
                m1.score as score,
                m2.plan_date as next_date,
                m2.id as next_id
                FROM measurements m1
                LEFT JOIN measurements m2 on ( 
                    m1.id<>m2.id and m1.control_id = m2.control_id and m2.realisation_date is null)
                WHERE (m1.id=".$id.")")
        )[0];

        // dd($action);
        return view("actions.show")
            ->with("action", $action);
    }
}
