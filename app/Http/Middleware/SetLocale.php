<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (request('change_language')) {
            session()->put('language', request('change_language'));
            app()->setLocale(request('change_language'));
            return $next($request);
        }

        if (isset(Auth::User()->language)) {
            app()->setLocale(Auth::User()->language);
            return $next($request);
        }

        if (session('language')) {
            app()->setLocale(session('language'));
            return $next($request);
        }

        if (config('panel.primary_language')) {
            app()->setLocale(config('panel.primary_language'));
            return $next($request);
        }

        return $next($request);
    }
}
