<?php

namespace App\Http\Controllers;

use App\Control;
use App\Domain;
use App\Exports\ControlsExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

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

        // get all attributes
        $attributes = [];
        $allAttributes = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($allAttributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($attributes, $value);
                }
            }
        }
        sort($attributes);

        // get domain base on his title
        $domain_title = $request->get('domain_title');
        if ($domain_title !== null) {
            $domain = Domain::where('title', '=', $domain_title)->get();
            if ($domain !== null) {
                $domain = $domain->first()->id;
                $request->session()->put('domain', $domain);
            }
        }

        // Domain filter
        $domain = $request->get('domain');
        if ($domain !== null) {
            $domain = intval($domain);
            if ($domain === 0) {
                $request->session()->forget('domain');
            } else {
                $request->session()->put('domain', $domain);
            }
        } else {
            $domain = $request->session()->get('domain');
        }

        // Attribute filter
        $attribute = $request->get('attribute');
        if ($attribute !== null) {
            if ($attribute === 'none') {
                $request->session()->forget('attribute');
                $attribute = null;
            } else {
                $request->session()->put('attribute', $attribute);
            }
        } else {
            $attribute = $request->session()->get('attribute');
        }

        // Period filter
        $period = $request->get('period');
        if ($period !== null) {
            $period = intval($period);
            if ($period === 99) {
                $request->session()->put('period', $period);
            } else {
                $request->session()->put('period', $period);
            }
        } else {
            $period = $request->session()->get('period');
        }

        // Status filter
        $status = $request->get('status');
        if ($status !== null) {
            $request->session()->put('status', $status);
        } else {
            $status = $request->session()->get('status');
        }

        // Late filter
        $late = $request->get('late');
        if ($late !== null) {
            $request->session()->put('status', '2');
            $status = '2';
        }

        // Build query
        $controls = DB::table('controls as c1')
            ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id');

        // Filter on domain
        if (($domain !== null) && ($domain !== 0)) {
            $controls = $controls->where('c1.domain_id', '=', $domain);
        }

        // Filter on period
        if (($period !== null) && ($period !== 99)) {
            $controls = $controls
                ->where('c1.plan_date', '>=', (new Carbon('first day of this month'))->addMonth($period)->format('Y-m-d'))
                ->where('c1.plan_date', '<', (new Carbon('first day of next month'))->addMonth($period)->format('Y-m-d'));
        }

        // Filter on status
        if ($late !== null) {
            $controls = $controls
                ->where('c1.plan_date', '<=', Carbon::today()->format('Y-m-d'))
                ->whereNull('c1.realisation_date');
        } elseif ($status === '1') {
            $controls = $controls->whereNotNull('c1.realisation_date');
        } elseif ($status === '2') {
            $controls = $controls->whereNull('c1.realisation_date');
        }

        // Filter on attribute
        if ($attribute !== null) {
            $controls = $controls->where('c1.attributes', 'LIKE', '%'.$attribute.'%');
        }

        // Query DB
        $controls = $controls->select(
            [
                'c1.id',
                'c1.measure_id',
                'c1.name',
                'c1.clause',
                'c1.domain_id',
                'c1.plan_date',
                'c1.realisation_date',
                'c1.score as score',
                'c2.id as next_id',
                'c2.plan_date as next_date',
            ]
        )
            ->orderBy('c1.id')->get();

        // return view
        return view('controls.index')
            ->with('controls', $controls)
            ->with('attributes', $attributes)
            ->with('domains', $domains);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // does not exists in that way
        return redirect('/control');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // does not exist in that way
        return redirect('/control');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $control = Control::find($id);

        if ($control->next_id !== null) {
            $next_control = DB::table('controls')
                ->select('id', 'plan_date')
                ->where('id', '=', $control->next_id)
                ->get()->first();
        } else {
            $next_control = null;
        }

        $prev_control = DB::table('controls')
            ->select('id', 'plan_date')
            ->where('next_id', '=', $id)
            ->get()->first();

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        return view('controls.show')
            ->with('control', $control)
            ->with('next_id', $next_control !== null ? $next_control->id : null)
            ->with('next_date', $next_control !== null ? $next_control->plan_date : null)
            ->with('prev_id', $prev_control !== null ? $prev_control->id : null)
            ->with('prev_date', $prev_control !== null ? $prev_control->plan_date : null)
            ->with('documents', $documents);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    // public function edit(int $id)
    public function edit(Request $request)
    {
        $id = intval($request->id);

        $control = Control::find($id);
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // get all attributes
        $values = [];
        $attributes = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                array_push($values, $value);
            }
        }
        sort($values);

        return view('controls.edit')
            ->with('control', $control)
            ->with('documents', $documents)
            ->with('attributes', $values);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Control $control)
    {
        // does not exists in that way
        $control->delete();
        return redirect('/control');
    }

    public function history()
    {
        // Get all controls
        $controls = DB::table('controls')
            ->select('id', 'clause', 'score', 'realisation_date', 'plan_date')
            ->get();

        // return
        return view('controls.history')
            ->with('controls', $controls);
    }

    public function domains()
    {
        // get all domains
        $domains = DB::table('domains')->get();

        // count control never made
        $controls_never_made = DB::select(
            DB::raw(
                '
                select domain_id 
                from controls c1 
                where realisation_date is null and 
                not exists (
                    select * 
                    from controls c2 
                    where realisation_date is not null and c1.measure_id=c2.measure_id);'
            )
        );

        // Last controls made by measures
        $active_controls = DB::select('
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
                order by id;');

        // return
        return view('/radar/domains')
            ->with('domains', $domains)
            ->with('active_controls', $active_controls)
            ->with('controls_never_made', $controls_never_made);
    }

    public function measures(Request $request)
    {
        // get all doains
        $domains = Domain::All();

        $cur_date = $request->get('cur_date');
        if ($cur_date === null) {
            $cur_date = today()->format('Y-m-d');
        } else {
            // Avoid SQL Injection
            $cur_date = \Carbon\Carbon::createFromFormat('Y-m-d', $cur_date)->format('Y-m-d');
        }

        // Build query
        $controls = DB::table('controls as c1')
            ->select(
                [
                    'c1.id as control_id',
                    'c1.name as name',
                    'c1.clause as clause',
                    'c1.measure_id as measure_id',
                    'c1.domain_id as domain_id',
                    'c1.plan_date as plan_date',
                    'c1.realisation_date as realisation_date',
                    'c1.score as score',
                    'c2.plan_date as next_date',
                    'c2.id as next_id',
                ]
            )
            ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->where('c2.realisation_date', '=', null)
            ->where('c1.next_id', '<>', null)
            ->where('c1.realisation_date', '<=', $cur_date)
            ->groupBy('measure_id')
            ->orderBy('clause')
            ->get();

        // return
        return view('radar.controls')
            ->with('controls', $controls)
            ->with('cur_date', $cur_date)
            ->with('domains', $domains);
    }

    public function attributes(Request $request)
    {
        // get all attributes
        $attributes = DB::table('attributes')
            ->orderBy('name')
            ->get();

        // Controls made
        $controls = DB::select('
                select
                    c2.id,
                    c2.name,
                    c2.attributes,
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
                    controls c2
                where
                    c1.id=c2.id
                order by id;');

        // return
        return view('radar.attributes')
            ->with('attributes', $attributes)
            ->with('controls', $controls);
    }

    /**
     * Show a measurement for planing
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function plan(int $id)
    {
        // does not exists in that way
        // $control->delete();
        $control = Control::find($id);
        if ($control === null) {
            return;
        }

        $years = [];
        $cur_year = Carbon::now()->year;
        for ($i = 0; $i <= 3; $i++) {
            $years[$i] = $cur_year + $i;
        }

        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = $month;
        }

        return view('controls.plan', compact('control'))
            ->with('years', $years)
            ->with('day', date('d', strtotime($control->plan_date)))
            ->with('month', date('m', strtotime($control->plan_date)))
            ->with('year', date('Y', strtotime($control->plan_date)));
    }

    /**
     * Save a control for planing
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function doPlan(Request $request)
    {
        $control = Control::find($request->id);

        // Control already made ?
        if ($control->realisation_date !== null) {
            return null;
        }

        $control->plan_date = $request->plan_date;
        $control->save();

        return redirect('/controls/'.$request->id);
    }

    public function make(Request $request)
    {
        // Not for aditor
        abort_if(Auth::User()->role === 3, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $id = (int) request('id');

        // does not exists in that way
        $control = Control::find($id);
        if ($control === null) {
            Log::Error('Control:make - Control not found  '. request('id'));
            return null;
        }

        // Control already made ?
        if ($control->realisation_date !== null) {
            // TODO : return an error "Control already made"
            return redirect('/control/show/'.$id);
        }

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // save control_id in session for document upload
        $request->session()->put('control', $id);

        // return view
        return view('controls.make')
            ->with('control', $control)
            ->with('documents', $documents);
    }

    /**
     * Do a Control
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function doMake()
    {
        // Log::Alert("doMake START");
        $id = (int) request('id');
        // Log::Alert("doMake id=".$id);
        // dd($request);

        // check :
        // plan date not in the past
        if (request('score') === null) {
            return back()
                ->withErrors(['score' => 'score not set'])
                ->withInput();
        }

        // Control fields
        $control = Control::find($id);

        // control already made ?
        if ($control->realisation_date !== null) {
            return null;
        }

        $control->observations = request('observations');
        $control->plan_date = request('plan_date');
        $control->realisation_date = request('realisation_date');
        $control->note = request('note');
        $control->score = request('score');
        $control->action_plan = request('action_plan');

        // Log::Alert("doMake realisation_date=".request("realisation_date"));

        // if there is no next control
        if ($control->next_id === null) {
            // create a new control
            $new_control = new Control();
            $new_control->measure_id = $control->measure_id;
            $new_control->domain_id = $control->domain_id;
            $new_control->name = $control->name;
            $new_control->clause = $control->clause;
            $new_control->objective = $control->objective;
            $new_control->input = $control->input;
            $new_control->model = $control->model;
            $new_control->indicator = $control->indicator;

            // should action_plan comes from measure
            $new_control->action_plan = $control->action_plan;
            $new_control->owner = $control->owner;
            $new_control->periodicity = $control->periodicity;
            $new_control->retention = $control->retention;
            $new_control->plan_date = request('next_date');
            $new_control->save();
            // make link
            $control->next_id = $new_control->id;
        }

        // update control
        $control->update();

        return redirect('/');
    }

    /**
     * Save a Control
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        // Only for CISO
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $id = intval($request->id);

        $control = Control::find($id);

        $control->name = request('name');
        $control->objective = request('objective');
        $control->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        $control->input = request('input');
        $control->plan_date = request('plan_date');
        $control->realisation_date = request('realisation_date');
        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->action_plan = request('action_plan');
        $control->periodicity = request('periodicity');

        $control->save();

        return redirect('/control/show/'.$id);
    }

    /**
     * Draft a Control
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function draft(Request $request)
    {
        $id = (int) $request->get('id');

        $control = Control::find($id);

        $control->plan_date = request('plan_date');
        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->action_plan = request('action_plan');

        $control->save();

        return redirect('/control/show/'.$id);
    }

    public function export()
    {
        return Excel::download(new ControlsExport(), 'control.xlsx');
    }

    public function template()
    {
        $id = (int) request('id');

        // find associate measurement
        $control = Control::find($id);

        // Get template file
        $template_filename = storage_path('app/models/control_.docx');
        if (! file_exists($template_filename)) {
            $template_filename = storage_path('app/models/control.docx');
        }

        // create templateProcessor
        $templateProcessor = new TemplateProcessor($template_filename);

        // Replace names
        $templateProcessor->setValue('ref', $control->clause);
        $templateProcessor->setValue('name', $control->name);
        $templateProcessor->setValue('attributes', $control->attributes);

        $templateProcessor->setValue(
            'objective',
            strtr(
                $control->objective,
                ["\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"]
            )
        );
        $templateProcessor->setValue(
            'input',
            strtr(
                $control->input,
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
        $templateProcessor->setValue('date', Carbon::today()->format('d/m/Y'));

        // save a copy
        $filepath = storage_path(
            'templates/control-' .
            $control->clause .
            '-' .
            now()->format('Ymd') .
            '.docx'
        );

        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);

        // return response()->make($user->avatar, 200, array(
        //     'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($user->avatar)
        // ));
    }
}
