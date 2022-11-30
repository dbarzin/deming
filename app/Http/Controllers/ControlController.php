<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\ControlsExport;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpWord\TemplateProcessor;
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
        $whereClause ="(true)";
        if ($domain<>0) {
            $whereClause .= "and (c1.domain_id=".$domain.")";
        }
        if ($late<>null) {
            $whereClause .= "and(c1.plan_date<='"
                .Carbon::today()->format("Y-m-d")
                ."')and(c1.realisation_date is null)";            
        }
        else {
            if (($period<>null)&&($period<>99)) {
                $whereClause .= "and(c1.plan_date>='"
                .((new Carbon('first day of this month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')and(c1.plan_date<'"
                .((new Carbon('first day of next month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')";
            }
            if ($status<>null) {
                if ($status=='1') {
                    $whereClause .= "and(c1.realisation_date is not null)";
                }
                if ($status=='2') {
                    $whereClause .= "and(c1.realisation_date is null)";
                }
            }
        }
        $controls=DB::select(
            DB::raw("select
                c1.id,
                c1.measure_id,
                c1.name,
                c1.clause,
                c1.domain_id,
                c1.plan_date,
                c1.realisation_date,
                c1.score as score,
                c2.id as next_id,
                c2.plan_date as next_date
            from
                controls c1 left join controls c2 on c1.next_id=c2.id
            where " . $whereClause .
            " order by c1.id"    
                ));

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

        if ($control->next_id!=null)
            $next_control = DB::table("controls")
               ->select("id","plan_date")
               ->where("id","=",$control->next_id)
               ->get()->first();
        else
            $next_control=null;

        $prev_control = DB::table("controls")
           ->select("id","plan_date")
           ->where("next_id","=",$id)
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
        $documents = DB::table('documents')->where('control_id', $id)->get();

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
        // Get all controls
        $controls = DB::table("controls")
            ->select("id","clause","score","realisation_date","plan_date")
            ->get();

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
                    controls c1 left join controls c2 on c1.next_id=c2.id
                where
                    c2.realisation_date is null and c1.next_id is not null
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
        if ($control==null)
            return;

        $years = [];
        $cur_year = Carbon::now()->year;
        for ($i=0; $i <= 3; $i++) { $years[$i] = $cur_year + $i;
        }

        $months = [];
        for ($month=1; $month <= 12; $month++) { $months[$month] = $month;
        }

        return view("controls.plan", compact("control"))
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
        $control = Control::find($request->id);   

        // Control already made ?
        if ($control->realisation_date!=null) 
            return null;

        $control->plan_date = $request->plan_date;
    	$control->save();

        return redirect("/controls/".$request->id);
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

        // Control already made ?
        if ($control->realisation_date!=null) 
            return null;

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

        // control already made ?
        if ($control->realisation_date!=null)
            return null;

        $control->observations = request("observations");
        $control->plan_date=request("plan_date");
        $control->realisation_date=request("realisation_date");
        $control->note = request("note");
        $control->score = request("score");
        $control->action_plan=request("action_plan");

        // Log::Alert("doMake realisation_date=".request("realisation_date"));


        // if there is no next control
        if ($control->next_id==null) 
        {
            // create a new control
            $new_control = new Control();
            $new_control->measure_id=$control->measure_id;
            $new_control->domain_id=$control->domain_id;
            $new_control->name=$control->name;
            $new_control->clause=$control->clause;
            $new_control->objective = $control->objective;
            $new_control->attributes = $control->attributes;
            $new_control->model = $control->model;
            $new_control->indicator = $control->indicator;

            // should action_plan comes from measure 
            $new_control->action_plan = $control->action_plan; 
            $new_control->owner = $control->owner;
            $new_control->periodicity = $control->periodicity;        
            $new_control->retention = $control->retention;
            $new_control->plan_date = request("next_date");
            $new_control->save();
            // make link
            $control->next_id=$new_control->id;
        }

        // update control
        $control->update();

        return redirect("/");
    }

    /**
     * Save a Control 
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        // Only for CISO
        if (Auth::User()->role!=1)
            return;

        $id = (int) request("id");
        // check
        // plan date is in the futur
        // save control 
        $control = Control::find($id);
        $control->name = request("name");
        $control->objective = request("objective");
        $control->attributes = request("attributes");
        $control->plan_date = request("plan_date");
        $control->realisation_date = request("realisation_date");
        $control->observations = request("observations");
        $control->note = request("note"); 
        $control->score = request("score"); 
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

        // Get template file
        $template_filename = storage_path('app/models/control_.docx');
        if (!file_exists($template_filename))
            $template_filename = storage_path('app/models/control.docx');

        // create templateProcessor
        $templateProcessor = new TemplateProcessor($template_filename);

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
        $filepath=storage_path(
            'templates/control-' . 
            $control->clause . 
            '-' .
            now()->format('Ymd') .
            '.docx');

        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);

        // return response()->make($user->avatar, 200, array(
        //     'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($user->avatar)
        // ));

    }

}

