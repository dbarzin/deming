<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Control;
use App\Models\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Only for administrator role
        abort_if(! $this->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = DB::table('users')->orderBy('id', 'asc')->get();

        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Only for administrator role
        abort_if(! $this->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Only for administrator role
        abort_if(! $this->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate request data
        $this->validate($request, [
            'login' => 'required|unique:users|min:1|max:30',
            'name' => 'required|min:1|max:90',
            'title' => 'nullable|min:1|max:30',
            'email' => 'required|unique:users|email:rfc',
            'role' => 'required|min:1|max:5',
        ]);

        // Custom password validation if LDAP is not enabled
        if (Config::get('app.ldap_domain') === null) {
            $validationResponse = $this->validatePassword($request);
            if ($validationResponse) {
                return $validationResponse;
            }
        }

        // Create and save the new user
        $user = new User();
        $user->login = $request->input('login');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->title = $request->input('title');
        $user->role = $request->input('role');
        $user->language = $request->input('language');

        if (Config::get('app.ldap_domain') === null) {
            $user->password = bcrypt($request->input('password1'));
        }

        $user->save();

        return redirect('/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     *
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Allow only admin or the owner of the profile to view
        $this->authorizeAdminOrOwner($user);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User $user
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Allow only admin or the owner of the profile to edit
        $this->authorizeAdminOrOwner($user);

        $controls = Control::select('id', 'name')->whereNull('realisation_date')->orderBy('name')->get();

        return view('users.edit', compact('user', 'controls'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  User $user
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Allow only admin or the owner of the profile to update
        $this->authorizeAdminOrOwner($user);

        // Validate request data
        $this->validate($request, [
            'login' => 'required|min:1|max:30|unique:users,login,'.$user->id,
            'name' => 'required|min:1|max:90',
            'title' => 'nullable|min:1|max:30',
            'email' => 'required|email:rfc|unique:users,email,'.$user->id,
            'role' => 'min:1|max:5',
        ]);

        // Custom password validation if LDAP is not enabled
        if (Config::get('app.ldap_domain') === null) {
            if ($request->input('password1') !== null) {
                $validationResponse = $this->validatePassword($request);
                if ($validationResponse) {
                    return $validationResponse;
                }
                $user->password = bcrypt($request->input('password1'));
            }
        }

        // Update user information
        $user->name = $request->input('name');
        $user->login = $request->input('login');
        $user->email = $request->input('email');
        if ($this->isAdmin()) {
            $user->role = $request->input('role');
        }
        $user->title = $request->input('title');
        $user->language = $request->input('language');

        // Update controls assigned to the user
        if ($this->isAdmin() || Auth::user()->role === 2) {
            $user->lastControls()->sync($request->input('controls', []));
        }

        $user->update();

        return $this->isAdmin() ? redirect('/users/' . $user->id) : redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Only for administrator role
        abort_if(! $this->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return redirect('/users');
    }

    /**
     * Export the list of users.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        // Only for administrator role
        abort_if(! $this->isAdmin(), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new UsersExport(), 'users.xlsx');
    }
    /**
     * Check if the authenticated user is an administrator.
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        return Auth::user()->role === 1;
    }

    /**
     * Check if the authenticated user is the owner of the resource.
     *
     * @param User $user
     *
     * @return bool
     */
    private function isOwner(User $user)
    {
        return Auth::user()->id === $user->id;
    }

    /**
     * Authorize the action if the user is either an admin or the owner.
     *
     * @param User $user
     */
    private function authorizeAdminOrOwner(User $user)
    {
        abort_if(! $this->isAdmin() && ! $this->isOwner($user), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Validate the password input from the request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function validatePassword(Request $request)
    {
        $password = $request->input('password1');

        if ($password === null) {
            return back()->withErrors(['password1' => 'No password'])->withInput();
        }

        if (strlen($password) < 8) {
            return back()->withErrors(['password1' => 'Password too short'])->withInput();
        }

        if ($password !== $request->input('password2')) {
            return back()->withErrors(['password1' => 'Passwords do not match'])->withInput();
        }

        return null;
    }
}
