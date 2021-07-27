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
        $actions=
            DB::select("
                select
                    m2.control_id,
                    m2.id,
                    m2.clause,
                    m2.action_plan,
                    m2.score,
                    m2.name,
                    m2.plan_date,
                    m3.id as next_id,
                    m3.plan_date as next_date
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
                    measurements m3
                where
                    m1.id = m2.id and
                    (m2.score=1 or m2.score=2) and
                    (m3.control_id=m2.control_id and m3.id>m2.id)
                    ;");

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
        $action=DB::table('measurements as m1')
            ->select(
                'm1.id', 
                'm1.control_id', 
                'm1.clause',
                'm1.name', 
                'm1.objective', 
                'm1.observations', 
                'm1.action_plan', 
                'm1.plan_date', 
                'm1.score',
                'm1.realisation_date',
                'm1.score',
                'm2.plan_date as next_date',
                'm2.id as next_id')
            ->leftJoin('measurements as m2', function($join){
                $join->on('m1.id', '<>', 'm2.id');
                $join->on('m1.control_id', '=', 'm2.control_id');
                $join->whereNull('m2.realisation_date');
            })
            ->where('m1.id','=',$id)
            ->first();

        // return            
        return view("actions.show")
            ->with("action", $action);
    }
}
