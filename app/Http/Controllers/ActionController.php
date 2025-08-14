<?php

namespace App\Http\Controllers;

// Models
use App\Exports\ActionsExport;
// Export
use App\Models\Action;
// Laravel
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Admin, user, auditor or auditee
        abort_if(
            ! ((Auth::User()->role === 1) ||
                (Auth::User()->role === 2) ||
                (Auth::User()->role === 3) ||
                (Auth::User()->role === 5)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get type filter
        $type = $request->get('type');
        if ($type !== null) {
            $request->session()->put('type', $type);
        } elseif ($request->has('type')) {
            $request->session()->forget('type');
        } else {
            $type = $request->session()->get('type');
        }

        // Get status filter
        $status = $request->get('status');
        if ($status !== null) {
            $request->session()->put('status', $status);
        } elseif ($request->has('status')) {
            $request->session()->forget('status');
        } else {
            $status = $request->session()->get('status');
            if ($status === null) {
                $status = '0';
                $request->session()->put('status', $status);
            }
        }

        // Get scope filter
        $scope = $request->get('scope');
        if ($scope !== null) {
            $request->session()->put('ascope', $scope);
        } elseif ($request->has('scope')) {
            $request->session()->forget('ascope');
        } else {
            $scope = $request->session()->get('ascope');
        }

        // Build query
        $actions = DB::table('actions')
            ->leftjoin('controls', 'control_id', '=', 'controls.id');

        // filter on type
        if (($type !== null) && (strlen($type) > 0)) {
            $actions = $actions->where('type', $type);
        }

        // filter on status
        if ($status === '0') {
            $actions = $actions->where('actions.status', 0);
        } elseif ($status === '1') {
            $actions = $actions->where('actions.status', 1);
        }

        // filter on scope
        if (($scope !== null) && (strlen($scope) > 0)) {
            $actions = $actions->where('actions.scope', $scope);
        }

        // filter on auditee controls
        if (Auth::User()->role === 5) {
            $actions = $actions
                ->leftjoin('action_user', 'controls.id', '=', 'control_user.control_id')
                ->where('action_user.user_id', '=', Auth::User()->id);
        }

        // Query DB
        $actions = $actions->select(
            [
                'actions.id',
                'actions.reference',
                'actions.type',
                'actions.name',
                'actions.criticity',
                'actions.scope',
                'actions.cause',
                'actions.status',
                'actions.progress',
                'actions.due_date',
            ]
        )->get();

        // Get scopes
        $scopes = DB::table('actions')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('status', '<>', 3)
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

        // return
        return view('actions.index')
            ->with('scopes', $scopes)
            ->with('actions', $actions);
    }

    /**
     * Save an action plan
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        abort_if(
            ! ((Auth::User()->role === 1) || (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'name' => 'required|min:3|max:255',
            ]
        );

        // Get the action plan
        $id = (int) $request->get('id');
        $action = Action::find($id);

        // Action not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Update fields
        $action->reference = request('reference');
        $action->type = request('type');
        $action->due_date = request('due_date');
        $action->name = request('name');
        $action->progress = request('progress');
        $action->scope = request('scope');
        $action->cause = request('cause');
        $action->remediation = request('remediation');
        $action->status = request('status');
        if ($action->status === 0) {
            $action->close_date = null;
        } else {
            $action->close_date = request('close_date');
        }
        $action->justification = request('justification');
        $action->update();

        // Sync measures
        $action->measures()->sync($request->input('measures', []));

        // Sync owners
        $action->owners()->sync($request->input('owners', []));

        return redirect("/action/show/{$id}");
    }

    /**
     * Update an action plan
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        abort_if(
            ! ((Auth::User()->role === 1) || (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get the action plan
        $id = (int) $request->get('id');
        $action = Action::find($id);

        // Action not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Update fields
        $action->remediation = request('remediation');
        $action->progress = request('progress');
        $action->update();

        return redirect('/actions');
    }

    /**
     * Display the action.
     *
     * @param  int $id
     *
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2) ||
            (Auth::User()->role === 3)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // TODO : check for user

        // Get the action
        $action = Action::find($id);

        // Control not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Return
        return view('actions.show')
            ->with('action', $action);
    }

    /**
     * Edit the action.
     *
     * @param  int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2) ||
            (Auth::User()->role === 3)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // TODO : check for user

        // Get the action
        $action = Action::find($id);

        // Control not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Get all scopes
        $scopes = DB::table('actions')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('status', '<>', 2)
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

        // Get all measures
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        $measures = DB::table('control_measure')
            ->select('measure_id')
            ->where('control_id', $id)
            ->pluck('measure_id')
            ->toArray();

        // Get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Return
        return view('actions.edit')
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('measures', $measures)
            ->with('users', $users)
            ->with('action', $action);
    }

    /**
     * Create an action.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get all scopes
        $scopes = DB::table('actions')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('status', '<>', 2)
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

        // Get all measures
        $all_measures = DB::table('measures')
            ->select('id', 'clause')
            ->orderBy('id')
            ->get();

        // Get users
        $users = DB::table('users')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Return
        return view('actions.create')
            ->with('scopes', $scopes)
            ->with('all_measures', $all_measures)
            ->with('users', $users);
    }

    /**
     * Store the action.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $action = new Action();

        // Set fields
        $action->reference = request('reference');
        $action->type = request('type');
        $action->due_date = request('due_date');
        $action->name = request('name');
        $action->progress = request('progress');
        $action->scope = request('scope');
        $action->cause = request('cause');
        $action->remediation = request('remediation');
        // for Postegres
        $action->criticity = 0;
        $action->status = 0;

        // Save
        $action->save();

        // Sync measures
        $action->measures()->sync($request->input('measures', []));

        // Sync owners
        $action->owners()->sync($request->input('owners', []));

        // Return
        return redirect('/actions');
    }

    /**
     * Show Close action.
     *
     * @param  int $id
     *
     * @return \Illuminate\View\View
     */
    public function close(int $id)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2) ||
            (Auth::User()->role === 3)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // TODO : check for user

        // Get the action
        $action = Action::find($id);

        // Control not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Return
        return view('actions.close')
            ->with('action', $action);
    }

    /**
     * Show Close action.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doClose(Request $request)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2) ||
            (Auth::User()->role === 3)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // TODO : check for user

        // Get the action
        $id = request('id');
        $action = Action::find($id);

        // Control not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // Get fields
        $action->status = request('status');
        $action->close_date = request('close_date');
        $action->justification = request('justification');

        // Save action
        $action->save();

        // Return
        return redirect('/actions');
    }

    public function export()
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->role !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return Excel::download(
            new ActionsExport(),
            trans('cruds.action.title') .
                '-' .
                now()->format('Y-m-d Hi') .
                '.xlsx'
        );
    }

    public function delete()
    {
        // For administrators and users only
        abort_if(
            Auth::User()->role !== 1 && Auth::User()->role !== 2,
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get the action plan
        $id = (int) request('id');
        $action = Action::find($id);

        // Action not found
        abort_if($action === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        // delete links
        DB::table('action_measure')->where('action_id', $action->id)->delete();

        // relete links to owners
        $action->owners()->detach();

        // delete
        $action->delete();

        // Return
        return redirect('/actions');
    }

    /**
     * Display the actions chart
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function chart(Request $request)
    {
        abort_if(
            ! ((Auth::User()->role === 1) ||
            (Auth::User()->role === 2)),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Start
        $start = $request->get('start');
        if ($start === null) {
            $start = Carbon::now()->startOfYear()->toDateString();
        }
        // End
        $end = $request->get('start');
        if ($end === null) {
            $end = Carbon::now()->today()->toDateString();
        }
        // Get scope
        $scope = $request->get('scope');
        if ($scope !== null) {
            $request->session()->put('scope', $scope);
        } else {
            if ($request->has('scope')) {
                $request->session()->forget('scope');
            } else {
                $scope = $request->session()->get('scope');
            }
        }

        // Get scopes
        $scopes = DB::table('actions')
            ->select('scope')
            ->whereNotNull('scope')
            ->where('status', '<>', 3)
            ->distinct()
            ->orderBy('scope')
            ->pluck('scope')
            ->toArray();

        // Get data
        $types = [1, 2, 3, 4];
        $data = [];

        foreach ($types as $type) {
            $count_open = Action::where('type', $type)
                ->where('status', 0)
                ->where(function ($query) use ($start) {
                    $query->whereDate('close_date', '>', $start)
                        ->orWhereNull('close_date');
                })
                ->where(function ($query) use ($end) {
                    $query->whereDate('close_date', '<', $end)
                        ->orWhereNull('close_date');
                })
                ->when(! is_null($scope), function ($query) use ($scope) {
                    $query->where('scope', $scope);
                })
                ->count();
            $count_closed = Action::where('type', $type)
                ->whereIn('status', [1, 2])
                ->where(function ($query) use ($start) {
                    $query->whereDate('close_date', '>', $start)
                        ->orWhereNull('close_date');
                })
                ->where(function ($query) use ($end) {
                    $query->whereDate('close_date', '<', $end)
                        ->orWhereNull('close_date');
                })
                ->when(! is_null($scope), function ($query) use ($scope) {
                    $query->where('scope', $scope);
                })
                ->count();

            $data[] = [
                'type' => $type,
                'open' => $count_open,
                'closed' => $count_closed,
            ];
        }
        // Get Actions in scrope
        $actions = Action
            ::where(function ($query) use ($start) {
                $query->whereDate('close_date', '>', $start)
                    ->orWhereNull('close_date');
            })
                ->where(function ($query) use ($end) {
                    $query->whereDate('close_date', '<', $end)
                        ->orWhereNull('close_date');
                })
                ->when(! is_null($scope), function ($query) use ($scope) {
                    $query->where('scope', $scope);
                })
                ->get();

        // Return
        return view('radar.actions')
            ->with('start', $start)
            ->with('end', $end)
            ->with('scopes', $scopes)
            ->with('actions', $actions)
            ->with('data', $data);
    }
}
