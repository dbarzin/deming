<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\ControlsExport;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpWord\Shared\Converter;

use App\Measure;
use App\Domain;
use App\Control;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get all doains
        $domains = Domain::All();

        // get domain base on his title
        $domain_title=$request->get("domain_title");
        if ($domain_title<>null) {
            $domain=Domain::where("title","=",$domain_title)->get();
            if ($domain<>null) {
                $domain = $domain->first()->id;
                $request->session()->put("domain", $domain);
            }
        }

        // get current domain
        $domain=$request->get("domain");
        if ($domain<>null) {
            $domin=intval($domain);
            if ($domain==0) { 
                $request->session()->forget("domain");
            }
            else {
                $request->session()->put("domain", $domain);
            }
        }
        else {
            $domain=$request->session()->get("domain");
        }

        // get current period
        $period=$request->get("period");
        if ($period<>null) {
            if ($period=="99") { 
                $request->session()->put("period", $period);
                $period=null;
            }
            else
                $request->session()->put("period", $period);
        }
        else {
            $period=$request->session()->get("period");
        }

        // get status
        $status=$request->get("status");
        if ($status<>null) { 
            $request->session()->put("status", $status);
        } else {
            $status=$request->session()->get("status");
        }

        // get all late control
        $late=$request->get("late");
        if ($late<>null) {
            $request->session()->put("status", "2");
        }

        // select
        $params = array();
        $whereClause ="(true)";
        if ($domain<>0) {
            $whereClause .= "and (domain_id=".$domain.")";
        }
        if ($late<>null) {
            $whereClause .= "and(plan_date<='"
                .Carbon::today()->format("Y-m-d")
                ."')and(realisation_date is null)";            
            
        }
        else {
            if (($period<>null)&&($period<>99)) {
                $whereClause .= "and(plan_date>='"
                .((new Carbon('first day of this month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')and(plan_date<'"
                .((new Carbon('first day of next month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')";
            }
            if ($status<>null) {
                if ($status=='1') {
                    $whereClause .= "and(realisation_date is not null)";
                }
                if ($status=='2') {
                    $whereClause .= "and(realisation_date is null)";
                }
            }
        }

        // Select
        if($status!='0') {
            $controls=DB::select(
                DB::raw("select
                    m1.id,
                    m1.measure_id,
                    m1.clause,
                    m1.name,
                    m1.domain_id,
                    domains.title, 
                    m1.plan_date,
                    m1.realisation_date,
                    m1.score as score,
                    m1.realisation_date, 
                    m1.score,
                    (
                        select max(m3.plan_date)
                        from controls m3
                        where m3.id>m1.id and m1.measure_id=m3.measure_id
                    ) as next_date,
                    (
                        select max(m3.id)
                        from controls m3
                        where m3.id>m1.id and m1.measure_id=m3.measure_id
                    ) as next_id
                from 
                    (
                    select measure_id, max(id) as id
                    from controls
                    where ". $whereClause . " group by measure_id
                    ) as m2,
                    controls m1,
                    domains
                where
                    m1.id=m2.id and domains.id=m1.domain_id
                order by m1.id;"));
        }
        else
        {
            $controls=DB::select(
                DB::raw("select
                    m1.id as id,
                    m1.measure_id as measure_id,
                    m1.name as name,
                    m1.clause as clause,
                    m1.domain_id as domain_id,
                    m1.plan_date as plan_date,
                    m1.realisation_date,
                    m1.score as score,
                    (
                        select max(m3.plan_date)
                        from controls m3
                        where m3.id>m1.id and m1.measure_id=m3.measure_id
                    ) as next_date,
                    (
                        select max(m3.id)
                        from controls m3
                        where m3.id>m1.id and m1.measure_id=m3.measure_id
                    ) as next_id
                from 
                    (
                    select measure_id, max(id) as id
                    from controls
                    where realisation_date is not null and ". $whereClause . " group by measure_id
                    ) as m2,
                    controls m1,
                    domains
                where
                    m1.id=m2.id and domains.id=m1.domain_id 
                UNION SELECT 
                    m1.id as id,
                    m1.measure_id as measure_id,
                    m1.name as name,
                    m1.clause as clause,
                    m1.domain_id as domain_id,
                    m1.plan_date as plan_date,
                    m1.realisation_date,
                    m1.score as score,
                    null as next_date,
                    null as next_id                    
                    FROM controls m1 
                    WHERE realisation_date is null and ". $whereClause .
                    " and not exists (
                        select * 
                        from controls m2 
                        where realisation_date is not null and m1.measure_id=m2.measure_id);"
                    ));
            }

        // view
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
        // does not exists in that way
        return redirect("/control");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // does not exist in that way
        return redirect("/control");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $control = Control::find($id);

        $next_control = DB::table("controls")
           ->select(\DB::raw("MIN(id) as id"),"plan_date")
           ->where("measure_id","=",$control->measure_id)
           ->where("id",">",$control->id)
           ->get()->first();

        $prev_control = DB::table("controls")
           ->select(\DB::raw("MAX(id) as id"),"plan_date")
           ->where("measure_id","=",$control->measure_id)
           ->where("id","<",$control->id)
           ->get()->first();

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        return view("controls.show")
            ->with("control", $control)
            ->with("next_id", $next_control!=null ? $next_control->id : null)
            ->with("next_date", $next_control!=null ? $next_control->plan_date : null)
            ->with("prev_id", $prev_control!=null ? $prev_control->id : null)
            ->with("prev_date", $prev_control!=null ? $prev_control->plan_date : null)
            ->with("documents", $documents);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    // public function edit(int $id)
    public function edit(Request $request)
    {
        $id = (int) request("id");
            
        $control = Control::find($id);
        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // save control_id in session for document upload
        $request->session()->put("measurement", $id);

        // return view
        //return view("control.make")

        return view("controls.edit")
            ->with("control", $control)
            ->with("documents", $documents);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Control $control)
    {
        $control->save();
        return redirect("/control");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Control $control)
    {
        // does not exists in that way
        $control->delete();
        return redirect("/control");
    }

    public function history(Request $request)
    {
        // get control
        $controls = Control::All();

        // return
        return view("controls.history")
            ->with("controls", $controls);
    }

    public function radar(Request $request)
    {
        // get control

        // get all doains
        $domains = Domain::All();
        
        $cur_date=$request->get("cur_date");
        if ($cur_date==null) {
            $cur_date=today()->format('Y-m-d');
        } else {
            // Avoid SQL Injection
            $cur_date=\Carbon\Carbon::createFromFormat('Y-m-d', $cur_date)->format('Y-m-d');
        }

        $controls=DB::select(
            DB::raw("
                select
                c1.id as control_id,
                c1.name as name,
                c1.clause as clause,
                c1.measure_id as measure_id,
                c1.domain_id as domain_id,
                c1.plan_date as plan_date,
                c1.realisation_date as realisation_date, 
                c1.score as score,
                c2.plan_date as next_date,
                c2.id as next_id
                from
                    controls c1
                    LEFT JOIN controls c2 on (
                        c1.measure_id = c2.measure_id and c2.id > c1.id and c2.realisation_date is null),
                    (
                    select max(id) as id
                    from controls
                    where realisation_date <= '" . $cur_date . "'
                    group by measure_id
                    ) as c3
                where
                    c1.id = c3.id 
                group by measure_id order by clause;"
                )
            );

        // return
        return view("controls.radar")
            ->with("controls", $controls)
            ->with("cur_date", $cur_date)
            ->with("domains", $domains);
    }

    /**
     * Show a measurement for planing
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function plan(int $id)
    {
        // does not exists in that way
        // $control->delete();
        $control = Control::find($id);

        $years = [];
        $cur_year = Carbon::now()->year;
        for ($i=0; $i <= 3; $i++) { $years[$i] = $cur_year + $i;
        }

        $months = [];
        for ($month=1; $month <= 12; $month++) { $months[$month] = $month;
        }

        return view("control.plan", compact("measurement"))
            ->with("years", $years)
            ->with("day", date('d', strtotime($control->plan_date)))
            ->with("month", date('m', strtotime($control->plan_date)))
            ->with("year", date('Y', strtotime($control->plan_date)));
    }


    /**
     * Save a control for planing
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function doPlan(Request $request)
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


    /*
    */

    public function make(Request $request)
    {
        // Not for aditor
        if (Auth::User()->role==3)
            return;

        $id = (int) request("id");

        // does not exists in that way
        $control = Control::find($id);
        if ($control==null) {
            Log::Error("Control:make - Control not found  ". request("id"));
            return null;
        }

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // save control_id in session for document upload
        $request->session()->put("control", $id);

        // return view
        return view("controls.make")
            ->with("control", $control)
            ->with("documents", $documents);
    }

    /**
     * Do a Control
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function doMake(Request $request)
    {
        // Log::Alert("doMake START");
        $id = (int) request("id");
        // Log::Alert("doMake id=".$id);
        // dd($request);

        // check : 
        // plan date not in the past
        if (request("score")==null) {
            return back()
            ->withErrors(['score' => 'score not set'])
            ->withInput();
        }            

        // Control fields
        $control = Control::find($id);
        $control->observations = request("observations");
        $control->plan_date=request("plan_date");
        $control->realisation_date=request("realisation_date");
        $control->note = request("note");
        $control->score = request("score");
        $control->action_plan=request("action_plan");

        // Log::Alert("doMake realisation_date=".request("realisation_date"));

        // update measurement
        $control->update();

        // if there is no next measurement
        if (
            DB::table('controls')
            ->where('clause','=',$control->clause)
            ->where('realisation_date','=',null)
            ->count()==0) {

            Log::Alert("create a new measurement");            
            // create a new measurement
            $new_control = new Control();
            $new_control->measure_id=$control->measure_id;
            $new_control->domain_id=$control->domain_id;
            $new_control->name=$control->name;
            $new_control->clause=$control->clause;
            $new_control->objective = $control->objective;
            $new_control->attributes = $control->attributes;
            $new_control->model = $control->model;
            $new_control->indicator = $control->indicator;
            // should action_plan comes from measure ?
            $new_control->action_plan = $control->action_plan; 
            $new_control->owner = $control->owner;
            $new_control->periodicity = $control->periodicity;        
            $new_control->retention = $control->retention;
            $new_control->plan_date = request("next_date");
            $new_control->save();
        }

        // Log::Alert("doMake Done.");
        return redirect("/controls");
    }

    /**
     * Save a Control 
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        //Only for CISO
        if (Auth::User()->role!=1)
            return;

        $id = (int) request("id");
        // check
        // plan date is in the futur
        // save control 
        $control = Control::find($id);
        $control->observations = request("observations");
        $control->note = request("note"); 
        $control->plan_date=request("plan_date");
	$control->action_plan=request("action_plan");

        $control-> update();
        return redirect("/control/show/".$id);
    }

    public function upload(Request $request) 
    {
        return null;
    }    

    public function export() 
    {
        return Excel::download(new ControlsExport, 'control.xlsx');
    }    


    public function template(Request $request)
    {
        $id = (int) request("id");

        // find associate measurement
        $control = Control::find($id);

        // get template
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(
            storage_path('app/models/control.docx')
        );

        // make changes xxxx
        $templateProcessor->setValue('ref', $control->clause);
        $templateProcessor->setValue('name', $control->name);
        $templateProcessor->setValue(
            'objective', 
            strtr(
                $control->objective, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue(
            'attributes', 
            strtr(
                $control->attributes, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue(
            'model', 
            strtr(
                $control->model, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue('date', Carbon::today()->format("d/m/Y"));

        // save a copy
        $filepath=storage_path('templates/measure-'.$control->clause.'.docx');
        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);

        // return response()->make($user->avatar, 200, array(
        //     'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($user->avatar)
        // ));

    }

}

