<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * CarnetController - vista principal del panel de carnets.
 */
class CarnetController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function index()
    {
        $response = Http::withToken($this->token())->get($this->api() . '/api/carnets');
        $data = $response->ok() ? $response->json() : [];

        $carnets       = $data['carnets']       ?? [];
        $totalCarnets  = $data['total']         ?? 0;
        $disponibles   = $data['disponibles']   ?? 0;
        $asignados     = $data['asignados']     ?? 0;

        return view('carnets.index', compact('carnets', 'totalCarnets', 'disponibles', 'asignados'));
    }

    public function store(Request $request)
    {
        $response = Http::withToken($this->token())
            ->post($this->api() . '/api/carnets', $request->except('_token'));

        if ($response->failed()) {
            return back()->withErrors(['error' => $response->json('message', 'Error al guardar.')])->withInput();
        }
        return back()->with('success', $response->json('message', 'Carnet guardado.'));
    }

    public function update(Request $request, $id)
    {
        $response = Http::withToken($this->token())
            ->put($this->api() . "/api/carnets/{$id}", $request->except(['_token', '_method']));

        if ($response->failed()) {
            return back()->withErrors(['error' => $response->json('message', 'Error al actualizar.')])->withInput();
        }
        return back()->with('success', $response->json('message', 'Carnet actualizado.'));
    }

    public function destroy($id)
    {
        Http::withToken($this->token())->delete($this->api() . "/api/carnets/{$id}");
        return back()->with('success', 'Carnet eliminado.');
    }

    public function preview($id)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . "/api/carnets/{$id}/preview");

        if ($response->failed()) abort(404);

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }
}
