<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CarnetVisitanteController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function index()
    {
        $response = Http::acceptJson()->withToken($this->token())->get($this->api() . '/api/carnets/visitantes');
        $data = $response->ok() ? $response->json() : [];
        $carnets = $data['data'] ?? [];
        $pisos = $data['pisos'] ?? ['PB', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'];
        $historialCarnets = collect($data['historial'] ?? [])->map(function ($item) {
            return json_decode(json_encode($item));
        });

        return view('admin.carnets.visitantes.index', compact('carnets', 'pisos', 'historialCarnets'));
    }

    public function generarCarnets(Request $request)
    {
        $response = Http::acceptJson()->withToken($this->token())
            ->timeout(120)
            ->post($this->api() . '/api/carnets/visitantes/generar', $request->except('_token'));
        return response()->json($response->json(), $response->status());
    }

    public function guardarPDF(Request $request)
    {
        $response = Http::acceptJson()->withToken($this->token())
            ->timeout(60)
            ->post($this->api() . '/api/carnets/visitantes/guardar', $request->except('_token'));
        return response()->json($response->json(), $response->status());
    }

    public function obtenerPDFBase64(Request $request)
    {
        $response = Http::acceptJson()->withToken($this->token())
            ->timeout(60)
            ->post($this->api() . '/api/carnets/visitantes/pdf', $request->except('_token'));
        return response()->json($response->json(), $response->status());
    }
}