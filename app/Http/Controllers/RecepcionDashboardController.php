<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RecepcionDashboardController extends Controller
{
    protected function api(): string
    {
        return config('services.backend.url');
    }

    protected function token(): string
    {
        return Session::get('api_token', '');
    }

    public function index(Request $request)
    {
        $userPerms   = Session::get('user_permissions', []);
        
        $canViewList = in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms);
        $canRegister = in_array('acceso_total', $userPerms) || in_array('registrar_visitantes', $userPerms);
        $canViewStats = $canViewList;

        if (!$canViewList) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $filtros = $request->only(['fecha', 'piso', 'estado', 'nombre']);
        if (empty($filtros['fecha'])) {
            $filtros['fecha'] = date('Y-m-d');
        }

        // Pedir dashboard data al backend
        $response = Http::withToken($this->token())
            ->timeout(30)
            ->get($this->api() . '/api/admin/recepcion/dashboard', $filtros);

        $data            = $response->ok() ? $response->json() : [];
        $visitantesArray = $data['visitantes'] ?? [];
        $visitantes      = json_decode(json_encode($visitantesArray), false);
        $totalVisitantes = $data['total']       ?? 0;
        $entradasHoy   = $data['entradas_hoy']  ?? 0;
        $salidasHoy    = $data['salidas_hoy']   ?? 0;

        // Visitantes "pegados"
        $stuckVisitors = [];
        if ($canViewList && !session()->has('stuck_visitors_checked')) {
            $stuckResp = Http::withToken($this->token())
                ->timeout(15)
                ->get($this->api() . '/api/admin/recepcion/visitantes-pegados');

            if ($stuckResp->ok()) {
                $stuckArray = $stuckResp->json('data', []);
                $stuckVisitors = json_decode(json_encode($stuckArray), false);
            }
            session(['stuck_visitors_checked' => true]);
        }

        return view('recepcion.dashboard', compact(
            'visitantes', 'totalVisitantes', 'entradasHoy', 'salidasHoy',
            'canViewStats', 'canRegister', 'canViewList', 'stuckVisitors'
        ));
    }

    public function filtrar(Request $request)
    {
        $request->validate([
            'fecha'  => 'nullable|date',
            'piso'   => 'nullable|string|max:10',
            'estado' => 'nullable|in:activos,todos',
            'nombre' => 'nullable|string|max:100',
        ]);

        $filtros = array_filter($request->only(['fecha', 'piso', 'estado', 'nombre']));
        return redirect()->route('recepcion.dashboard', $filtros);
    }

    public function limpiarFiltros()
    {
        return redirect()->route('recepcion.dashboard');
    }
}