<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifica que el usuario esté autenticado via token en sesión.
 * Reemplaza el middleware Auth::check() que requería acceso a BD.
 */
class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (!Session::has('api_token') || !Session::has('user')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
