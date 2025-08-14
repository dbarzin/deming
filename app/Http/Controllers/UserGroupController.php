<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $all_users = User::select('id', 'name')->orderBy('name')->get();

        // Get Controls
        $all_controls = Control::select('id', 'name')->whereNull('realisation_date')->orderBy('name')->get();

        return view('groups.create', compact('all_users', 'all_controls'));
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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'name' => 'required|unique:user_groups|min:1|max:90',
        ]);

        // Create and save the new user
        $group = new UserGroup();
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->save();

        // Sync users
        $group->users()->sync($request->input('users', []));

        // Sync controls
        $group->controls()->sync($request->input('controls', []));

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
            (Auth::User() === null) || (Auth::User()->role !== 1),
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
            (Auth::User() === null) || (Auth::User()->role !== 1),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        // Get Users
        $all_users = User::select('id', 'name')->orderBy('name')->get();

        // Get Controls
        $all_controls = Control::select('id', 'name')->whereNull('realisation_date')->orderBy('name')->get();

        return view('groups.edit', compact('group', 'all_users', 'all_controls'));
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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'name' => 'required|min:1|max:90',
        ]);

        // Update user information
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->update();

        // Sync users
        $group->users()->sync($request->input('users', []));

        // Sync controls
        $group->controls()->sync($request->input('controls', []));

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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Remove users from controls
        $group->controls()->detach();

        // Remove users from group
        $group->users()->detach();

        // Dete userGroup
        $group->delete();

        return redirect('/groups');
    }
}
