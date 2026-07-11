<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar validador personalizado para reCAPTCHA Enterprise
        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $siteKey = config('recaptcha.site_key');
            $secretKey = config('recaptcha.secret_key');
            
            if (empty($secretKey) || empty($siteKey)) {
                Log::warning('reCAPTCHA keys no configuradas');
                return true; // Permitir si no está configurado (desarrollo)
            }

            if (empty($value)) {
                return false;
            }

            try {
                // Usar API clásica de Google reCAPTCHA (funciona con Enterprise checkbox)
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                $result = $response->json();
                
                Log::info('reCAPTCHA verification result', [
                    'success' => $result['success'] ?? false,
                    'errors' => $result['error-codes'] ?? []
                ]);
                
                return $result['success'] ?? false;
            } catch (\Exception $e) {
                Log::error('Error verificando reCAPTCHA: ' . $e->getMessage());
                return false;
            }
        }, 'La verificación de reCAPTCHA ha fallado. Por favor, inténtelo de nuevo.');
    }
}
