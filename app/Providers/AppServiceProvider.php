<?php

namespace App\Providers;

use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
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
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        if (env('APP_DEBUG')) {
            DB::listen(function ($query) {
                Log::info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        }

        if (in_array('keycloak', Config::get('services.socialite_controller.providers'))) {
            Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
                $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
            });
        }

        if (in_array('oidc', Config::get('services.socialite_controller.providers'))) {
            $this->bootOIDCSocialite();
        }
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
