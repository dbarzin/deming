<?php

namespace App\Http\Controllers;

use App\Exports\ControlsExport;
use App\Models\Control;
use App\Models\Document;
use App\Models\Domain;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // get all domains
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

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereNull('realisation_date')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

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

        // Scope filter
        $scope = $request->get('scope');
        if ($scope !== null) {
            if ($scope === 'none') {
                $request->session()->forget('scope');
                $scope = null;
            } else {
                $request->session()->put('scope', $scope);
            }
        } else {
            $scope = $request->session()->get('scope');
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
            ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->leftjoin('domains', 'c1.domain_id', '=', 'domains.id');

        // Filter on domain
        if (($domain !== null) && ($domain !== 0)) {
            $controls = $controls->where('c1.domain_id', '=', $domain);
        }

        // Filter on scope
        if ($scope !== null) {
            $controls = $controls->where('c1.scope', '=', $scope);
        }

        // filter on measure
        if ($request->measure!=null) {
            $controls = $controls
                ->where('c1.measure_id','=',$request->measure);
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
                ->where('c1.plan_date', '<', Carbon::today()->format('Y-m-d'))
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
                'c1.scope',
                'c1.clause',
                'c1.domain_id',
                'c1.plan_date',
                'c1.realisation_date',
                'c1.score as score',
                'c2.id as next_id',
                'c2.plan_date as next_date',
                'domains.title',
            ]
        )
            ->orderBy('c1.id')->get();

        // return view
        return view('controls.index')
            ->with('controls', $controls)
            ->with('attributes', $attributes)
            ->with('scopes', $scopes)
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

        // Control not found
        if ($control === null) {
            abort(404);
        }

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
    public function edit(int $id)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control = Control::find($id);

        // Control not found
        if ($control === null) {
            abort(404);
        }

        $documents = DB::table('documents')->where('control_id', $id)->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereNull('realisation_date')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

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
            ->with('scopes', $scopes)
            ->with('attributes', $values);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get the control
        $control = Control::find($id);

        // Control not found
        if ($control === null) {
            abort(404);
        }

        // Delete files
        $documents = Document::select('id')->where('control_id', $id)->get();
        foreach ($documents as $doc) {
            unlink(storage_path('docs/' . $doc->id));
        }

        // Delete associated documents
        Document::where('control_id', $id)->delete();

        // Previous control must point to next control
        Control::where('next_id', $control->id)
            ->update(['next_id' => $control->next_id]);

        // Then delete the control
        $control->delete();

        return redirect('/controls');
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

    public function domains(Request $request)
    {
        // get all domains
        $domains = DB::table('domains')->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNull('realisation_date')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        // Scope filter
        $scope = $request->get('scope');
        if ($scope !== null) {
            $request->session()->put('scope', $scope);
        } else {
            $request->session()->forget('scope');
        }

        // count control never made
        $controls_never_made = DB::select(
            'select domain_id 
            from controls c1 
            where realisation_date is null and 
            not exists (
                select * 
                from controls c2 
                where realisation_date is not null and c1.measure_id=c2.measure_id);'
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
                        realisation_date is not null ' .
                        ($scope !== null ? "and scope=\"{$scope}\"" : '') .
                    'group by measure_id
                    ) as c1,                    
                    controls c2,
                    domains
                where
                    c1.id=c2.id and domains.id=c2.domain_id
                order by id;');

        // return
        return view('/radar/domains')
            ->with('domains', $domains)
            ->with('scopes', $scopes)
            ->with('active_controls', $active_controls)
            ->with('controls_never_made', $controls_never_made);
    }

    public function measures(Request $request)
    {
        // Get all domains
        $domains = Domain::All();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNull('realisation_date')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        $cur_scope = $request->get("scope");
        if ($cur_scope !== null)
            $request->session()->put('scope', $cur_scope);
        else
            $request->session()->forget('scope');
        /*
        $cur_date = $request->get('cur_date');
        if ($cur_date === null) {
            $cur_date = today()->format('Y-m-d');
        } else {
            // Avoid SQL Injection
            $cur_date = \Carbon\Carbon::createFromFormat('Y-m-d', $cur_date)->format('Y-m-d');
        }
        */
        // Query controls
        $controls = DB::table('controls as c1')
            ->select(
                DB::raw(
                    '
                    c1.id AS control_id,
                    c1.name,
                    c1.clause,
                    c1.scope,
                    c1.measure_id, 
                    c1.domain_id, 
                    c1.plan_date, 
                    c1.realisation_date, 
                    c1.score AS score, 
                    c2.plan_date AS next_date, 
                    c2.id AS next_id'
                )
            )
            ->join('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->where('c2.realisation_date', '=', null);
        if ($cur_scope !=null)
            $controls = $controls->where('c1.scope','=',$cur_scope);
        $controls = $controls
//            ->where('c1.realisation_date', '<=', $cur_date)
            ->orderBy('clause')
            ->orderBy('scope')
            ->get();

        // return
        return view('radar.controls')
            ->with('scopes', $scopes)
            ->with('controls', $controls)
//          ->with('cur_date', $cur_date)
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

        $users = User::orderBy('name')->get();

        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNull('realisation_date')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        return view('controls.plan', compact('control'))
            ->with('years', $years)
            ->with('day', date('d', strtotime($control->plan_date)))
            ->with('month', date('m', strtotime($control->plan_date)))
            ->with('year', date('Y', strtotime($control->plan_date)))
            ->with('scopes', $scopes)
            ->with('users', $users)
        ;
    }

    /**
     * unPlan a control.
     *
     * @param  \App\Measure $measure
     *
     * @return \Illuminate\Http\Response
     */
    public function unplan(Request $request)
    {
        // Not for Auditor
        abort_if(Auth::User()->role === 3, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control = Control
                ::whereNull('realisation_date')
                ->where('id', '=', $request->id)
                ->get()
                ->first();

        if ($control !== null) {
            // break previous link
            $prev_control = Control::where('next_id', $control->id)->get()->first();
            if ($prev_control !== null) {
                $prev_control->next_id = null;
                $prev_control->update();
            }

            $control->delete();
        }

        return redirect('/measures');
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
        // For administrators and users only
        abort_if((Auth::User()->role !== 1) && (Auth::User()->rol !== 2), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control = Control::find($request->id);

        // Control not found
        if ($control === null) {
            abort(404);
        }

        // Control already made ?
        if ($control->realisation_date !== null) {
            return back()
                ->withErrors(['msg' => 'Control already made'])
                ->withInput();
        }

        // Check duplicate control on same scope
        if (Control::whereNull('realisation_date')
                ->where('id','<>',$request->id)
                ->where('scope','=',$request->scope)
                ->count() > 0) {
            return back()
                ->withErrors(['msg' => 'Control duplicate'])
                ->withInput();
        }

        $control->scope = $request->scope;
        $control->plan_date = $request->plan_date;
        $control->periodicity = $request->periodicity;
        $control->owners()->sync($request->input('owners', []));
        $control->save();

        return redirect('/controls/'.$request->id);
    }

    public function make(Request $request)
    {
        // Not for aditor
        abort_if(Auth::User()->role === 3, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $id = (int) request('id');

        // Control not found
        $control = Control::find($id);
        if ($control === null) {
            abort(404);
        }

        // Control already made ?
        if ($control->realisation_date !== null) {
            return back()->withErrors(['msg' => 'Control already made']);
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

        // Control not found
        if ($control === null) {
            abort(404);
        }

        // control already made ?
        if ($control->realisation_date !== null) {
            return back()->withErrors(['msg' => 'Control already made']);
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
            $new_control = $control->replicate();
            $new_control->observations = null;
            $new_control->realisation_date = null;
            $new_control->note = null;
            $new_control->score = null;
            $new_control->plan_date = request('next_date');
            $new_control->save();

            // Set owners
            $new_control->owners()->sync($control->owners->pluck('id')->toArray());

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

        // Get the control
        $control = Control::find($request->id);

        // Control not found
        if ($control === null) {
            abort(404);
        }

        $control->name = request('name');
        $control->scope = request('scope');
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

        return redirect('/control/show/' . $request->id);
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
        $templateProcessor->setValue('scope', $control->scope);
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
    }
}
