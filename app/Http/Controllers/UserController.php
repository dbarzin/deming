<?php

namespace App\Http\Controllers;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only for administrator role
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = DB::table('users')->orderBy('id', 'asc')->get();

        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('users.create');
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
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate(
            $request,
            [
                'login' => 'required|min:1|max:30',
                'name' => 'required|min:1|max:90',
                'title' => 'required|min:1|max:30',
                'email' => 'required|email:rfc',
                'role' => 'required',
            ]
        );

        if (Config::get('app.ldap_domain') === null) {

            $password = request('password1');

            if ($password === null) {
                return back()
                    ->withErrors(['password1' => 'No password'])
                    ->withInput();
            }
            if (strlen($password) < 8) {
                return back()
                    ->withErrors(['password1' => 'Password too short'])
                    ->withInput();
            }
            if ($password !== request('password2')) {
                return back()
                    ->withErrors(['password1' => 'Passwords does not match'])
                    ->withInput();
            }
        }

        $user = new User();
        $user->login = request('login');
        $user->name = request('name');
        $user->email = request('email');
        $user->title = request('title');
        $user->role = request('role');
        $user->language = request('language');
        if (Config::get('app.ldap_domain') === null) {
            $user->password = bcrypt(request('password1'));
        }
        $user->save();

        return redirect('/users');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        abort_if(
            (Auth::User()->role !== 1) &&
            (Auth::User()->id !== $user->id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        abort_if(
            (Auth::User()->role !== 1) &&
            (Auth::User()->id !== $user->id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $controls = Control::select('id', 'clause')->whereNull('realisation_date')->orderBy('clause')->get();

        return view('users.edit', compact('user', 'controls'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        abort_if(
            (Auth::User()->role !== 1) &&
            (Auth::User()->id !== $user->id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden'
        );

        $this->validate(
            $request,
            [
                'name' => 'required|min:1|max:40',
                'email' => 'required|email:rfc',
            ]
        );

        if (Config::get('app.ldap_domain') === null) {
            if (request('password1') !== null) {
                if (strlen(request('password1')) < 8) {
                    return back()
                        ->withErrors(['password1' => 'Password too short'])
                        ->withInput();
                }
                if ((request('password1') !== null) && (request('password1') !== request('password2'))) {
                    return back()
                        ->withErrors(['password1' => 'Passwords does not match'])
                        ->withInput();
                }
            }
        }

        $user->name = request('name');
        $user->email = request('email');
        if (Auth::User()->role === 1) {
            $user->role = request('role');
        }
        $user->title = request('title');
        $user->language = request('language');
        if (Config::get('app.ldap_domain') === null) {
            if (request('password1') !== null) {
                $user->password = bcrypt(request('password1'));
            }
        }

        // Update controls not already made
        $user->lastControls()->sync($request->input('controls', []));

        $user->update();

        if (Auth::User()->role === 1) {
            return redirect('/users/' . $user->id);
        }
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return redirect('/users');
    }

    public function export()
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Excel::download(new UsersExport(), 'users.xlsx');
    }
}
