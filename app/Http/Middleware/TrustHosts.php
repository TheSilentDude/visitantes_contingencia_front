<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        // Derivar hosts dinámicamente desde las variables de entorno
        $hosts = [
            $this->allSubdomainsOfApplicationUrl(),
            'localhost',
            '127.0.0.1',
        ];

        // Extraer host de APP_URL
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        if ($appHost) {
            $hosts[] = $appHost;
        }

        // Extraer host de CARNET_URL
        $carnetHost = parse_url(config('app.carnet_url'), PHP_URL_HOST);
        if ($carnetHost) {
            $hosts[] = $carnetHost;
        }

        return array_unique(array_filter($hosts));
    }
}