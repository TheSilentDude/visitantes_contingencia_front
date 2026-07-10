<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * SecureCarnetController - Proxy para acceso seguro a archivos de carnet.
 * Los PDFs se obtienen del backend y se sirven al cliente.
 */
class SecureCarnetController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function viewPdfEmpleado($filename)
    {
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->get($this->api() . "/api/carnets/secure/empleados/{$filename}");

        if ($response->failed()) abort(404);

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function viewPdfVisitante($filename)
    {
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->get($this->api() . "/api/carnets/secure/visitantes/{$filename}");

        if ($response->failed()) abort(404);

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadPdfEmpleado($filename)
    {
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->get($this->api() . "/api/carnets/secure/empleados/{$filename}/download");

        return response($response->body(), $response->status(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadPdfVisitante($filename)
    {
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->get($this->api() . "/api/carnets/secure/visitantes/{$filename}/download");

        return response($response->body(), $response->status(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function showVisitante($token)
    {
        try {
            $response = Http::timeout(10)->get($this->api() . "/api/carnet/visitante/{$token}");
            
            $data = $response->json();
            
            if ($response->failed() || !isset($data['status'])) {
                return view('carnet.error', [
                    'titulo' => 'Error de Conexión',
                    'mensaje' => 'No se pudo conectar con el servidor para validar el carnet.',
                    'tipo' => 'conexion'
                ]);
            }
            
            if ($data['status'] === 'error') {
                return view('carnet.error', [
                    'titulo' => $data['titulo'] ?? 'Error',
                    'mensaje' => $data['mensaje'] ?? 'Ha ocurrido un error.',
                    'tipo' => $data['tipo'] ?? 'sistema'
                ]);
            }
            
            if ($data['status'] === 'disponible') {
                return view('carnet.disponible', [
                    'carnet' => (object)$data['carnet'],
                    'codigo_carnet' => $data['codigo_carnet'],
                    'piso' => $data['piso']
                ]);
            }
            
            if ($data['status'] === 'visitante') {
                return view('carnet.visitante', [
                    'visitante' => json_decode(json_encode($data['visitante']))
                ]);
            }
            
        } catch (\Exception $e) {
            return view('carnet.error', [
                'titulo' => 'Error del Sistema',
                'mensaje' => 'Error procesando la solicitud en el frontend.',
                'tipo' => 'sistema'
            ]);
        }
    }
}