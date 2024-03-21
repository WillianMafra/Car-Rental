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
        // is user authenticated?
        if (!auth()->check()) {
            return redirect()->route('login');
        }

         // Is user admin? (role 1)
         if (auth()->user()->role_id != 1) {
            return redirect()->route('home')->with('error', 'Unauthorized action.');        }

        return $next($request);
    }
}
