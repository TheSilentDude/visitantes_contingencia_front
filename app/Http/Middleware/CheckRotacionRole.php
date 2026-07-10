<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRotacionRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('api_token') || !Session::has('user')) {
            return redirect()->route('login');
        }

        $rolRotacion = config('rotacion.roles.rotacion', 5);

        if ((int) Session::get('user_rol_id') !== $rolRotacion) {
            abort(403, 'No tiene permisos para acceder al módulo de rotación.');
        }

        return $next($request);
    }
}
