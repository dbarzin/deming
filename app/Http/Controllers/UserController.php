<?php

namespace App\Http\Controllers;

use App\User;
use App\Exports\DomainsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = DB::table('users')->orderBy("id", "asc")->get();
        return view("users.index")->with("users", $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Auth::User()->role!=1, Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Auth::User()->role!=1, Response::HTTP_FORBIDDEN, '403 Forbidden');
        //
        $this->validate(
            $request, [
            "login" => "required|min:1|max:30",
            "name" => "required|min:1|max:30",
            "email" => "required|email:rfc,dns",
            "password1" => "required|min:8",
            "role" => "required"
            ]
        );

        if (request("password1")!=request("password2")) {
            return  redirect('/users/create')
                ->withErrors(['password1' => 'Les mots de passe ne correspondent pas'])
                ->withInput();
        }

        $user = new User();
        $user->login = request("login");
        $user->name = request("name");
        $user->email = request("email");
        $user->title = request("title");
        $user->role = request("role");
        $user->password = bcrypt(request("password1"));
        $user->save();

        return redirect("/users");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view("users.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        abort_if(
            (Auth::User()->role!=1) &&
            (Auth::User()->id!=$user->id), Response::HTTP_FORBIDDEN, '403 Forbidden'
        );
        
        return view("users.edit", compact("user"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Domain              $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        abort_if(
            (Auth::User()->role!=1) &&
            (Auth::User()->id!=$user->id), Response::HTTP_FORBIDDEN, '403 Forbidden'
        );

        $this->validate(
            $request, [
            "name" => "required|min:1|max:40",
            "email" => "required|email:rfc,dns"
            ]
        );

        if (request("password1")!=null) {
            if (request("password1")!=request("password2")) {
                return  redirect('/users/edit')
                    ->withErrors(['password1' => 'Les mots de passe ne correspondent pas'])
                    ->withInput();
            }
        }

        $user->name = request("name");
        $user->email = request("email");
        if (Auth::User()->role==1) {
            $user->role = request("role");
        }
        $user->title = request("title");
        if (request("password1")!=null) {
            $user->password = bcrypt(request("password1"));
        }
        $user->update();
        return redirect("/users");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(USer $user)
    {
        abort_if(Auth::User()->role!=1, Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user->delete();
        return redirect("/users");
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }    

}
