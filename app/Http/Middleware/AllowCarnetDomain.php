<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowCarnetDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Derivar dominios permitidos dinámicamente desde .env
        $allowedHosts = [
            'localhost',
            '127.0.0.1',
        ];

        // Extraer host de APP_URL (frontend)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if ($appHost) {
            $allowedHosts[] = $appHost;
        }

        // Extraer host de CARNET_URL
        $carnetHost = parse_url(config('app.carnet_url'), PHP_URL_HOST);
        if ($carnetHost) {
            $allowedHosts[] = $carnetHost;
        }

        $allowedHosts = array_unique($allowedHosts);
        
        $host = $request->getHost();
        
        // Verificar si el host está en la lista de permitidos
        if (!in_array($host, $allowedHosts)) {
            // Log del intento de acceso no autorizado
            \Log::warning('Acceso denegado desde host no autorizado', [
                'host' => $host,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            // Redirigir al dominio principal o mostrar error
            if ($request->is('c/*') || $request->is('v/*')) {
                // Para rutas de carnets, redirigir al dominio correcto (derivado de APP_URL)
                $correctUrl = config('app.url') . $request->getRequestUri();
                return redirect($correctUrl, 301);
            }
            
            abort(403, 'Acceso no autorizado desde este dominio');
        }
        
        // Agregar headers de seguridad para carnets públicos
        $response = $next($request);
        
        if ($request->is('c/*') || $request->is('v/*') || $request->is('carnet16.php')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Cache para carnets públicos (30 minutos)
            $response->headers->set('Cache-Control', 'public, max-age=1800');
        }
        
        return $response;
    }
}