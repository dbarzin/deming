<?php

namespace App\Http\Controllers;

use App\Exports\MeasuresExport;
use App\Models\Control;
use App\Models\Domain;
use App\Models\Measure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
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
            ->join('domains', 'domains.id', '=', 'measures.domain_id')
            ->addSelect(
                ['control_count' => DB::table('controls')
                    ->selectRaw('count(*) as controls_count')
                    ->leftjoin('control_measure', 'control_measure.measure_id', 'measures.id')
                    ->whereColumn('control_measure.control_id', 'controls.id')
                    ->whereIn('controls.status', [0,1]),
                ]
            );

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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // get the list of domains
        $domains = Domain::All();

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
                'domain_id' => 'required',
                'clause' => 'required|min:3|max:30|unique:measures,clause',
                'name' => 'required|min:5|max:255',
                'objective' => 'required',
            ]
        );

        $measure = new Measure();
        $measure->domain_id = request('domain_id');
        $measure->clause = request('clause');
        $measure->name = request('name');
        $measure->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        $measure->objective = request('objective');
        $measure->input = request('input');
        $measure->model = request('model');
        $measure->indicator = request('indicator');
        $measure->action_plan = request('action_plan');

        $measure->save();

        $request->session()->put('domain', $measure->domain_id);

        return redirect('/alice/index');
    }

    /**
     * Display a measure
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        // Not for API
        abort_if(
            (Auth::User()->role === 4),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // user must have one control assigned
        abort_if(
            (Auth::User()->role === 5) &&
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
     * @param  \App\Measure $measure
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
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
        $attributes = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
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
     * @param  \App\Measure $measure
     *
     * @return \Illuminate\Http\Response
     */
    public function clone(int $id)
    {
        // Not for Auditor, API and auditee
        abort_if(
            (Auth::User()->role === 3) ||
            (Auth::User()->role === 4) ||
            (Auth::User()->role === 5),
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
        $attributes = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($values, $value);
                }
            }
        }
        sort($values);
        $values = array_unique($values);

        // transform to array
        $measure->attributes = explode(' ', $measure->attributes);

        return view('measures.create', compact('measure', 'values', 'domains'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Measure             $measure
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
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
                'domain_id' => 'required',
                'clause' => 'required|min:3|max:30',
                'name' => 'required|min:5',
                'objective' => 'required',
            ]
        );

        // find measure
        $measure = Measure::find($request->id);

        // not found
        abort_if($measure === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // update measure
        $measure->domain_id = request('domain_id');
        $measure->name = request('name');
        $measure->clause = request('clause');
        $measure->attributes = request('attributes') !== null ? implode(' ', request('attributes')) : null;
        $measure->objective = request('objective');
        $measure->input = request('input');
        $measure->model = request('model');
        $measure->indicator = request('indicator');
        $measure->action_plan = request('action_plan');

        $measure->update();

        /*
        // update the current control
        $control = Control::where('measure_id', $measure->id)
            // ->where('realisation_date', null)
            ->whereIn('status', [0,1])
            ->get()->first();
        if ($control !== null) {
            $control->clause = $measure->clause;
            $control->name = $measure->name;
            $control->attributes = $measure->attributes;
            $control->objective = $measure->objective;
            $control->input = $measure->input;
            $control->model = $measure->model;
            $control->indicator = $measure->indicator;
            $control->action_plan = $measure->action_plan;
            $control->save();
        }
        */
        // retun to view measure
        return redirect('/alice/show/'.$measure->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Measure $measure
     *
     * @return \Illuminate\Http\Response
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

        // Get measure
        $measure = Measure::find($request->id);

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
     * @param  \App\Measure $measure
     *
     * @return \Illuminate\Http\Response
     */
    public function plan(Request $request)
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
        $scopes = DB::table('controls')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('scope', '<>', '')
            ->whereIn('status', [0,1])
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->pluck('scope')
            ->toArray();

        // Get all users
        $users = User::orderBy('name')->get();

        // get all attributes
        $values = [];
        $attributes = DB::table('measures')
            ->select('attributes')
            ->get();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', $attribute->attributes) as $value) {
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
                'users',
                'values'
            )
        );
    }

    /**
     * Activate a measure
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
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

        $measure = Measure::find($request->id);

        // create a new control
        $control = new Control();
        $control->name = $request->get('name');
        $control->scope = $request->get('scope');
        $control->attributes = $request->get('attributes[]');
        $control->objective = $request->get('objective');
        $control->input = $request->get('input');
        $control->model = $request->get('model');
        $control->indicator = $request->get('indicator');
        $control->action_plan = $request->get('action_plan');
        $control->periodicity = $request->get('periodicity');
        $control->plan_date = $request->get('plan_date');
        // Save it
        $control->save();

        // Sync onwers
        $control->owners()->sync($request->input('owners', []));

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
     * @return \Illuminate\Http\Response
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
            // ->where('realisation_date', null)
            ->where('status', [0,1])
            ->get()
            ->first()->id;
        if ($control_id !== null) {
            // break link
            // DB::update('UPDATE controls SET next_id = null WHERE next_id =' . $control_id);
            Control::where('next_id', $control_id)
                ->update(['next_id' => null]);
            // delete control
            // DB::delete('DELETE FROM controls WHERE id = ' . $control_id);
            Control::where('id', $control_id)
                ->delete();
        }

        // return to the list of measures
        return redirect('/alice/index');
    }

    /**
     * Export all Measure in xlsx
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
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
