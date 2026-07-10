<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VisitanteController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    public function create()
    {
        return view('visitantes.create');
    }

    public function store(Request $request)
    {
        $http = Http::withToken($this->token())->acceptJson()->timeout(60);

        if ($request->hasFile('foto_file')) {
            $http = $http->attach('foto_file',
                file_get_contents($request->file('foto_file')->getRealPath()),
                $request->file('foto_file')->getClientOriginalName()
            );
        }

        $response = $http->post($this->api() . '/api/visitantes', $request->except(['_token', 'foto', 'foto_file']));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($response->json(), $response->status());
        }

        if ($response->failed()) {
            return back()
                ->withErrors($response->json('errors', ['error' => $response->json('message', 'Error al registrar visitante.')]))
                ->withInput();
        }

        $source = $request->input('source', 'recepcion');
        $redirectRoute = $source === 'vehicular' ? 'vehiculos.accesos' : 'recepcion.dashboard';

        return redirect()->route($redirectRoute)
            ->with('success', $response->json('message', 'Visitante registrado correctamente.'));
    }

    public function checkout(Request $request, $visitante)
    {
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->post($this->api() . "/api/visitantes/{$visitante}/checkout", $request->except('_token'));

        if ($response->failed()) {
            return back()->with('error', $response->json('message', 'Error al registrar salida.'));
        }

        return back()->with('success', $response->json('message', 'Salida registrada.'));
    }

    public function searchEmployee(Request $request)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . '/api/visitantes/buscar-empleado', $request->only(['q', 'search']));
        return response()->json($response->json(), $response->status());
    }

    public function buscarPorCedula(Request $request)
    {
        $response = Http::withToken($this->token())
            ->post($this->api() . '/api/visitantes/buscar-cedula', $request->only(['cedula', 'origen']));
        return response()->json($response->json(), $response->status());
    }

    public function getVisitanteDetalle($id)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . "/api/visitantes/detalle/{$id}");
        return response()->json($response->json(), $response->status());
    }

    public function getCarnetsByPiso(Request $request)
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . '/api/carnets/por-piso', $request->only(['piso']));
        return response()->json($response->json(), $response->status());
    }

    public function storeSelectedEmployee(Request $request)
    {
        // Almacenar empleado seleccionado en sesión (no requiere backend)
        session(['selectedEmployee' => json_encode($request->all())]);
        return response()->json(['ok' => true]);
    }
}
