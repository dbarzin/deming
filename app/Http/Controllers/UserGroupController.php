<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $all_users = User::select('id', 'name')->orderBy('name')->get();

        return view('groups.create', compact('all_users'));
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
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'name' => 'required|min:1|max:90',
        ]);

        // Create and save the new user
        $userGroup = new UserGroup();
        $userGroup->name = $request->input('name');
        $userGroup->description = $request->input('description');
        $userGroup->save();

        // Sync users
        $userGroup->users()->sync($request->input('users', []));

        return redirect('/groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  UserGroup $group
     *
     * @return \Illuminate\Http\Response
     */
    public function show(UserGroup $group)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $group = UserGroup::find($id);
        abort_if($group === null, Response::HTTP_NOT_FOUND, '404 Not Found');

        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UserGroup $group
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(UserGroup $group)
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get Group
        abort_if($group === null, Response::HTTP_NOT_FOUND, '404 Not Found');

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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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

    /**
     * Export the list of users.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new UserGroupsExport(), 'groups.xlsx');
    }
}
