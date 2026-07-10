<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$required_permissions): Response
    {
        $user_permissions = session('user_permissions', []);

        // Bypass para el Rol Administrador Global
        if (in_array('acceso_total', $user_permissions)) {
            return $next($request);
        }

        $hasPermission = false;
        foreach ($required_permissions as $p) {
            if (in_array($p, $user_permissions)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'No autorizado. Permiso(s) requerido(s): ' . implode(', ', $required_permissions)], 403);
            }

            return redirect()->route('login')->withErrors([
                'usuario' => 'Usted no tiene permisos para acceder a este módulo.'
            ]);
        }

        return $next($request);
    }
}
