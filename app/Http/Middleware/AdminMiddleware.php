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
        // is user 
        if (!auth()->check()) {
            return redirect()->route('login');
        }

         // Verifique se o usuário tem a role desejada (no caso, "1")
         if (auth()->user()->role != 1) {
            // Se não tiver a role desejada, você pode retornar uma resposta 403 Forbidden ou redirecionar para uma página de acesso negado
            return redirect()->back()->with('error', 'Unauthorized action.');        }

        return $next($request);
    }
}
