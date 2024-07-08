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
use PhpOffice\PhpWord\TemplateProcessor as PhpWordTemplateProcessor;

class ControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Not for API
        abort_if(
            Auth::User()->role === 4,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
        $attributes = array_unique($attributes);

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
            ->where('scope', '<>', '');
        if (Auth::User()->role === 5) {
            $scopes = $scopes
                ->leftjoin('control_user', 'controls.id', '=', 'control_user.control_id')
                ->where('control_user.user_id', '=', Auth::User()->id);
        }
        $scopes = $scopes
            //->whereNull('realisation_date')
            ->whereIn('status', [0,1])
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
            if ($period === null) {
                $request->session()->put('period', 99);
            }
        }

        // Status filter
        $status = $request->get('status');
        if ($status !== null) {
            $request->session()->put('status', $status);
        } else {
            $status = $request->session()->get('status');
            if ($status === null) {
                $status = '2';
            }
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

        // filter on auditee controls
        if (Auth::User()->role === 5) {
            $controls = $controls
                ->leftjoin('control_user', 'c1.id', '=', 'control_user.control_id')
                ->where('control_user.user_id', '=', Auth::User()->id);
        }

        // Filter on domain
        if (($domain !== null) && ($domain !== 0)) {
            $controls = $controls->where('c1.domain_id', '=', $domain);
        }

        // Filter on scope
        if ($scope !== null) {
            $controls = $controls->where('c1.scope', '=', $scope);
        }

        // filter on measure
        if ($request->measure !== null) {
            $controls = $controls
                ->where('c1.measure_id', '=', $request->measure);
        }

        // Filter on period
        if (($period !== null) && ($period !== 99)) {
            $controls = $controls
                ->where('c1.plan_date', '>=', (new Carbon('first day of this month'))->addMonth($period)->format('Y-m-d'))
                ->where('c1.plan_date', '<', (new Carbon('first day of next month'))->addMonth($period)->format('Y-m-d'));
        }

        // Filter on status
        if ($late !== null) { // All
            $controls = $controls
                ->where('c1.plan_date', '<', Carbon::today()->format('Y-m-d'))
                // ->whereNull('c1.realisation_date');
                ->whereIn('c1.status', [0,1]);
        } elseif ($status === '1') { // Done
            if (Auth::User()->role === 5) {
                // Auditee want to see his proposed controls too
                $controls = $controls
                    ->whereIn('c1.status', [1,2]);
            } else {
                $controls = $controls
                    ->where('c1.status', 2);
            }
        } elseif ($status === '2') { // Todo
            if (Auth::User()->role === 5) {
                $controls = $controls
                    //->whereNull('c1.realisation_date');
                    ->where('c1.status', 0);
            } else {
                $controls = $controls
                    //->whereNull('c1.realisation_date');
                    ->whereIn('c1.status', [0,1]);
            }
        }

        // Filter on attribute
        if ($attribute !== null) {
            $controls = $controls->where('c1.attributes', 'LIKE', '%'.$attribute.'%');
        }

        // Query DB
        $controls = $controls->select(
            [
                'c1.id',
                'c1.name',
                'c1.scope',
                'c1.plan_date',
                'c1.realisation_date',
                'c1.score as score',
                'c1.status',
                'c2.id as next_id',
                'c2.plan_date as next_date',
                'domains.title',
            ]
        )
            ->orderBy('c1.id')->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'control_id',
                'measure_id',
                'clause'
            ])
            ->leftjoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls->pluck('id'))
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach($controls as $control) {
            $control->measures = $measuresByControlId->get($control->id, collect())->map(function ($controlMeasure) {
                return [
                    'id' => $controlMeasure->measure_id,
                    'clause' => $controlMeasure->clause
                    ];
                });
            }

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
        return redirect('/bob/index');
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
        // Not API
        abort_if(Auth::User()->role === 4, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // for aditee only if he is assigne to that control
        abort_if(
            ((Auth::User()->role === 5) &&
                ! DB::table('control_user')
                    ->where('control_id', $id)
                    ->where('user_id', Auth::User()->id)
                    ->exists()),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $documents = DB::table('documents')->where('control_id', $id)->get();

        // get all ids
        $ids = DB::table('controls')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->pluck('id')
            ->toArray();

        // get all clauses
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        $measures = DB::table('control_measure')
            ->select('measure_id')
            ->where('control_id',$id)
            ->get()
            ->pluck('measure_id')
            ->toArray();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            // ->whereNull('realisation_date')
            ->whereIn('status', [0,1])
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
        $values = array_unique($values);

        $users = User::orderBy('name')->get();

        return view('controls.edit')
            ->with('control', $control)
            ->with('documents', $documents)
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('measures', $measures)
            ->with('ids', $ids)
            ->with('attributes', $values)
            ->with('users', $users);
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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

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

        return redirect('/bob/index');
    }

    public function history()
    {
        // Not API and auditee
        abort_if(
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get all controls
        $controls = DB::table('controls')
            ->select('id', 'score', 'realisation_date', 'plan_date')
            ->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select([
                'control_id',
                'clause'
            ])
            ->leftjoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls->pluck('id'))
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach($controls as $control) {
            $control->measures = $measuresByControlId->get($control->id, collect())->map(function ($controlMeasure) {
                return $controlMeasure->clause;
                });
            }

        // return
        return view('controls.history')
            ->with('controls', $controls);
    }

    public function domains(Request $request)
    {
        // Not API and auditee
        abort_if(
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get all active domains
        $domains = DB::table('domains')
            ->select(DB::raw('distinct domains.id, domains.title'))
            ->join('measures', 'domains.id', '=', 'measures.domain_id')
            ->join('control_measure', 'control_measure.measure_id', '=', 'measures.id')
            ->join('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('controls.status', [0,1])
            ->orderBy('domains.title')
            ->get();

        // get all frameworks
        $frameworks = DB::table('domains')
            ->select(DB::raw('distinct domains.framework'))
            ->join('measures', 'domains.id', '=', 'measures.domain_id')
            ->join('control_measure', 'control_measure.measure_id', '=', 'measures.id')
            ->join('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('controls.status', [0,1])
            ->orderBy('domains.framework')
            ->get()
            ->pluck('framework')
            ->toArray();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            // ->whereNull('realisation_date')
            ->whereIn('status', [0,1])
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
        // TODO : improve me
        $controls_never_made = DB::select(
            'select domain_id
            from controls c1
            where
            (status=0 or status=1) and
            not exists (
                select *
                from controls c2
                where c2.status=2 and c1.id=c2.next_id);'
        );

        // Last controls made by measures
        // TODO : improve me
        $active_controls = DB::select('
                select
                    c2.id,
                    c2.measure_id,
                    domains.title,
                    c2.realisation_date,
                    c2.score
                from
                    controls c1,
                    controls c2,
                    domains
                where
                    (c1.status=0 or c1.status=1) and
                    c1.id = c2.next_id and
                    domains.id=c1.domain_id
                order by domains.title;');

        // return
        return view('radar.domains')
            ->with('frameworks', $frameworks)
            ->with('domains', $domains)
            ->with('scopes', $scopes)
            ->with('active_controls', $active_controls)
            ->with('controls_never_made', $controls_never_made);
    }

    public function measures(Request $request)
    {
        // get all active domains
        $domains = DB::table('domains')
            ->select(DB::raw('distinct domains.id, domains.title, domains.description'))
            ->join('controls', 'domains.id', '=', 'controls.domain_id')
            // ->whereNull('realisation_date')
            ->whereIn('status', [0,1])
            ->orderBy('domains.title')
            ->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            //->whereNull('realisation_date')
            ->whereIn('status', [0,1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        $cur_scope = $request->get('scope');
        if ($cur_scope !== null) {
            $request->session()->put('scope', $cur_scope);
        } else {
            $request->session()->forget('scope');
        }
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
                    c2.clause,
                    c1.scope,
                    c2.measure_id,
                    c2.domain_id,
                    c1.plan_date,
                    c1.realisation_date,
                    c1.score as score,
                    c2.plan_date as next_date,
                    c2.id AS next_id'
                )
            )
            ->join('controls as c2', 'c1.next_id', '=', 'c2.id')
            // ->where('c2.realisation_date', '=', null);
            ->whereIn('c2.status', [0, 1]);
        if ($cur_scope !== null) {
            $controls = $controls->where('c1.scope', '=', $cur_scope);
        }
        $controls = $controls
            ->orderBy('clause')
            ->orderBy('scope')
            ->get();

        // return
        return view('/radar/controls')
            ->with('scopes', $scopes)
            ->with('controls', $controls)
//          ->with('cur_date', $cur_date)
            ->with('domains', $domains);
    }

    public function attributes()
    {
        // Not API and auditee
        abort_if(
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get all attributes
        $attributes = DB::table('attributes')
            ->orderBy('name')
            ->get();

        // Controls made
        // TODO : improve me
        $controls = DB::select('
                select
                    c2.id,
                    c1.name,
                    c1.attributes,
                    c2.realisation_date,
                    c2.score
                from
                    controls c1,
                    controls c2,
                    domains
                where
                    c1.status=0 and
                    c1.id = c2.next_id and
                    domains.id=c1.domain_id
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
        // For administrators and users only
        abort_if((Auth::User()->role !== 1) && (Auth::User()->role !== 2), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // does not exists in that way
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

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
            // ->whereNull('realisation_date')
            ->whereIn('status', [0,1])
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
        // For administrators and users only
        abort_if((Auth::User()->role !== 1) && (Auth::User()->rol !== 2), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $control = Control
                // ::whereNull('realisation_date')
                ::whereIn('status', [0,1])
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

        return redirect('/alice/index');
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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        // if ($control->realisation_date !== null) {
        if ($control->status === 2) {
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.made')])
                ->withInput();
        }

        // Check duplicate control on same scope
        if (Control
            // ::whereNull('realisation_date')
            ::whereIn('status', [0,1])
                ->where('id', '<>', $control->id)
                ->where('measure_id', '=', $control->measure_id)
                ->where('scope', '=', $request->scope)
                ->count() > 0) {
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.duplicate')])
                ->withInput();
        }

        $control->scope = $request->scope;
        $control->plan_date = $request->plan_date;
        $control->periodicity = $request->periodicity;
        $control->owners()->sync($request->input('owners', []));
        $control->save();

        return redirect('/bob/show/'.$request->id);
    }

    public function make(Request $request)
    {
        // Not for auditor and API
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

        // for aditee only if he is assigne to that control
        abort_if(
            ((Auth::User()->role === 5) &&
                ! DB::table('control_user')
                    ->where('user_id', Auth::User()->id)
                    ->where('control_id', $id)
                    ->exists()),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        if ($control->status === 2) {
            return back()->withErrors(['msg' => trans('cruds.control.error.made')]);
        }

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // save control_id in session for document upload
        $request->session()->put('control', $id);

        // compute next control date
        $next_date = date('Y-m-d', strtotime($control->periodicity.' months', strtotime($control->plan_date)));

        // compute next control date
        $next_date = $control->next_date === null ?
            \Carbon\Carbon::createFromFormat('Y-m-d', $control->plan_date)
                ->addMonths($control->periodicity)
                ->format('Y-m-d')
            : $control->next_date->format('Y-m-d');

        // return view
        return view('controls.make')
            ->with('control', $control)
            ->with('documents', $documents)
            ->with('next_date', $next_date);
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
        // Only for admin, user and auditee
        abort_if(
            ! ((Auth::User()->role === 1)
            || (Auth::User()->role === 2)
            || (Auth::User()->role === 5)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

        // for aditee only if he is assigne to that control
        abort_if(
            ((Auth::User()->role === 5) &&
                ! DB::table('control_user')
                    ->where('user_id', Auth::User()->id)
                    ->where('control_id', $id)
                    ->exists()),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // control already made ?
        if ($control->status === 2) {
            return back()->withErrors(['msg' => 'Control already made']);
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->realisation_date = request('realisation_date');
        // only admin and user can update the plan_date and action_plan
        if (
            (Auth::User()->role === 1) ||
            (Auth::User()->role === 2)
        ) {
            $control->plan_date = request('plan_date');
            $control->action_plan = request('action_plan');
        } else {
            $control->realisation_date = date('Y-m-d', strtotime('today'));
        }
        // Log::Alert("doMake realisation_date=".request("realisation_date"));

        // Auditee -> propose control
        if (Auth::User()->role === 5) {
            $control->status = 1;
        }
        // if there is no next control
        elseif ($control->next_id === null) {
            // create a new control
            $new_control = $control->replicate();
            $new_control->observations = null;
            $new_control->realisation_date = null;
            $new_control->note = null;
            $new_control->score = null;
            $new_control->status = 0;
            // only admin and user can update the plan_date, realisation_date and action_plan
            if (
                (Auth::User()->role === 1) ||
                (Auth::User()->role === 2)
            ) {
                $new_control->plan_date = request('next_date');
            } else {
                $new_control->plan_date = date('Y-m-d', strtotime($control->periodicity.' months', strtotime($control->plan_date)));
            }

            $new_control->save();

            // Set owners
            $new_control->owners()->sync($control->owners->pluck('id')->toArray());

            // set status done
            $control->status = 2;

            // make link
            $control->next_id = $new_control->id;
        }

        // update control
        $control->update();

        return redirect('/bob/index');
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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $control->name = request('name');
        $control->scope = request('scope');
        $control->objective = request('objective');
        $control->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        $control->input = request('input');
        $control->model = request('model');
        $control->plan_date = request('plan_date');
        $control->realisation_date = request('realisation_date');
        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->action_plan = request('action_plan');
        $control->periodicity = request('periodicity');
        $control->status = request('status');
        $control->next_id = request('next_id');
        $control->owners()->sync($request->input('owners', []));
        $control->measures()->sync($request->input('measures', []));

        $control->save();

        return redirect('/bob/show/' . $request->id);
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
        // Not for API and Auditor
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) $request->get('id');

        // for aditee only if he is assigned to that control
        abort_if(
            ((Auth::User()->role === 5) &&
                ! DB::table('control_user')
                    ->where('user_id', Auth::User()->id)
                    ->where('control_id', $id)
                    ->exists()),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get the control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        if ($control->status === 2) {
            return back()->withErrors(['msg' => trans('cruds.control.error.made')]);
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');

        // only admin and user can update the plan_date and action_plan
        if (
            (Auth::User()->role === 1) ||
            (Auth::User()->role === 2)
        ) {
            $control->plan_date = request('plan_date');
            $control->action_plan = request('action_plan');
            // do not save the realisation date as it is in draft
        }
        $control->save();

        return redirect('/bob/show/'.$id);
    }

    /**
     * Reject a Control
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request)
    {
        // Only for Admin and user
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) $request->get('id');

        // Get the control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        if ($control->status === 2) {
            return back()->withErrors(['msg' => trans('cruds.control.error.made')]);
        }

        // Change fields
        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->plan_date = request('plan_date');
        $control->action_plan = request('action_plan');

        // Reject -> set status=0
        $control->status = 0;

        $control->save();

        return redirect('/bob/index');
    }

    /**
     * Accept a Control
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function accept()
    {
        // Only for Admin and user
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

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
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // control already made ?
        // if ($control->realisation_date !== null) {
        if ($control->status === 2) {
            return back()->withErrors(['msg' => 'Control already made']);
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->realisation_date = request('realisation_date');
        $control->plan_date = request('plan_date');
        $control->action_plan = request('action_plan');

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

        // set status done
        $control->status = 2;

        // update control
        $control->update();

        return redirect('/bob/index');
    }

    public function export()
    {
        // For administrators and users only
        abort_if((Auth::User()->role !== 1) && (Auth::User()->rol !== 2), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new ControlsExport(), trans('cruds.control.title') . '-' . now()->format('Y-m-d Hi') . '.xlsx');
    }

    public function template()
    {
        // For administrators and users only
        abort_if(
            (Auth::User()->role !== 1) &&
                (Auth::User()->rol !== 2) &&
                (Auth::User()->role !== 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

        // find associate measurement
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Get template file
        $template_filename = storage_path('app/models/control_.docx');
        if (! file_exists($template_filename)) {
            $template_filename = storage_path('app/models/control.docx');
        }

        // create templateProcessor
        $templateProcessor = new PhpWordTemplateProcessor($template_filename);

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
