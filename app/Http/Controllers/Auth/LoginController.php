<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LdapRecord\Auth\BindException;
use LdapRecord\Container;
use LdapRecord\Models\Entry as LdapEntry;

class LoginController extends Controller
{
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

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    /**
     * Determine the field used for login (email or login).
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Expose username property to the framework.
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Attempt an LDAP bind for the given app username + password using LDAPRecord v2.
     * Returns the corresponding LDAP user on success, or null on failure.
     */
    protected function ldapBindAndGetUser(string $appUsername, string $password): ?LdapEntry
    {

        try {
            $query = LdapEntry::query();

            // Optionnel : restreindre à une OU si configuré
            $base = config('app.ldap_users_base_dn', env('LDAP_USERS_BASE_DN'));
            if ($base) {
                $query->in($base);
            }

            // Filtre de localisation : OR sur les attributs pertinents
            $attrs = array_filter(array_map('trim', explode(',', config('app.ldap_login_attributes'))));

            $first = true;
            foreach ($attrs as $attr) {
                if ($first) {
                    $query->whereEquals($attr, $appUsername);
                    $first = false;
                } else {
                    $query->orWhereEquals($attr, $appUsername);
                }
            }

            \Log::debug("LDAP dn: " . $query->getDn() . " query: " . $query->getQuery());

            /** @var LdapEntry|null $ldapUser */
            $ldapUser = $query->first();
            if (! $ldapUser) {
                \Log::debug("LDAP user not found !");
                return null;
            }

            $connection = Container::getConnection();
            $dn = $ldapUser->getDn();

            if ($connection->auth()->attempt($dn, $password, true)) {
                return $ldapUser;
            }

            return null;
        } catch (BindException $e) {
            Log::warning('LDAP bind failed', [
                'error' => $e->getMessage(),
                'diagnostic' => $e->getDetailedError()->getDiagnosticMessage(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('LDAP error: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Override Laravel's default login attempt to add LDAPRecord support, toggled by .env
     *
     * Priority:
     *  - If LDAP_ENABLED=true => try LDAP; on success, log the mapped local user in.
     *  - If LDAP fails and LDAP_FALLBACK_LOCAL=true => try local DB credentials.
     *  - If LDAP_ENABLED=false => only local DB credentials.
     */
    protected function attemptLogin(Request $request)
    {
        $useLdap = config('app.ldap_enabled');
        $fallbackLocal = config('app.ldap_fallback_local');
        $autoProvision = config('app.ldap_auto_provision');

        $credentials = $request->only($this->username(), 'password');
        $identifier = $credentials[$this->username()] ?? '';
        $password = $credentials['password'] ?? '';

        if ($useLdap) {
            $ldapUser = $this->ldapBindAndGetUser($identifier, $password);

            if ($ldapUser) {
                // Map / locate local application user
                $local = User::query()
                    ->when(filter_var($identifier, FILTER_VALIDATE_EMAIL), function ($q) use ($identifier) {
                        return $q->where('email', $identifier);
                    }, function ($q) use ($identifier) {
                        return $q->where('login', $identifier);
                    })
                    ->first();

                if (! $local && $autoProvision) {
                    // Minimal safe provisioning – adapt attributes to your schema
                    $local = User::create([
                        'name' => $ldapUser->getFirstAttribute('cn') ?: $identifier,
                        'email' => $ldapUser->getFirstAttribute('mail') ?: 'user@localhost.local',
                        'login' => $identifier,
                        'role' => 5, // Auditee
                        // Store a random password so DB auth is not accidentally usable unless you set one explicitly
                        'password' => bcrypt(str()->random(32)),
                    ]);
                }

                if ($local) {
                    $remember = $request->boolean('remember');
                    $this->guard()->login($local, $remember);
                    return true;
                }

                // LDAP OK but no mapped local user and no auto-provision
                return false;
            }

            // LDAP failed – optionally fall back to local DB auth
            if (! $fallbackLocal) {
                return false;
            }
        }

        // Local database auth path (default Laravel)
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
}
