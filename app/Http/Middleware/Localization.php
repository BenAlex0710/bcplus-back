<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->expectsJson() || $request->ajax()) {
            if (session()->has('locale')) {
                app()->setLocale(session()->get('locale'));
            }
        } else {
            $lang = ($request->hasHeader('Lets-BcNews-Language')) ? $request->header('Lets-BcNews-Language') : 'en';
            session()->put('locale', $lang);
            app()->setLocale($lang);
        }
        return $next($request);
    }
}
