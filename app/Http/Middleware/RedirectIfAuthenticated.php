<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function __construct(private AuthFactory $auth) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
