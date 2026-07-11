<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifica que el usuario autenticado (via sesión) tenga el rol requerido.
 * Reemplaza el CheckRole original que usaba Auth::user() + BD.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Session::has('api_token') || !Session::has('user')) {
            return redirect()->route('login');
        }

        $userRolId = (int) Session::get('user_rol_id');

        $roleMap = [
            'admin'              => [1],
            'rrhh'               => [2],
            'recepcion'          => [3, 4],
            'rotacion'           => [5],
            'impresiones_reversa'=> [6],
        ];

        foreach ($roles as $role) {
            foreach (explode(',', $role) as $singleRole) {
                $singleRole = trim($singleRole);
                $allowedIds = $roleMap[$singleRole] ?? [];
                if (in_array($userRolId, $allowedIds, true)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'No autorizado.');
    }
}
