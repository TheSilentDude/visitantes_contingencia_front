<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function buscarCedula(Request $request)
    {
        $response = Http::withToken($this->token())
            ->acceptJson()
            ->withoutRedirecting()
            ->get($this->api() . '/api/buscar-cedula', $request->only(['cedula', 'origen']));
        return response()->json($response->json(), $response->status());
    }
}
