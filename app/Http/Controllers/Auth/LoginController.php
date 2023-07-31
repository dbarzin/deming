<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Config;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // LDAP Login
    protected function ldapLogin(string $userid, string $password)
    {
        $ldapserver = Config::get('app.ldap_url');

        putenv('LDAPTLS_REQCERT=require');
        putenv('LDAPTLS_CACERT='.Config::get('app.ldap_cert'));

        $ldapconn = ldap_connect($ldapserver);
        if ($ldapconn) {
            return ldap_bind($ldapconn, $userid . '@' . Config::get('app.ldap_domain'), $password);
        }
        return false;
    }

    protected function attemptLogin(Request $request)
    {
        if (Config::get('app.ldap_domain') !== null) {
            $credentials = $request->only($this->username(), 'password');
            $username = $credentials[$this->username()];
            $password = $credentials['password'];
            try {
                if ($this->ldapLogin($username, $password)) {
                    $user = User::where('login', $username)->first();
                    if (! $user) {
                        return false;
                    }
                    $this->guard()->login($user, true);
                    return true;
                }
                return false;
            } finally {
                return false;
            }
        } else {
            return $this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            );
        }
    }
}
