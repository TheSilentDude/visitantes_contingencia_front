<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CtsaludSearchController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function getEmployeeData(Request $request)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . '/api/visitantes/datos-empleado-backend', $request->only(['cedula', 'id']));
        
        \Illuminate\Support\Facades\Log::info("CTSalud response: " . $response->status() . " - " . $response->body());

        return response()->json($response->json(), $response->status());
    }
}
