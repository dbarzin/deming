<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\Measure;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Only for administrator role
        abort_if(Auth::user()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userGroups = UserGroup::orderBy('name')->get();

        return view('groups.index')->with('groups', $userGroups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Only for administrator role
        abort_if(Auth::user()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $all_users = User::select('id', 'name')->orderBy('name')->get();

        // Get Measures
        $all_measures = Measure::select('id', 'name')->whereNull('realisation_date')->orderBy('name')->get();

        return view('groups.create', compact('all_users', 'all_measures'));
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
        // Only for administrator role
        abort_if(Auth::user()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'name' => 'required|unique:user_groups|min:1|max:90',
            'type' => 'required|in:' . implode(',', UserGroup::TYPES),
        ]);

        // Create and save the new user
        $group = new UserGroup();
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->type = $request->input('type');
        $group->save();

        // Sync users
        $group->users()->sync($request->input('users', []));

        // Sync measures
        $group->measures()->sync($request->input('measures', []));

        return redirect('/groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  UserGroup $group
     *
     * @return \Illuminate\View\View
     */
    public function show(UserGroup $group)
    {
        // Only for administrator role
        abort_if(
            (Auth::user() === null) || (Auth::user()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UserGroup $group
     *
     * @return \Illuminate\View\View
     */
    public function edit(UserGroup $group)
    {
        // Only for administrator role
        abort_if(
            (Auth::user() === null) || (Auth::user()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get Users
        $all_users = User::select('id', 'name')->orderBy('name')->get();

        // Get Measures
        $all_measures = Measure::select('id', 'name')->whereNull('realisation_date')->orderBy('name')->get();

        return view('groups.edit', compact('group', 'all_users', 'all_measures'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  UserGroup $group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, UserGroup $group)
    {
        // Only for administrator role
        abort_if(Auth::user()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'name' => 'required|min:1|max:90',
            'type' => 'required|in:' . implode(',', UserGroup::TYPES),
        ]);

        // Update user information
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->type = $request->input('type');
        $group->update();

        // Sync users
        $group->users()->sync($request->input('users', []));

        // Sync measures: the form only lists open measures, so keep the
        // completed / proposed ones already linked to the group
        $closedMeasures = DB::table('measure_user_group')
            ->join('measures', 'measures.id', '=', 'measure_user_group.measure_id')
            ->where('measure_user_group.user_group_id', $group->id)
            ->whereNotNull('measures.realisation_date')
            ->pluck('measure_user_group.measure_id')
            ->all();
        $group->measures()->sync(array_merge($request->input('measures', []), $closedMeasures));

        return redirect('/groups');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  UserGroup $group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(UserGroup $group)
    {
        // Only for administrator role
        abort_if(Auth::user()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Remove users from controls
        $group->measures()->detach();

        // Remove users from group
        $group->users()->detach();

        // Dete userGroup
        $group->delete();

        return redirect('/groups');
    }
}
