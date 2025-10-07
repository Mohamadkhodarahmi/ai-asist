<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For now, allow any authenticated user to access admin
        // In production, you should implement proper admin role checking
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // TODO: Implement proper admin role checking
        // Example: Check if user has admin role or is super admin
        // if (!auth()->user()->hasRole('admin')) {
        //     abort(403, 'Unauthorized access to admin area');
        // }

        return $next($request);
    }
}