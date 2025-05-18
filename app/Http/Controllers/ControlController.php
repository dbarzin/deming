<?php

namespace App\Http\Controllers;

use App\Exports\ControlsExport;
use App\Models\Action;
use App\Models\Control;
use App\Models\Document;
use App\Models\Domain;
use App\Models\Measure;
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

        // -----------------------------------------------------
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

        // Clause filter
        $clause = $request->get('clause');
        if ($clause !== null) {
            if ($clause === 'none') {
                $request->session()->forget('clause');
                $clause = null;
            } else {
                $request->session()->put('clause', $clause);
            }
        } else {
            $clause = $request->session()->get('clause');
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

        // -----------------------------------------------------
        // get all domains
        $domains = Domain::All();

        // get all clauses
        $clauses = DB::table('measures')
            ->select('clause')
            ->when($domain !== null, function ($q) use ($domain) {
                return $q->where('domain_id', '=', $domain);
            })
            ->get()
            ->pluck('clause');

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
                ->leftJoin('control_user', 'controls.id', '=', 'control_user.control_id')
                ->leftJoin('control_user_group', 'controls.id', '=', 'control_user_group.control_id')
                ->leftJoin('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
                ->where(function ($query) {
                    $query->where('control_user.user_id', '=', Auth::User()->id)
                        ->orWhere('user_user_group.user_id', '=', Auth::User()->id);
                });
        }
        $scopes = $scopes
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope');

        // -----------------------------------------------------
        // Build query
        $controls = DB::table('controls as c1')
            ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->leftjoin(
                'control_measure',
                'control_measure.control_id',
                '=',
                'c1.id'
            )
            ->leftjoin('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->leftjoin('domains', 'measures.domain_id', '=', 'domains.id');

        // filter on auditee controls
        if (Auth::User()->role === 5) {
            $controls = $controls
                ->leftJoin('control_user', 'c1.id', '=', 'control_user.control_id')
                ->leftJoin('control_user_group', 'c1.id', '=', 'control_user_group.control_id')
                ->leftJoin('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
                ->where(function ($query) {
                    $query->where('control_user.user_id', '=', Auth::User()->id)
                        ->orWhere('user_user_group.user_id', '=', Auth::User()->id);
                });
        }

        // Filter on domain
        if ($domain !== null && $domain !== 0) {
            $controls = $controls->where('measures.domain_id', '=', $domain);
        }

        // Filter on clause
        if ($clause !== null) {
            $controls = $controls->where('clause', '=', $clause);
        }

        // Filter on scope
        if ($scope !== null) {
            $controls = $controls->where('c1.scope', '=', $scope);
        }

        // Filter on period
        if ($period !== null && $period !== 99) {
            $controls = $controls
                ->where(
                    'c1.plan_date',
                    '>=',
                    (new Carbon('first day of this month'))
                        ->addMonth($period)
                        ->format('Y-m-d')
                )
                ->where(
                    'c1.plan_date',
                    '<',
                    (new Carbon('first day of next month'))
                        ->addMonth($period)
                        ->format('Y-m-d')
                );
        }

        // Filter on status
        if ($late !== null) {
            // All
            $controls = $controls
                ->where('c1.plan_date', '<', Carbon::today()->format('Y-m-d'))
                // ->whereNull('c1.realisation_date');
                ->whereIn('c1.status', [0, 1]);
        } elseif ($status === '1') {
            // Done
            if (Auth::User()->role === 5) {
                // Auditee want to see his proposed controls too
                $controls = $controls->whereIn('c1.status', [1, 2]);
            } else {
                $controls = $controls->where('c1.status', 2);
            }
        } elseif ($status === '2') {
            // TODO: ??
            if (Auth::User()->role === 5) {
                $controls = $controls
                    //->whereNull('c1.realisation_date');
                    ->where('c1.status', 0);
            } else {
                $controls = $controls
                    //->whereNull('c1.realisation_date');
                    ->whereIn('c1.status', [0, 1]);
            }
        }

        // get action plan associated
        $controls = $controls->leftjoin('actions', 'actions.control_id', '=', 'c1.id');

        // Query DB
        $controls = $controls
            ->select([
                'c1.id',
                'c1.name',
                'c1.scope',
                'c1.plan_date',
                'c1.realisation_date',
                'c1.score as score',
                'c1.status',
                'actions.id as action_id',
                'c2.id as next_id',
                'c2.plan_date as next_date',
            ])
            ->orderBy('c1.id')
            ->distinct()
            ->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select(['control_id', 'measure_id', 'clause'])
            ->leftjoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach ($controls as $control) {
            $control->measures = $measuresByControlId
                ->get($control->id, collect())
                ->map(function ($controlMeasure) {
                    return [
                        'id' => $controlMeasure->measure_id,
                        'clause' => $controlMeasure->clause,
                    ];
                });
        }

        // return view
        return view('controls.index')
            ->with('controls', $controls)
            ->with('clauses', $clauses)
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
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get all clauses
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope');

        // get all attributes
        $values = [];
        $attributes = DB::table('measures')->select('attributes')->get();
        foreach ($attributes as $key) {
            foreach (explode(' ', $key->attributes) as $value) {
                array_push($values, $value);
            }
        }
        sort($values);
        $values = array_unique($values);

        // get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // get all groups
        $groups = DB::table('user_groups')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Owners list
        $owners = collect();
        foreach ($users as $user) {
            $owners->put('USR_' . $user->id, $user->name);
        }
        foreach ($groups as $group) {
            $owners->put('GRP_' . $group->id, $group->name);
        }

        return view('controls.create')
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('attributes', $values)
            ->with('owners', $owners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'name' => 'required|min:3|max:255',
                'scope' => 'max:32',
                'objective' => 'required',
                'plan_date' => 'required',
                'periodicity' => 'required|integer',
            ]
        );

        // Create control
        $control = new Control();
        // Fill fields
        $control->name = request('name');
        $control->scope = request('scope');
        $control->objective = request('objective');
        $control->attributes =
            request('attributes') !== null
                ? implode(' ', request('attributes'))
                : null;
        $control->input = request('input');
        $control->model = request('model');
        $control->plan_date = request('plan_date');
        $control->action_plan = request('action_plan');
        $control->periodicity = request('periodicity');
        // Save it
        $control->save();

        // Sync users
        $users = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'USR_'))
                $users->push(intval(substr($owner, 4)));
        }
        $control->users()->sync($users);

        // Sync groups
        $groups = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'GRP_'))
                $groups->push(intval(substr($owner, 4)));
        }
        $control->groups()->sync($groups);

        // Sync measures
        $control->measures()->sync($request->input('measures', []));

        // Redirect to index
        return redirect('/bob/index');
    }

    /**
     * Display a control
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        // Not API
        abort_if(
            Auth::User()->role === 4,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // for aditee only if he is assigne to that control
        abort_if(
            Auth::User()->role === 5 &&
                ! (DB::table('control_user')
                    ->where('control_id', $id)
                    ->where('user_id', Auth::User()->id)
                    ->exists()
                    ||
                DB::table('control_user_group')
                    ->join('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
                    ->where('control_user_group.control_id', $id)
                    ->where('user_user_group.user_id', Auth::User()->id)
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
                ->get()
                ->first();
        } else {
            $next_control = null;
        }

        $prev_control = DB::table('controls')
            ->select('id', 'plan_date')
            ->where('next_id', '=', $id)
            ->get()
            ->first();

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // Return
        return view('controls.show')
            ->with('control', $control)
            ->with('next_id', $next_control !== null ? $next_control->id : null)
            ->with(
                'next_date',
                $next_control !== null ? $next_control->plan_date : null
            )
            ->with('prev_id', $prev_control !== null ? $prev_control->id : null)
            ->with(
                'prev_date',
                $prev_control !== null ? $prev_control->plan_date : null
            )
            ->with('documents', $documents);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        // Only for administrator role
        abort_if(
            Auth::User()->role !== 1,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $documents = DB::table('documents')->where('control_id', $id)->get();

        // get all ids
        $ids = DB::table('controls')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->pluck('id');

        // get all clauses
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // get all groups
        $groups = DB::table('user_groups')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Owners list
        $owners = collect();
        foreach ($users as $user) {
            $owners->put('USR_' . $user->id, $user->name);
        }
        foreach ($groups as $group) {
            $owners->put('GRP_' . $group->id, $group->name);
        }

        // get measures
        $measures = DB::table('control_measure')
            ->select('measure_id')
            ->where('control_id', $id)
            ->get()
            ->pluck('measure_id')
            ->toArray();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        // get all attributes
        $values = [];
        $attributes = DB::table('measures')->select('attributes')->get();
        foreach ($attributes as $key) {
            foreach (explode(' ', $key->attributes) as $value) {
                array_push($values, $value);
            }
        }
        sort($values);
        $values = array_unique($values);

        return view('controls.edit')
            ->with('control', $control)
            ->with('documents', $documents)
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('measures', $measures)
            ->with('ids', $ids)
            ->with('attributes', $values)
            ->with('owners', $owners);
    }

    /**
     * Clone a control.
     *
     * @param  int Control id
     *
     * @return \Illuminate\Http\Response
     */
    public function clone(Request $request)
    {
        // Only for admin and users
        abort_if(
            (Auth::User()->role !== 1) && (Auth::User()->role !== 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get all clauses
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope');

        // get all attributes
        $values = [];
        $attributes = DB::table('measures')->select('attributes')->get();
        foreach ($attributes as $key) {
            foreach (explode(' ', $key->attributes) as $value) {
                array_push($values, $value);
            }
        }
        sort($values);
        $values = array_unique($values);

        // get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // get all groups
        $groups = DB::table('user_groups')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Owners list
        $owners = collect();
        foreach ($users as $user) {
            $owners->put('USR_' . $user->id, $user->name);
        }
        foreach ($groups as $group) {
            $owners->put('GRP_' . $group->id, $group->name);
        }

        // Get Control
        $control = Control::find($request->id);

        // Workstation not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $request->merge(
            $control->only(
                [
                    'name','scope', 'objective',
                    'input', 'periodicity', 'model', 'action_plan',
                    'plan_date',
                ]
            )
        );
        $request->merge(['measures' => $control->measures()->pluck('id')->toArray()]);
        $request->merge(['attributes' => explode(' ', $control->attributes)]);

        // Construct owners copy
        $items = [];
        foreach ($control->users as $user) {
            array_push($items, 'USR_' . $user->id);
        }
        foreach ($control->groups as $group) {
            array_push($items, 'GRP_' . $group->id);
        }
        $request->merge(['owners' => $items]);

        $request->flash();

        return view('controls.create')
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('attributes', $values)
            ->with('owners', $owners);
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
        abort_if(
            Auth::User()->role !== 1,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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
            ->update(['next_id' => $control->next_id,
            ]);

        // delete control_measures
        DB::Table('control_measure')
            ->where('control_id', '=', $control->id)
            ->delete();

        // delete control_
        DB::Table('control_user_group')
            ->where('control_id', '=', $control->id)
            ->delete();

        // Delete link with actions
        DB::Table('actions')
            ->where('control_id', $control->id)
            ->update(['control_id' => null]);

        // Then delete the control
        $control->delete();

        return redirect('/bob/index');
    }

    public function history()
    {
        // Abort if user role is 4 or 5
        abort_if(in_array(Auth::user()->role, [4, 5]), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Fetch controls and their measures in a single query
        $controlsData = DB::table('controls')
            ->select(
                'controls.id',
                'controls.score',
                'controls.observations',
                'controls.realisation_date',
                'controls.plan_date',
                'controls.periodicity',
                'measures.clause'
            )
            ->leftJoin('control_measure', 'controls.id', '=', 'control_measure.control_id')
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->get();

        // Group measures by control_id
        $controls = $controlsData->groupBy('id')->map(function ($controlGroup) {
            $control = $controlGroup->first();
            $control->measures = $controlGroup->pluck('clause')->filter()->values();
            return $control;
        })->values();

        // Repeat controls based on periodicity
        $expandedControls = collect();
        foreach ($controls as $control) {
            $expandedControls->push($control);

            if (($control->realisation_date === null) &&
                ($control->periodicity > 0) && ($control->periodicity <= 12)) {
                for ($i = 1; $i <= 12 / $control->periodicity; $i++) {
                    $repeatedControl = clone $control;
                    $repeatedControl->id = null;
                    $repeatedControl->score = null;
                    $repeatedControl->observations = null;
                    $repeatedControl->realisation_date = null;
                    $repeatedControl->plan_date = Carbon::parse($control->plan_date)->addMonthsNoOverflow($i * $control->periodicity);
                    $expandedControls->push($repeatedControl);
                }
            }
        }
        // Return view with controls
        return view('controls.history')
            ->with('controls', $expandedControls);
    }

    /*
     * Radar par domaine
     */
    public function domains(Request $request)
    {
        // Not API and auditee
        abort_if(
            Auth::User()->role === 4 || Auth::User()->role === 5,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Scope filter
        $scope = $request->get('scope');
        if ($scope !== null) {
            if ($scope === 'none') {
                $scope = null;
                $request->session()->forget('scope');
            } else {
                $request->session()->put('scope', $scope);
            }
        } else {
            $scope = $request->session()->get('scope');
        }

        // Framework filter
        $framework = $request->get('framework');
        if ($framework !== null) {
            if ($framework === 'none') {
                $framework = null;
                $request->session()->forget('framework');
            } else {
                $request->session()->put('framework', $framework);
            }
        } else {
            $framework = $request->session()->get('framework');
        }

        // Group by
        $group = $request->get('group');
        if ($group !== null) {
            $request->session()->put('group', $group);
        } else {
            $group = $request->session()->get('group');
        }

        // get all active domains
        $domains = DB::table('domains')
            ->select(DB::raw('distinct domains.id, domains.title'))
            ->join('measures', 'domains.id', '=', 'measures.domain_id')
            ->join(
                'control_measure',
                'control_measure.measure_id',
                '=',
                'measures.id'
            )
            ->join('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('controls.status', [0, 1]);

        if ($framework !== null) {
            $domains = $domains->where('framework', '=', $framework);
        }

        $domains = $domains->orderBy('domains.title')->get();

        // get all frameworks
        $frameworks = DB::table('domains')
            ->select(DB::raw('distinct domains.framework as title'))
            ->join('measures', 'domains.id', '=', 'measures.domain_id')
            ->join(
                'control_measure',
                'control_measure.measure_id',
                '=',
                'measures.id'
            )
            ->join('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('controls.status', [0, 1])
            ->orderBy('domains.framework')
            ->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereIn('status', [0, 1])
            ->whereNotNull('scope')
            ->distinct()
            ->orderBy('scope')
            ->get();

        // count control never made
        $controls_never_made = DB::table('controls as c1')
            ->select('domain_id')
            ->join(
                'control_measure',
                'c1.id',
                '=',
                'control_measure.control_id'
            )
            ->join('measures', 'measures.id', '=', 'control_measure.measure_id')
            ->leftJoin('controls as c2', 'c2.next_id', '=', 'c1.id')
            ->whereIn('c1.status', [0, 1])
            ->whereNull('c2.id')
            ->get();

        // Last controls made and measures
        $active_controls = DB::table('controls as c1');

        if ($group === '1') {
            // Group by measurements
            $active_controls = $active_controls->select([
                'domains.title',
                'measures.id as measure_id',
                'measures.clause as clause',
                'c1.id as control_id',
                'c1.name as name',
                'c1.scope as scope',
                DB::raw('min(c1.score) as score'),
            ]);
        } else {
            // All controls
            $active_controls = $active_controls->select([
                'domains.title',
                'measures.id as measure_id',
                'measures.clause as clause',
                'c1.id as control_id',
                'c1.name as name',
                'c1.scope as scope',
                'c1.score as score',
            ]);
        }

        $active_controls = $active_controls
            ->join('controls as c2', 'c2.id', '=', 'c1.next_id')
            ->join(
                'control_measure',
                'control_measure.control_id',
                '=',
                'c1.id'
            )
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->join('domains', 'domains.id', '=', 'measures.domain_id')
            ->whereIn('c2.status', [0, 1]);

        // Filter on framework
        if ($framework !== null) {
            $active_controls = $active_controls->where(
                'domains.framework',
                '=',
                $framework
            );
        }

        // Filter on scope
        if ($scope !== null) {
            $active_controls = $active_controls->where('c1.scope', '=', $scope);
        }

        // Group by measures
        if ($group === '1') {
            $active_controls = $active_controls->groupBy([
                'domains.title',
                'measures.id',
                'measures.clause',
            ]);
        }

        // Sort result
        $active_controls = $active_controls
            ->orderBy('domains.title')
            ->orderBy('clause')
            ->get();

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
            ->select(
                DB::raw(
                    'distinct domains.id, domains.title, domains.description'
                )
            )
            ->join('measures', 'domains.id', '=', 'measures.domain_id')
            ->join(
                'control_measure',
                'measures.id',
                '=',
                'control_measure.measure_id'
            )
            ->join('controls', 'control_measure.control_id', '=', 'controls.id')
            ->whereIn('status', [0, 1])
            ->orderBy('domains.title')
            ->get();

        // get all scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope');

        $cur_scope = $request->get('scope');
        if ($cur_scope !== null) {
            $request->session()->put('scope', $cur_scope);
        } else {
            $request->session()->forget('scope');
        }

        // Query controls
        $controls = DB::table('controls as c1')
            ->select(
                DB::raw(
                    '
                    c1.id AS control_id,
                    c1.name,
                    measures.clause,
                    c1.scope as scope,
                    control_measure.measure_id,
                    measures.domain_id,
                    c1.plan_date,
                    c1.realisation_date,
                    c1.score as score,
                    c2.plan_date as next_date,
                    c2.id AS next_id'
                )
            )
            ->join('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->join(
                'control_measure',
                'control_measure.control_id',
                '=',
                'c2.id'
            )
            ->join('measures', 'control_measure.measure_id', '=', 'measures.id')
            ->whereIn('c2.status', [0, 1]);
        if ($cur_scope !== null) {
            $controls = $controls->where('c1.scope', '=', $cur_scope);
        }
        $controls = $controls->orderBy('clause')->orderBy('scope')->get();

        // return
        return view('/radar/controls')
            ->with('scopes', $scopes)
            ->with('controls', $controls)
            ->with('domains', $domains);
    }

    public function attributes()
    {
        // Not API and auditee
        abort_if(
            Auth::User()->role === 4 || Auth::User()->role === 5,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get all attributes
        $attributes = DB::table('attributes')->orderBy('name')->get();

        // Controls made
        $controls = DB::table('controls as c1')
            ->select([
                'c2.id',
                'c2.name',
                'c2.attributes',
                'c2.realisation_date',
                'c2.score',
            ])
            ->join('controls as c2', 'c1.id', '=', 'c2.next_id')
            ->where('c1.status', '=', 0)
            ->orderBy('c2.id')
            ->get();

        // Fetch measures for all controls in one query
        $controlMeasures = DB::table('control_measure')
            ->select(['control_id', 'measure_id', 'clause'])
            ->leftjoin('measures', 'measures.id', '=', 'measure_id')
            ->whereIn('control_id', $controls->pluck('id'))
            ->orderBy('clause')
            ->get();

        // Group measures by control_id
        $measuresByControlId = $controlMeasures->groupBy('control_id');

        // map clauses
        foreach ($controls as $control) {
            $control->measures = $measuresByControlId
                ->get($control->id, collect())
                ->map(function ($controlMeasure) {
                    return [
                        'id' => $controlMeasure->measure_id,
                        'clause' => $controlMeasure->clause,
                    ];
                });
        }

        // return
        return view('radar.attributes')
            ->with('attributes', $attributes)
            ->with('controls', $controls);
    }

    /**
     * Show a measurement for planing
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function plan(int $id)
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->role !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

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

        // get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // get all groups
        $groups = DB::table('user_groups')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Owners list
        $owners = collect();
        foreach ($users as $user) {
            $owners->put('USR_' . $user->id, $user->name);
        }
        foreach ($groups as $group) {
            $owners->put('GRP_' . $group->id, $group->name);
        }

        // get all measures
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // Get current measures
        $measures = DB::table('control_measure')
            ->select('measure_id')
            ->where('control_id', $id)
            ->get()
            ->pluck('measure_id');

        // Get al active scopes
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope');

        return view('controls.plan', compact('control'))
            ->with('years', $years)
            ->with('day', date('d', strtotime($control->plan_date)))
            ->with('month', date('m', strtotime($control->plan_date)))
            ->with('year', date('Y', strtotime($control->plan_date)))
            ->with('all_measures', $all_measures)
            ->with('measures', $measures)
            ->with('scopes', $scopes)
            ->with('owners', $owners);
    }

    /**
     * unPlan a control.
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function unplan(Request $request)
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->role !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get the control
        $control = Control
            ::whereIn('status', [0, 1])
                ->where('id', '=', $request->id)
                ->get()
                ->first();

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Break previous link
        $prev_control = Control::where('next_id', $control->id)
            ->get()
            ->first();
        if ($prev_control !== null) {
            $prev_control->next_id = null;
            $prev_control->update();
        }

        // Delete measure_control items
        $control->measures()->detach();

        // Delete control
        $control->delete();

        return redirect('/alice/index');
    }

    /**
     * Save a control for planing
     *
     * @param  Request $Request
     *
     * @return \Illuminate\Http\Response
     */
    public function doPlan(Request $request)
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->rol !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Validate fields
        $this->validate($request, [
            'plan_date' => 'required',
            'periodicity' => 'required',
        ]);

        // Find the control
        $control = Control::find($request->id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        if ($control->status === 2) {
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.made')])
                ->withInput();
        }

        // Update fields
        $control->plan_date = $request->plan_date;
        $control->periodicity = $request->periodicity;
        $control->save();

        // Sync users
        $users = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'USR_'))
                $users->push(intval(substr($owner, 4)));
        }
        $control->users()->sync($users);

        // Sync groups
        $groups = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'GRP_'))
                $groups->push(intval(substr($owner, 4)));
        }
        $control->groups()->sync($groups);

        // Redirect
        return redirect('/bob/show/' . $request->id);
    }

    public function make(Request $request)
    {
        $id = (int) request('id');

        // Get control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // for (aditee or auditor) only if he is assigne to that control
        abort_if(! $control->canMake(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // get associated documents
        $documents = DB::table('documents')->where('control_id', $id)->get();

        // save control_id in session for document upload
        $request->session()->put('control', $id);

        // compute next control date
        if ($control->periodicity === 0) {
            // Once
            $next_date = null;
        } else {
            $next_date =
                $control->next_date === null
                    ? Carbon::createFromFormat('Y-m-d', $control->plan_date)
                        ->addMonthsNoOverflow($control->periodicity)
                        ->format('Y-m-d')
                    : $control->next_date->format('Y-m-d');
        }

        // return view
        return view('controls.make')
            ->with('control', $control)
            ->with('documents', $documents)
            ->with('next_date', $next_date);
    }

    /**
     * Make a Control
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function doMake(Request $request)
    {
        // Not for API
        abort_if(
            Auth::User()->role === 4,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

        // Control fields
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // for (aditee or auditor) only if he is assigne to that control
        abort_if(! $control->canMake(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Score must be set
        if ((request('score') === null) || (request('score') === 0)) {
            return back()
                ->withErrors(['score' => 'score not set'])
                ->withInput();
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->realisation_date = request('realisation_date');

        // only admin and user can update the plan_date and action_plan
        if (Auth::User()->role === 1 || Auth::User()->role === 2) {
            $control->plan_date = request('plan_date');
            $control->action_plan = request('action_plan');

            // Create an action plan ?
            if ($request->has('add_action_plan')) {
                $action = new Action();
                $action->name = $control->name;
                $action->scope = $control->scope;
                $action->status = 0;
                $action->cause = $control->observations;
                $action->remediation = $control->action_plan;
                $action->due_date = request('next_date');
                $action->control_id = $control->id;
                $action->save();

                // Sync measures
                $measures = DB::table('control_measure')
                    ->select('measure_id')
                    ->where('control_id', $control->id)
                    ->pluck('measure_id');
                $action->measures()->sync($measures);

                // Sync owners
                $owners = DB::table('control_user')
                    ->select('user_id')
                    ->where('control_id', $control->id)
                    ->pluck('user_id');
                $action->owners()->sync($owners);
            }
        } else {
            $control->realisation_date = date('Y-m-d', strtotime('today'));
        }

        // Auditee -> propose control
        if (Auth::User()->role === 5) {
            $control->status = 1;
        } else {
            // set status done
            $control->status = 2;

            // if there is no next control and control not once
            if (($control->next_id === null) && ($control->periodicity !== 0)) {
                // create a new control
                $new_control = $control->replicate();
                $new_control->observations = null;
                $new_control->realisation_date = null;
                $new_control->note = null;
                $new_control->score = null;
                $new_control->status = 0;
                // only admin and user can update the plan_date, realisation_date and action_plan
                if (Auth::User()->role === 1 || Auth::User()->role === 2) {
                    $new_control->plan_date = request('next_date');
                } else {
                    $new_control->plan_date = date(
                        'Y-m-d',
                        strtotime(
                            $control->periodicity . ' months',
                            strtotime($control->plan_date)
                        )
                    );
                }
                $new_control->save();

                // Set owners
                $new_control->users()->sync($control->users->pluck('id'));

                // Set groups
                $new_control->groups()->sync($control->groups->pluck('id'));

                // Set measures
                $new_control->measures()->sync($control->measures->pluck('id'));

                // make link
                $control->next_id = $new_control->id;
            }
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
        abort_if(
            Auth::User()->role !== 1,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get the control
        $control = Control::find($request->id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $this->validate(
            $request,
            [
                'name' => 'required|min:3|max:255',
                'scope' => 'max:32',
                'objective' => 'required',
                'plan_date' => 'required',
                'periodicity' => 'required|integer',
            ]
        );

        $control->name = request('name');
        $control->scope = request('scope');
        $control->objective = request('objective');
        $control->attributes =
            request('attributes') !== null
                ? implode(' ', request('attributes'))
                : null;
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

        // Sync users
        $users = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'USR_'))
                $users->push(intval(substr($owner, 4)));
        }
        $control->users()->sync($users);

        // Sync groups
        $groups = collect();
        foreach($request->input('owners', []) as $owner) {
            if (str_starts_with($owner,'GRP_'))
                $groups->push(intval(substr($owner, 4)));
        }
        $control->groups()->sync($groups);

        // Sync Measures
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
        // Not for API
        abort_if(
            Auth::User()->role === 4,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) $request->get('id');

        // Get the control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // For aditee only if he is assigned to that control
        abort_if(! $control->canMake(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Control already made ?
        if ($control->status === 2) {
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.made')])
                ->withInput();
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score') === 0 ? null : request('score');

        // only admin and user can update the plan_date and action_plan
        if (Auth::User()->role === 1 || Auth::User()->role === 2) {
            $control->plan_date = request('plan_date');
            $control->action_plan = request('action_plan');
            // do not save the realisation date as it is in draft
        }
        $control->save();

        return redirect('/bob/show/' . $id);
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
            ! (Auth::User()->role === 1 || Auth::User()->role === 2),
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
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.made')])
                ->withInput();
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
            ! (Auth::User()->role === 1 || Auth::User()->role === 2),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) request('id');

        // Score must be set
        if ((request('score') === null) || (request('score') === 0)) {
            return back()
                ->withErrors(['score' => 'score not set'])
                ->withInput();
        }

        // Control fields
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Control already made ?
        if ($control->status === 2) {
            return back()
                ->withErrors(['msg' => trans('cruds.control.error.made')])
                ->withInput();
        }

        $control->observations = request('observations');
        $control->note = request('note');
        $control->score = request('score');
        $control->realisation_date = request('realisation_date');
        $control->plan_date = request('plan_date');
        $control->action_plan = request('action_plan');

        // create a new control
        $new_control = $control->replicate();
        $new_control->status = 0;
        $new_control->observations = null;
        $new_control->realisation_date = null;
        $new_control->note = null;
        $new_control->score = null;
        $new_control->plan_date = request('next_date');

        // Save new control
        $new_control->save();

        // Clone measures
        $new_control->measures()->sync($control->measures->pluck('id')->toArray());

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
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->rol !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return Excel::download(
            new ControlsExport(),
            trans('cruds.control.title') .
                '-' .
                now()->format('Y-m-d Hi') .
                '.xlsx'
        );
    }

    public function tempo(Request $request)
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->rol !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get controls
        if ($request->id !== null) {
            // Measure ID
            $id = (int) $request->id;
            // find associate control
            $measure = Measure::find($id);

            // Measure not found
            abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

            // Get all date and score for that measure
            $controls = DB::Table('controls')
                ->select('id', 'realisation_date', 'note', 'controls.score')
                ->whereNotNull('next_id')
                ->leftjoin('control_measure', 'control_id', '=', 'controls.id')
                ->where('measure_id', $id)
                ->orderBy('realisation_date')
                ->get();
        } else {
            $controls = null;
        }

        $measures = DB::Table('measures')
            ->select('id', 'name', 'clause')
            ->orderby('name')->get();

        // return view
        return view('radar.measures')
            ->with('controls', $controls)
            ->with('measures', $measures);
    }

    public function template(Request $request)
    {
        // Not for API
        abort_if(
            Auth::User()->role === 4,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $id = (int) $request->id;

        // find associate control
        $control = Control::find($id);

        // Control not found
        abort_if($control === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // for (aditee or auditor) only if he is assigne to that control
        abort_if(! $control->canMake(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get template file
        $template_filename = storage_path('app/models/control_.docx');
        if (! file_exists($template_filename)) {
            $template_filename = storage_path('app/models/control_'.Auth::User()->language.'.docx');
            if (! file_exists($template_filename)) {
                $template_filename = storage_path('app/models/control_en.docx');
            }
        }

        // create templateProcessor
        $templateProcessor = new PhpWordTemplateProcessor($template_filename);

        // Replace names
        $clauses = $control->measures->map(function ($measure) {
            return $measure->clause;
        })->implode(', ');

        $templateProcessor->setValue('ref', $clauses);
        $templateProcessor->setValue('name', $control->name);
        $templateProcessor->setValue('scope', $control->scope);
        $templateProcessor->setValue('attributes', $control->attributes);

        $templateProcessor->setComplexValue('objective', self::string2Textrun($control->objective));
        $templateProcessor->setComplexValue('input', self::string2Textrun($control->input));
        $templateProcessor->setComplexValue('model', self::string2Textrun($control->model));
        $templateProcessor->setComplexValue('observations', self::string2Textrun(urldecode($request->observations)));
        $templateProcessor->setValue('date', Carbon::today()->format('d/m/Y'));

        // save a copy
        $filepath = storage_path(
            'templates/control-' .
                $control->id .
                '-' .
                now()->format('Ymd') .
                '.docx'
        );

        // if (file_exists($filepath)) unlink($filepath);
        $templateProcessor->saveAs($filepath);

        // return
        return response()->download($filepath);
    }

    private static function string2Textrun(?string $str)
    {
        if ($str === null) {
            return new \PhpOffice\PhpWord\Element\TextRun();
        }

        $textlines = explode("\n", $str);
        $textrun = new \PhpOffice\PhpWord\Element\TextRun();

        // Fonction d'échappement XML
        $escape = fn ($text) => htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $textrun->addText($escape(array_shift($textlines)));

        foreach ($textlines as $line) {
            $textrun->addTextBreak();
            $textrun->addText($escape($line));
        }

        return $textrun;
    }
}
