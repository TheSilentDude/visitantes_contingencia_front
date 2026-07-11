<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class QRGeneratorController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function generate(Request $request)
    {
        $response = Http::timeout(30)
            ->post($this->api() . '/api/generate-qr', $request->except('_token'));
        return response()->json($response->json(), $response->status());
    }
}