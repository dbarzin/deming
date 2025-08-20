<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Log;

/**
 * Socialite Controller for OpenID Connect Autentication
 */
class SocialiteController extends Controller
{
    public const ROLES_MAP = [
        //'admin' => '1',
        'user' => 2,
        'auditee' => 5,
        'auditor' => 3,
        //'api' => '4',
    ];

    public const LOCALES = ['en', 'fr'];

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['redirect', 'callback']]);
    }

    /**
     * Redirect action use to redirect user to OIDC provider.
     */
    public function redirect(string $provider)
    {
        $providers = config('services.socialite_controller.providers', []);

        if (in_array($provider, $providers)) {
            Log::debug("Redirect with '{$provider}' provider");
            $config_name = 'services.socialite_controller.'.$provider;
            $additional_scopes = config($config_name.'.additional_scopes');
            return Socialite::with($provider)->scopes($additional_scopes)->redirect();
        }

        Log::warning("Redirect: Provider '{$provider}' not found.");
        abort(404);
    }

    /**
     * Callback action use when OIDC provider redirect user to app.
     */
    public function callback(Request $_request, string $provider)
    {
        $providers = config('services.socialite_controller.providers', []);

        if (! in_array($provider, $providers)) {
            Log::warning("Callback: Provider '{$provider}' not found.");
            abort(404);
        }

        Log::debug("Callback provider : '{$provider}'");

        // Get additionnal config for current provider
        $config_name = 'services.socialite_controller.'.$provider;
        $allow_createUser = false;
        $allow_updateUser = false;
        if (config($config_name)) {
            $allow_createUser = config($config_name.'.allow_createUser', $allow_createUser);
            $allow_updateUser = config($config_name.'.allow_updateUser', $allow_updateUser);
        }
        Log::debug('CONFIG: allow_createUser='.($allow_createUser ? 'true' : 'false'));
        Log::debug('CONFIG: allow_updateUser='.($allow_updateUser ? 'true' : 'false'));

        $role_claim = null;
        $default_role = null;

        if ($allow_createUser || $allow_updateUser) {
            $role_claim = config($config_name.'.role_claim', '');
            Log::debug('CONFIG: role_claim='.$role_claim);
            $default_role = config($config_name.'.default_role', '');
            Log::debug('CONFIG: default_role='.$default_role);
        }

        try {
            $socialite_user = Socialite::with($provider)->user();
            $user = null;

            // Search user by email
            if ($socialite_user->email) {
                $user = User::query()->whereEmail($socialite_user->email)->first();
            } else {
                Log::warning('User has no attribute email');
            }

            // If not exist and allow to create user then create it
            if (! $user && $allow_createUser) {
                $user = $this->createUser($socialite_user, $provider, $role_claim, $default_role);
            }

            // If no user redirect to login with error message
            if (! $user) {
                Log::warning("User [{$socialite_user->id}, {$socialite_user->email}] not found in deming database");
                return redirect('login')->withErrors(['socialite' => trans('cruds.login.error.user_not_exist') ]);
            }

            if ($allow_updateUser) {
                $this->updateUser($user, $socialite_user, $provider, $role_claim, $default_role);
            }

            Log::info("User '{$user->login}' login with {$provider} provider");

            Auth::guard('web')->login($user);

            return redirect('/');
        } catch (Exception $exception) {
            return redirect('login');
        }
    }

    /**
     * Create user with claims provided.
     */
    protected function createUser(SocialiteUser $socialite_user, string $provider, string $role_claim, string $default_role): User|null
    {
        $user = new User();

        $user->login = $this->getUserLogin($socialite_user);
        $user->name = $socialite_user->name;
        $user->email = $socialite_user->email;
        $user->title = "User provide by {$provider}";
        $user->role = $this->get_user_role($socialite_user, $role_claim, $default_role);
        $user->language = $this->get_user_langage($socialite_user);

        // TODO allow null password
        $user->password = bin2hex(random_bytes(32));

        Log::info("Create new user '{$user->login}' with role '{$user->role}' from {$provider} provider");
        try {
            $user->save();
        } catch(QueryException $exception) {
            Log::debug($exception->getMessage());
            Log::error('Unable to create user');
            return null;
        }

        return $user;
    }

    /**
     * Update user with claims providid.
     */
    protected function updateUser(User $user, SocialiteUser $socialite_user, string $provider, string $role_claim, string $default_role)
    {
        $updated = false;

        $login = $this->getUserLogin($socialite_user);
        if ($login !== $user->login) {
            Log::debug("Login changed {$user->login} => {$login}");
            $user->login = $login;
            $updated = true;
        }

        if ($socialite_user->name !== $user->name) {
            Log::debug("Name changed {$user->name} => {$socialite_user->name}");
            $user->name = $socialite_user->name;
            $updated = true;
        }

        $role = $this->get_user_role($socialite_user, $role_claim, $default_role);
        if ($role !== $user->role) {
            Log::debug("Role changed {$user->role} => {$role}");
            $user->role = $role;
            $updated = true;
        }

        $language = $this->get_user_langage($socialite_user);
        if ($language !== $user->language) {
            Log::debug("Lauguage change {$user->language} => {$language}");
            $user->language = $language;
            $updated = true;
        }

        if ($updated) {
            Log::info("Update user '{$user->login}' with role '{$user->role}' from {$provider} provider");
            $user->save();
        }
        return $user;
    }

    /**
     * Return user's login.
     */
    private function getUserLogin(SocialiteUser $socialite_user): string
    {
        // set login with preferred_username, otherwise use id
        if ($socialite_user->offsetExists('preferred_username')) {
            return $socialite_user->offsetGet('preferred_username');
        }
        return $socialite_user->id;
    }

    /**
     * Return user's role.
     * If no role provided, use $default_role value.
     * If $default_role is null and no role provided, null return.
     */
    private function get_user_role(SocialiteUser $socialite_user, string $role_claim, string $default_role): int|null
    {
        $role_name = '';
        if ($role_claim !== '') {
            $role_name = $this->get_claim_value($socialite_user, $role_claim);
            Log::debug("Provided claim '{$role_claim}'='{$role_name}'");
        }
        if (! array_key_exists($role_name, self::ROLES_MAP)) {
            if ($default_role !== '') {
                $role_name = $default_role;
            } else {
                Log::error("No default role set! A valid role must be provided. role='{$role_name}'");
                return null;
            }
        }
        return self::ROLES_MAP[$role_name];
    }

    /**
     * Return user's language.
     * Use locale claim to dertermine user's language.
     */
    private function get_user_langage(SocialiteUser $socialite_user): string
    {
        if ($socialite_user->offsetExists('locale')) {
            $locale = explode('-', $socialite_user->offsetGet('locale'))[0];
            if (in_array($locale, self::LOCALES)) {
                return $locale;
            }
        }
        return self::LOCALES[0];
    }

    private function get_claim_value(SocialiteUser $user, string $claim)
    {
        $value = null;
        foreach (explode('.', $claim) as $offset) {
            if (! $value) {
                if (! $user->offsetExists($offset)) {
                    return null;
                }
                $value = $user->offsetGet($offset);
                continue;
            }
            if (! array_key_exists($offset, $value)) {
                return null;
            }
            $value = $value[$offset];
        }
        return $value;
    }
}
