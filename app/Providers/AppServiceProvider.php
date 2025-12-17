<?php

namespace App\Providers;

use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use LdapRecord\Container;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (
            (App::environment('production') && (Config::get('app.force_https') === null))
            ||
            Config::get('app.force_https')
        ) {
            URL::forceScheme('https');
        }

        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    $query->bindings
                );
            });
        }

        // Si le logging LDAP est activé, on force l’utilisation du channel "ldap"
        if (config('ldap.logging.enabled')) {
            Container::setLogger(
                Log::channel('ldap')
            );
        }

        if (in_array('keycloak', Config::get('services.socialite_controller.providers'))) {
            Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
                $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
            });
        }

        if (in_array('oidc', Config::get('services.socialite_controller.providers'))) {
            $this->bootOIDCSocialite();
        }

        // Show appVersion from version.txt in Blades
        view()->composer('*', function ($view) {
            $version = trim(file_get_contents(base_path('version.txt')));
            $view->with('appVersion', $version);
        });
    }

    /**
     * Register Generic OpenID Connect Provider.
     */
    private function bootOIDCSocialite()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'oidc',
            function ($app) use ($socialite) {
                $config = $app['config']['services.oidc'];
                return $socialite->buildProvider(\App\Providers\Socialite\GenericSocialiteProvider::class, $config);
            }
        );
    }
}
