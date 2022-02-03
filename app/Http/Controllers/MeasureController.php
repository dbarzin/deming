<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\MeasuresExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Measure;
use App\Domain;
use App\Measurement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $measures = Measure::All();
        $domains = Domain::All();

        $domain=$request->get("domain");
        if ($domain<>null) {
            if ($domain=="0") { 
                $request->session()->forget("domain");
                $domain=null;
            }
        }
        else {
            $domain=$request->session()->get("domain");
        }

        if (($domain<>null)) {
            $measures = Measure::where("domain_id", $domain)->get()->sortBy("clause");
            $request->session()->put("domain", $domain);
        }
        else {
            $measures = Measure::All()->sortBy("clause");
        }

        // return
        return view("measures.index")
            ->with("measures", $measures)
            ->with("domains", $domains);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get the list of domains
        $domains = Domain::All();

        //dd($domains);

        // store it in the response 
        return view("measures.create")->with('domains', $domains);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate(
            $request, [
            "domain_id" => "required",
            "clause" => "required|min:3|max:30",
            "name" => "required|min:5",
            "objective" => "required"
            ]
        );
    
        $measure = new Measure();

        $measure->domain_id = request("domain_id");
        $measure->clause = request("clause");
        $measure->name = request("name");
        $measure->objective = request("objective");
        $measure->attributes = request("attributes");
        $measure->model = request("model");
        $measure->indicator = request("indicator");
        $measure->action_plan = request("action_plan");
        $measure->owner = request("owner");
        $measure->periodicity = request("periodicity");
        $measure->retention= request("retention");

        $measure->save();

        $request->session()->put("domain", $measure->domain_id);

        return redirect("/measures");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Measure $measure
     * @return \Illuminate\Http\Response
     */
    public function show(Measure $measure)
    {
        return view("measures.show", compact("measure"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Measure $measure
     * @return \Illuminate\Http\Response
     */
    public function edit(Measure $measure)
    {
        // get the list of domains
        $domains = Domain::All();

        return view("measures.edit", compact("measure"))->with('domains', $domains);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Measure             $measure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Measure $measure)
    {
        $this->validate(
            $request, [
            "domain_id" => "required",
            "clause" => "required|min:3|max:30",
            "name" => "required|min:5",
            "objective" => "required"
            ]
        );

        // update measure
        $measure->domain_id = request("domain_id");
        $measure->name = request("name");
        $measure->clause = request("clause");
        $measure->objective = request("objective");
        $measure->attributes = request("attributes");
        $measure->model = request("model");
        $measure->indicator = request("indicator");
        $measure->action_plan = request("action_plan");
        $measure->owner = request("owner");
        $measure->periodicity = request("periodicity");
        $measure->retention = request("retention");
        $measure->save();

        // update the open measure
        $measure=Measurement::where('measure_id', $measure->id)
                            ->where('realisation_date', null)
                            ->get()->first();
        if ($measure<>null) {
            $measure->clause = $measure->clause;
            $measure->name = $measure->name;
            $measure->objective = $measure->objective;
            $measure->attributes = $measure->attributes;
            $measure->model = $measure->model;
            $measure->indicator = $measure->indicator;
            $measure->action_plan = $measure->action_plan;
            $measure->periodicity = $measure->periodicity;
            $measure->periodicity = $measure->retention;
            $measure->save();
        }

        // retun to view measure
        return redirect("/measures/".$measure->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(Measure $measure)
    {
        //
        $measure->delete();
        return redirect("/measures");
    }


    /**
     * Activate a measure
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {
        // dd($request);
        $measure = Measure::find($request->id);

        // create the correspodign meaurement        
        $measure = new Measurement();
        $measure->measure_id=$measure->id;
        $measure->domain_id=$measure->domain_id;
        $measure->name=$measure->name;
        $measure->clause=$measure->clause;
        $measure->objective = $measure->objective;
        $measure->attributes = $measure->attributes;
        $measure->model = $measure->model;
        $measure->indicator = $measure->indicator;
        $measure->action_plan = $measure->action_plan;
        $measure->owner = $measure->owner;
        $measure->periodicity = $measure->periodicity;
        $measure->retention = $measure->retention;
        $measure->plan_date = Carbon::now()->endOfMonth();

        $measure->save();

        // return to the list of measures
        return redirect("/measures");
    }


    /**
     * Disable a measure
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {

        DB::table('measures')
            ->where('measure_id', '=', $request->id)
            ->whereNull('realisation_date')
            ->delete();

        // return to the list of measures
        return redirect("/measures");
    }

    public function export() 
    {
        return Excel::download(new MeasuresExport, 'measures.xlsx');
    }    

}
