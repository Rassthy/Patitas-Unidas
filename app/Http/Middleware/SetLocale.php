<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $settings = Auth::user()->user_settings;
            $idioma = is_array($settings) ? ($settings['idioma'] ?? 'es') : 'es';
            App::setLocale($idioma);
        } else {
            App::setLocale('es');
        }

        return $next($request);
    }
}