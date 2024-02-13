<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class RedirectIfSuspendedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'api')
    {
        if (Auth::guard($guard)->check() && Auth::user()->status == 3) {
            if (!$request->expectsJson()) {
                return route('login');
            }
            throw new AuthenticationException("Your Account has been suspended");
        }

        return $next($request);
    }
}
