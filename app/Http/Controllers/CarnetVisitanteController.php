<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * Carnets de visitantes legacy (acceso público + administración).
 * Los endpoints de generación de PDF están en Admin/CarnetVisitanteController.
 */
class CarnetVisitanteController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function index()
    {
        $response = Http::withToken($this->token())->get($this->api() . '/api/carnets/visitantes');
        $carnets = $response->ok() ? $response->json('data', []) : [];
        return view('admin.carnets.visitantes.legacy', compact('carnets'));
    }

    public function create()
    {
        return view('admin.carnets.visitantes.create');
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token())
            ->timeout(60)
            ->post($this->api() . '/api/carnets/visitantes', $request->except('_token'));

        if ($response->failed()) {
            return back()->withErrors(['error' => $response->json('message', 'Error.')])->withInput();
        }
        return redirect()->route('admin.carnets.visitantes.legacy.index')->with('success', $response->json('message', 'Carnet generado.'));
    }

    // Vista previa y descarga — el backend devuelve el PDF directamente
    public function preview($filename)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . "/api/carnets/visitantes/{$filename}/preview");

        return response($response->body(), $response->status(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function download($filename)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . "/api/carnets/visitantes/{$filename}/download");

        return response($response->body(), $response->status(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function publicPreview($filename)
    {
        $response = Http::get($this->api() . "/carnets/visitantes/{$filename}/view");
        return response($response->body(), $response->status(), ['Content-Type' => 'application/pdf']);
    }

    public function publicDownload($filename)
    {
        $response = Http::get($this->api() . "/carnets/visitantes/{$filename}/download");
        return response($response->body(), $response->status(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function getPdfBase64(Request $request)
    {
        $response = Http::withToken($this->token())
            ->post($this->api() . '/api/carnets/visitantes/pdf', $request->except('_token'));
        return response()->json($response->json(), $response->status());
    }

    public function fixPermissions(Request $request)
    {
        return response()->json(['message' => 'Los permisos son gestionados por el backend.']);
    }
}