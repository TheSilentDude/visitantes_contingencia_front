<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ForceLogoutMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // No interceptar: pantalla de aviso, cierre forzado, polling JSON ni login
        if ($request->routeIs(
            'forced.logout',
            'execute.forced.logout',
            'check.force.logout',
            'login',
            'login.post',
            'logout'
        )) {
            return $next($request);
        }

        $token = Session::get('api_token');
        if (!$token) {
            return $next($request);
        }

        // Consultar al backend si se debe forzar el logout (valor actual en BD)
        try {
            $response = Http::withToken($token)
                ->timeout(5)
                ->get(config('services.backend.url') . '/api/check-force-logout');

            if ($response->ok() && $response->json('force_logout')) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['force_logout' => true]);
                }

                return redirect()->route('forced.logout');
            }
        } catch (\Exception $e) {
            // Si el backend no responde, dejar pasar sin forzar logout
        }

        return $next($request);
    }
}
