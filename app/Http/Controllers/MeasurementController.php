<?php

namespace App\Http\Controllers;

Use \Carbon\Carbon;

use App\Exports\MeasurementsExport;
use Maatwebsite\Excel\Facades\Excel;

use PhpOffice\PhpWord\Shared\Converter;

use App\Measurement;
use App\Domain;
use App\Control;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MeasurementController extends Controller
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

        // get all late measurements
        $late=$request->get("late");
        if ($late<>null) {
            $request->session()->put("status", "2");
        }

        // select
        $params = array();
        $whereClause ="(true)";
        if ($domain<>0) {
            $whereClause .= "and (m1.domain_id=".$domain.")";
        }
        if ($late<>null) {
            $whereClause .= "and(m1.plan_date<='"
                .Carbon::today()->format("Y-m-d")
                ."')and(m1.realisation_date is null)";            
            
        }
        else {
            if (($period<>null)&&($period<>99)) {
                $whereClause .= "and(m1.plan_date>='"
                .((new Carbon('first day of this month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')and(m1.plan_date<'"
                .((new Carbon('first day of next month'))->addMonth((int)$period)->format("Y-m-d"))
                ."')";
            }
            if ($status<>null) {
                if ($status=='1') {
                    $whereClause .= "and(m1.realisation_date is not null)";
                }
                if ($status=='2') {
                    $whereClause .= "and(m1.realisation_date is null)";
                }
            }
        }

        // select
        $measurements=DB::select(
            DB::raw("
                SELECT
                m1.id as id,
                m1.control_id as control_id,
                m1.name as name,
                m1.clause as clause,
                m1.domain_id as domain_id,
                m1.plan_date as plan_date,
                m1.realisation_date,
                m1.score as score,
                m3.next_date,
                m3.next_id
                FROM measurements m1
                LEFT OUTER JOIN (
                    select
                        m2.control_id,
                        min(m2.id) as next_id,
                        m2.plan_date as next_date
                        from measurements m2
                        group by control_id
                    ) as m3 on (m1.control_id=m3.control_id and m3.next_id>m1.id)
                WHERE " 
                . $whereClause));

        // view
        return view("measurements.index")
            ->with("measurements", $measurements)
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
        return redirect("/measurements");
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
        return redirect("/measurements");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $measurement = Measurement::find($id);

        $next_measurement = DB::table("measurements")
           ->select(\DB::raw("MIN(id) as id"),"plan_date")
           ->where("control_id","=",$measurement->control_id)
           ->where("id",">",$measurement->id)
           ->get()->first();

        $prev_measurement = DB::table("measurements")
           ->select(\DB::raw("MAX(id) as id"),"plan_date")
           ->where("control_id","=",$measurement->control_id)
           ->where("id","<",$measurement->id)
           ->get()->first();

        // get associated documents
        $documents = DB::table('documents')->where('measurement_id', $id)->get();

        return view("measurements.show")
            ->with("measurement", $measurement)
            ->with("next_id", $next_measurement!=null ? $next_measurement->id : null)
            ->with("next_date", $next_measurement!=null ? $next_measurement->plan_date : null)
            ->with("prev_id", $prev_measurement!=null ? $prev_measurement->id : null)
            ->with("prev_date", $prev_measurement!=null ? $prev_measurement->plan_date : null)
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
            
        $measurement = Measurement::find($id);
        // get associated documents
        $documents = DB::table('documents')->where('measurement_id', $id)->get();

        // save measurement_id in session for document upload
        $request->session()->put("measurement", $id);

        // return view
        //return view("measurements.make")

        return view("measurements.edit")
            ->with("measurement", $measurement)
            ->with("documents", $documents);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Measurement $measurement)
    {
        $measurement->save();
        return redirect("/measurements");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Measurement $measurement)
    {
        // does not exists in that way
        $measurement->delete();
        return redirect("/measurements");
    }

    public function history(Request $request)
    {
        // get measurements
        $measurements = Measurement::All();

        // return
        return view("measurements.history")
            ->with("measurements", $measurements);
    }

    public function radar(Request $request)
    {
        // get measurements

        // get all doains
        $domains = Domain::All();
        
        $cur_date=$request->get("cur_date");
        if ($cur_date==null) {
            $cur_date=\Carbon\Carbon::now()->format('Y-m-d');
        } else {
            // Avoid SQL Injection
            $cur_date=\Carbon\Carbon::createFromFormat('Y-m-d', $cur_date)->format('Y-m-d');
        }

        $measurements=DB::select(
            DB::raw("
                SELECT
                m1.id as measurement_id,
                m1.name as name,
                m1.clause as clause,
                m1.control_id as control_id,
                m1.domain_id as domain_id,
                m1.plan_date as plan_date,
                max(m1.realisation_date) as realisation_date, 
                m1.score as score,
                m2.plan_date as next_date,
                m2.id as next_id
                FROM measurements m1
                LEFT JOIN measurements m2 on (
                    m1.control_id = m2.control_id and m2.id > m1.id and m2.realisation_date is null)
                WHERE m1.realisation_date <= '" 
                . $cur_date
                ."' group by control_id order by clause"
            )
        );

        // return
        return view("measurements.radar")
            ->with("measurements", $measurements)
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
        // $measurement->delete();
        $measurement = Measurement::find($id);

        $years = [];
        $cur_year = Carbon::now()->year;
        for ($i=0; $i <= 3; $i++) { $years[$i] = $cur_year + $i;
        }

        $months = [];
        for ($month=1; $month <= 12; $month++) { $months[$month] = $month;
        }

        return view("measurements.plan", compact("measurement"))
            ->with("years", $years)
            ->with("day", date('d', strtotime($measurement->plan_date)))
            ->with("month", date('m', strtotime($measurement->plan_date)))
            ->with("year", date('Y', strtotime($measurement->plan_date)));
    }


    /**
     * Save a measurement for planing
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function doPlan(Request $request)
    {
        // TODO : remove this function
        $id = (int) request("id");
        // $month = (int) request("month");
        // $year = (int) request("year");
        // TODO: check not date in the past
        // save
        $measurement = Measurement::find($id);
        $measurement->plan_date = request("plan_date"); 
        $measurement-> update();

        return redirect("/measurements");
    }


    public function make(Request $request)
    {
        // Not for aditor
        if (Auth::User()->role==3)
            return;

        $id = (int) request("id");

        // does not exists in that way
        $measurement = Measurement::find($id);
        if ($measurement==null) {
            Log::Error("Measurement:make - Measurement not found  ". request("id"));
            return null;
        }

        // get associated documents
        $documents = DB::table('documents')->where('measurement_id', $id)->get();

        // save measurement_id in session for document upload
        $request->session()->put("measurement", $id);

        // return view
        return view("measurements.make")
            ->with("measurement", $measurement)
            ->with("documents", $documents);
    }


    /**
     * Save a Measurement
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
        // save measurement
        $measurement = Measurement::find($id);
        // $measurement->name = request("name");
        // $measurement->objective = request("objective");
        // $measurement->attributes = request("attributes");
        $measurement->observations = request("observations");
        $measurement->note = request("note");
        //$measurement->score = request("score");
        $measurement->plan_date=request("plan_date");
        $measurement->action_plan=request("action_plan");
        //$measurement->realisation_date=request("realisation_date");
        $measurement-> update();
        return redirect("/measurement/show/".$id);
    }


    /**
     * Do a Measurement
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

        // Measurement fields
        $measurement = Measurement::find($id);
        $measurement->observations = request("observations");
        $measurement->plan_date=request("plan_date");
        $measurement->realisation_date=request("realisation_date");
        $measurement->note = request("note");
        $measurement->score = request("score");
        $measurement->action_plan=request("action_plan");

        // Log::Alert("doMake realisation_date=".request("realisation_date"));

        // update measurement
        $measurement->update();

        // if there is no next measurement
        if (
            DB::table('measurements')
            ->where('clause','=',$measurement->clause)
            ->where('realisation_date','=',null)
            ->count()==0) {

            Log::Alert("create a new measurement");            
            // create a new measurement
            $new_measurement = new Measurement();
            $new_measurement->control_id=$measurement->control_id;
            $new_measurement->domain_id=$measurement->domain_id;
            $new_measurement->name=$measurement->name;
            $new_measurement->clause=$measurement->clause;
            $new_measurement->objective = $measurement->objective;
            $new_measurement->attributes = $measurement->attributes;
            $new_measurement->model = $measurement->model;
            $new_measurement->indicator = $measurement->indicator;
            // should action_plan comes from control ?
            $new_measurement->action_plan = $measurement->action_plan; 
            $new_measurement->owner = $measurement->owner;
            $new_measurement->periodicity = $measurement->periodicity;        
            $new_measurement->retention = $measurement->retention;
            $new_measurement->plan_date = request("next_date");
            $new_measurement->save();
        }

        // Log::Alert("doMake Done.");
        return redirect("/measurements");
    }

    public function upload(Request $request) 
    {
        return null;
    }    

    public function export() 
    {
        return Excel::download(new MeasurementsExport, 'measurements.xlsx');
    }    


    public function template(Request $request)
    {
        $id = (int) request("id");

        // find associate measurement
        $measurement = Measurement::find($id);

        // get template
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(
            storage_path('app/models/control.docx')
        );

        // make changes xxxx
        $templateProcessor->setValue('ref', $measurement->clause);
        $templateProcessor->setValue('name', $measurement->name);
        $templateProcessor->setValue(
            'objective', 
            strtr(
                $measurement->objective, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue(
            'attributes', 
            strtr(
                $measurement->attributes, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue(
            'model', 
            strtr(
                $measurement->model, 
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue('date', Carbon::today()->format("d/m/Y"));

        // save a copy
        $filepath=storage_path('templates/control-'.$measurement->clause.'.docx');
        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);

        // return response()->make($user->avatar, 200, array(
        //     'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($user->avatar)
        // ));

    }


}
