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
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

         $this->username = $this->findUsername();
    }

    /**
     * Login with LDAP
     *
     */
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
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            } 
            return false;
        } else {
            return $this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            );
        }
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';
 
        request()->merge([$fieldType => $login]);

        return $fieldType;
    }
 
    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }
}
