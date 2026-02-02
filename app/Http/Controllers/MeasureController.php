<?php

namespace App\Http\Controllers;

use App\Exports\MeasuresExport;
use App\Models\Control;
use App\Models\Domain;
use App\Models\Measure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Mercator\Core\Models\Task;

class MeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        // Not for Auditor, API and auditee
        abort_if(Auth::User()->isAPI(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $domains = Domain::All();

        $domain = $request->get('domain');
        if ($domain !== null) {
            if ($domain === '0') {
                $request->session()->forget('domain');
                $domain = null;
            }
        } else {
            $domain = $request->session()->get('domain');
        }

        $measures = DB::table('measures')
            ->select(
                [
                    'measures.id',
                    'measures.domain_id',
                    'measures.clause',
                    'measures.name',
                    'domains.title',
                ]
            )
            ->join('domains', 'domains.id', '=', 'measures.domain_id');

        // Filtrer les mesures uniquement si l'utilisateur est Auditee
        if (Auth::user()->isAuditee()) {
            $userId = Auth::id();

            $measures->whereExists(function($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('control_measure')
                    ->whereColumn('control_measure.measure_id', 'measures.id')
                    ->where(function($subQuery) use ($userId) {
                        // Mesures liées à des contrôles assignés directement
                        $subQuery->whereExists(function($q) use ($userId) {
                            $q->select(DB::raw(1))
                                ->from('control_user')
                                ->whereColumn('control_user.control_id', 'control_measure.control_id')
                                ->where('control_user.user_id', $userId);
                        })
                            // OU mesures liées à des contrôles assignés via un groupe
                            ->orWhereExists(function($q) use ($userId) {
                                $q->select(DB::raw(1))
                                    ->from('control_user_group')
                                    ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                                    ->whereColumn('control_user_group.control_id', 'control_measure.control_id')
                                    ->where('user_user_group.user_id', $userId);
                            });
                    });
            });

            // Compter uniquement les contrôles assignés à l'utilisateur
            $measures->addSelect(
                ['control_count' => DB::table('controls')
                    ->selectRaw('count(*) as controls_count')
                    ->leftjoin('control_measure', 'control_measure.measure_id', 'measures.id')
                    ->whereColumn('control_measure.control_id', 'controls.id')
                    ->whereIn('controls.status', [0,1])
                    ->where(function($q) use ($userId) {
                        $q->whereExists(function($subQ) use ($userId) {
                            $subQ->select(DB::raw(1))
                                ->from('control_user')
                                ->whereColumn('control_user.control_id', 'controls.id')
                                ->where('control_user.user_id', $userId);
                        })
                            ->orWhereExists(function($subQ) use ($userId) {
                                $subQ->select(DB::raw(1))
                                    ->from('control_user_group')
                                    ->join('user_user_group', 'user_user_group.user_group_id', '=', 'control_user_group.user_group_id')
                                    ->whereColumn('control_user_group.control_id', 'controls.id')
                                    ->where('user_user_group.user_id', $userId);
                            });
                    }),
                ]
            );
        } else {
            // Pour les autres rôles, compter tous les contrôles
            $measures->addSelect(
                ['control_count' => DB::table('controls')
                    ->selectRaw('count(*) as controls_count')
                    ->leftjoin('control_measure', 'control_measure.measure_id', 'measures.id')
                    ->whereColumn('control_measure.control_id', 'controls.id')
                    ->whereIn('controls.status', [0,1]),
                ]
            );
        }

        if ($domain !== null) {
            $measures->where('measures.domain_id', $domain);
            $request->session()->put('domain', $domain);
        }

        $measures = $measures->orderBy('clause')->get();

        // return
        return view('measures.index')
            ->with('measures', $measures)
            ->with('domains', $domains);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        // Only Admin and User can create a measure
        abort_if(!Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get the list of domains
        $domains = Domain::All();

        // get all attributes
        $values = [];
        $attributes = DB::table('attributes')->select('values')
            ->union(DB::table('measures')
                ->select(DB::raw('attributes as value')))
            ->get();
        foreach ($attributes as $key) {
            foreach (explode(' ', $key->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($values, $value);
                }
            }
        }
        sort($values);
        $values = array_unique($values);

        // for clone action
        $measure = null;

        // store it in the response
        return view('measures.create', compact('measure', 'values', 'domains'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Only Admin and User can create a measure
        abort_if(!Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'domain_id' => 'required',
                'clause' => 'required|min:3|max:30|unique:measures,clause',
                'name' => 'required|min:5|max:255',
                'objective' => 'required',
            ]
        );

        $request['attributes'] = implode(' ', $request->get('attributes') !== null ? $request->get('attributes') : []);

        $measure = Measure::query()->create($request->all());
        // $measure = new Measure();
        // $measure->domain_id = request('domain_id');
        // $measure->clause = request('clause');
        // $measure->name = request('name');
        // $measure->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        // $measure->objective = request('objective');
        // $measure->input = request('input');
        // $measure->model = request('model');
        // $measure->indicator = request('indicator');
        // $measure->action_plan = request('action_plan');
        // $measure->save();

        $request->session()->put('domain', $measure->domain_id);

        return redirect('/alice/index');
    }

    /**
     * Display a measure
     *
     * @param  int $id
     *
     * @return View
     */
    public function show(int $id)
    {
        // Not for API
        abort_if(Auth::User()->isAPI(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // user must have one control assigned
        abort_if(
            (Auth::User()->isAuditee()) &&
            ! DB::table('controls')
                ->where('measure_id', $id)
                ->join('control_measure', 'control_measure.control_id', '=', 'controls.id')
                ->join('control_user', 'control_user.control_id', '=', 'controls.id')
                ->where('control_user.user_id', Auth::User()->id)
                ->exists(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $measure = Measure::find($id);

        // not found
        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Get associate controls
        $controls = DB::table('controls')
            ->select('controls.id', 'controls.name', 'controls.scope', 'score', 'controls.status', 'realisation_date', 'plan_date')
            ->join('control_measure', 'control_measure.control_id', '=', 'controls.id')
            ->leftjoin('actions', 'actions.control_id', '=', 'controls.id')
            ->where('control_measure.measure_id', $id)
            ->get();

        return view('measures.show')
            ->with('measure', $measure)
            ->with('controls', $controls);
    }

    /**
     * Clone measure.
     *
     * @param  int $id
     *
     * @return View
     */
    public function edit(int $id)
    {
        // Only Admin and User can edit a measure
        abort_if(!Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $measure = Measure::find($id);

        // not found
        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // get the list of domains
        $domains = Domain::All();

        // get all attributes
        $values = [];
        $attributes = DB::table('attributes')->select('values')
            ->union(DB::table('measures')
                ->select(DB::raw('attributes as value')))
            ->get();
        foreach ($attributes as $key) {
            foreach (explode(' ', $key->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($values, $value);
                }
            }
        }
        sort($values);
        $values = array_unique($values);

        return view('measures.edit', compact('measure', 'values', 'domains'));
    }

    /**
     * Clone measure.
     *
     * @param  int $id
     *
     * @return View
     */
    public function clone(int $id)
    {
        // Only Admin and User can clone a measure
        abort_if(!Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $measure = Measure::find($id);

        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        $domains = Domain::all();

        // Récupération de toutes les valeurs d'attributs disponibles
        $values = [];
        $attributes = DB::table('attributes')->select('values')
            ->union(DB::table('measures')
                ->select(DB::raw('attributes as value')))
            ->get();

        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                if (strlen($value) > 0) {
                    $values[] = $value;
                }
            }
        }

        $values = array_unique($values);
        sort($values);

        // Extraire les attributs sélectionnés de la mesure existante
        $selectedAttributes = array_filter(
            explode(' ', $measure->attributes ?? ''),
            fn ($val) => strlen($val) > 0
        );

        return view(
            'measures.create',
            compact('measure', 'values', 'domains', 'selectedAttributes')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Only Admin and User can update a measure
        abort_if(!Auth::User()->isAdmin() && !Auth::User()->isUser(),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'domain_id' => 'required',
                'clause' => 'required|min:3|max:30',
                'name' => 'required|min:5',
                'objective' => 'required',
            ]
        );

        // find measure
        $measure = Measure::query()->find($request->id);

        // not found
        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // update measure
        $request['attributes'] = implode(' ', $request->get('attributes') !== null ? $request->get('attributes') : []);
        $measure->update($request->all());

        // return to view measure
        return redirect('/alice/show/'.$measure->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Check measure exists
        abort_if(
            ! DB::table('measures')->where('id', $request->id)->exists(),
            Response::HTTP_NOT_FOUND,
            '404 Not Found'
        );

        // Has controls ?
        if (DB::table('measures')
            ->where('id', $request->id)
            ->join('control_measure', 'measures.id', 'control_measure.measure_id')
            ->exists()) {
            return back()
                ->withErrors(['msg' => 'There are controls associated with this measure !'])
                ->withInput();
        }

        // Destroy it
        Measure::destroy($request->id);

        return redirect('/alice/index');
    }

    /**
     * Plan a measure.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function plan(Request $request): View
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $measure = Measure::find($request->id);

        // Control not found
        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // get all clauses
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // get all measures for this measure
        $measures = [$request->id];

        // get all active scopes
        $scopes = Control::query()
            ->whereNotNull('scope')
            ->where('scope', '!=', '')
            ->whereIn('status', [0, 1])
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

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

        // get all attributes
        $values = [];
        $attributes = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($values, $value);
                }
            }
            sort($values);
            $values = array_unique($values);
        }

        return view(
            'measures.plan',
            compact(
                'measure',
                'all_measures',
                'measures',
                'scopes',
                'owners',
                'values'
            )
        );
    }

    /**
     * Activate a measure
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request) : RedirectResponse
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'plan_date' => 'required',
                'periodicity' => 'required',
                'measures' => 'array|min:1',
            ]
        );

        // create a new control
        $control = new Control();
        $control->name = $request->get('name');
        $control->scope = $request->get('scope');
        $control->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        $control->objective = $request->get('objective');
        $control->input = $request->get('input');
        $control->model = $request->get('model');
        $control->indicator = $request->get('indicator');
        $control->action_plan = $request->get('action_plan');
        $control->periodicity = $request->get('periodicity');
        $control->plan_date = $request->get('plan_date');

        // Save it
        $control->save();

        // Sync users
        $users = collect();
        foreach ($request->input('owners', []) as $owner) {
            if (str_starts_with($owner, 'USR_')) {
                $users->push(intval(substr($owner, 4)));
            }
        }
        $control->users()->sync($users);

        // Sync groups
        $groups = collect();
        foreach ($request->input('owners', []) as $owner) {
            if (str_starts_with($owner, 'GRP_')) {
                $groups->push(intval(substr($owner, 4)));
            }
        }
        $control->groups()->sync($groups);

        // Sync measures
        $control->measures()->sync($request->input('measures', []));

        // return to the list of measures
        return redirect('/alice/index');
    }

    /**
     * Disable a measure
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable(Request $request)
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $control_id = DB::table('controls')
            ->select('id')
            ->where('measure_id', '=', $request->id)
            ->where('status', [0,1])
            ->first()
            ->id;

        if ($control_id !== null) {
            // break link
            Control::where('next_id', $control_id)
                ->update(['next_id' => null]);
            // delete control
            Control::where('id', $control_id)
                ->delete();
        }

        // return to the list of measures
        return redirect('/alice/index');
    }

    /**
     * Export all Measure in xlsx
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return Excel::download(new MeasuresExport(), trans('cruds.measure.title') . '-' . now()->format('Y-m-d Hi') . '.xlsx');
    }
}
