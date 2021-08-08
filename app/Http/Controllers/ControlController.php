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
use Illuminate\Support\Facades\Log;

class ControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $controls = Control::All();
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
            $controls = Control::where("domain_id", $domain)->get()->sortBy("clause");
            $request->session()->put("domain", $domain);
        }
        else {
            $controls = Control::All()->sortBy("clause");
        }

        // return
        return view("controls.index")
            ->with("controls", $controls)
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
        return view("controls.create")->with('domains', $domains);;
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
    
        $control = new Control();

        $control->domain_id = request("domain_id");
        $control->clause = request("clause");
        $control->name = request("name");
        $control->objective = request("objective");
        $control->attributes = request("attributes");
        $control->model = request("model");
        $control->indicator = request("indicator");
        $control->action_plan = request("action_plan");
        $control->owner = request("owner");
        $control->periodicity = request("periodicity");
        $control->retention= request("retention");

        $control-> save();

        $request->session()->put("domain", $control->domain_id);

        return redirect("/controls");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Control $control
     * @return \Illuminate\Http\Response
     */
    public function show(Control $control)
    {
        return view("controls.show", compact("control"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Control $control
     * @return \Illuminate\Http\Response
     */
    public function edit(Control $control)
    {
        // get the list of domains
        $domains = Domain::All();

        return view("controls.edit", compact("control"))->with('domains', $domains);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Control             $control
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Control $control)
    {
        $this->validate(
            $request, [
            "domain_id" => "required",
            "clause" => "required|min:3|max:30",
            "name" => "required|min:5",
            "objective" => "required"
            ]
        );

        // update control
        $control->domain_id = request("domain_id");
        $control->name = request("name");
        $control->clause = request("clause");
        $control->objective = request("objective");
        $control->attributes = request("attributes");
        $control->model = request("model");
        $control->indicator = request("indicator");
        $control->action_plan = request("action_plan");
        $control->owner = request("owner");
        $control->periodicity = request("periodicity");
        $control->retention = request("retention");
        $control->save();

        // update the open measurement
        $measurement=Measurement::where('control_id', $control->id)
                            ->where('realisation_date', null)
                            ->get()->first();
        if ($measurement<>null) {
            $measurement->clause = $control->clause;
            $measurement->name = $control->name;
            $measurement->objective = $control->objective;
            $measurement->attributes = $control->attributes;
            $measurement->model = $control->model;
            $measurement->indicator = $control->indicator;
            $measurement->action_plan = $control->action_plan;
            $measurement->periodicity = $control->periodicity;
            $measurement->periodicity = $control->retention;
            $measurement->save();
        }

        // retun to view control
        return redirect("/controls/".$control->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(Control $control)
    {
        //
        $control->delete();
        return redirect("/controls");
    }


    /**
     * Activate a control
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {
        // dd($request);
        $control = Control::find($request->id);

        // create the correspodign meaurement        
        $measurement = new Measurement();
        $measurement->control_id=$control->id;
        $measurement->domain_id=$control->domain_id;
        $measurement->name=$control->name;
        $measurement->clause=$control->clause;
        $measurement->objective = $control->objective;
        $measurement->attributes = $control->attributes;
        $measurement->model = $control->model;
        $measurement->indicator = $control->indicator;
        $measurement->action_plan = $control->action_plan;
        $measurement->owner = $control->owner;
        $measurement->periodicity = $control->periodicity;
        $measurement->retention = $control->retention;
        $measurement->plan_date = Carbon::now()->endOfMonth();

        $measurement->save();

        // return to the list of controls
        return redirect("/controls");
    }


    /**
     * Disable a control
     *
     * @param  \App\c $c
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {

        DB::table('measurements')
            ->where('control_id', '=', $request->id)
            ->whereNull('realisation_date')
            ->delete();

        // return to the list of controls
        return redirect("/controls");
    }

    public function export() 
    {
        return Excel::download(new ControlsExport, 'controls.xlsx');
    }    

}
