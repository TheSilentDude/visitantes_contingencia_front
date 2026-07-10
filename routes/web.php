<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitanteController;
use App\Http\Controllers\CarnetVisitanteController;
use App\Http\Controllers\CarnetController;
use App\Http\Controllers\RecepcionDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CtsaludSearchController;
use App\Http\Controllers\ApiController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Proxy route for images to avoid CORS canvas tainting in Cropper.js
Route::get('/proxy-image', function(\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url) return response('No URL provided', 400);
    try {
        $response = \Illuminate\Support\Facades\Http::get($url);
        if ($response->successful()) {
            $contentType = $response->header('Content-Type');
            if (!$contentType) {
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                $contentType = $ext == 'png' ? 'image/png' : 'image/jpeg';
            }
            return response($response->body())->header('Content-Type', $contentType);
        }
        return response('Status: ' . $response->status(), $response->status());
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
})->name('proxy.image');

// Public Carnet Verification Route
Route::get('/carnet-digital/{id}', [CarnetController::class, 'digital'])->name('carnet.digital');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/check-force-logout', [AuthController::class, 'checkForceLogout'])->name('check.force.logout');
Route::get('/forced-logout', [AuthController::class, 'forcedLogout'])->name('forced.logout');
Route::post('/execute-forced-logout', [AuthController::class, 'executeForcedLogout'])->name('execute.forced.logout');

// Password Reset Routes
Route::get('/password/request', function () {
    return view('auth.passwords.email');
})->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

// Rutas públicas para PDFs de carnets (sin middleware de autenticación)
Route::get('/carnets/visitantes/{filename}/view', [CarnetVisitanteController::class, 'publicPreview'])->name('carnets.visitantes.public.preview');
Route::get('/carnets/visitantes/{filename}/download', [CarnetVisitanteController::class, 'publicDownload'])->name('carnets.visitantes.public.download');

// Rutas seguras para carnets con tokens encriptados
Route::get('/v/{token}', [App\Http\Controllers\SecureCarnetController::class, 'showVisitante'])
    ->middleware(\App\Http\Middleware\AllowCarnetDomain::class)
    ->name('carnet.visitante.short');

// Nueva ruta unificada para tokens de 16 caracteres (empleados y visitantes)
Route::get('/c/{token}', function($token) {
    if (!preg_match('/^[A-Z0-9]{16}$/', $token)) {
        abort(400, 'Formato de token inválido');
    }
    
    try {
        $backendUrl = config('services.backend.url');
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get($backendUrl . "/api/carnet/verify-unified/{$token}");
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
                'mensaje' => $data['message'] ?? 'Ha ocurrido un error.',
                'tipo' => $data['tipo'] ?? 'sistema'
            ]);
        }
        
        if ($data['type'] === 'empleado') {
             if ($data['state'] === 'unassigned') {
                 $carnet = json_decode(json_encode($data['carnet']));
                 return view('public.carnet_empleado_disponible', ['carnet' => $carnet]);
             } else {
                 $usuario = json_decode(json_encode($data['usuario']));
                 $photoUrl = $data['photoUrl'];
                 $pisos = $data['pisos'];
                 return view('public.carnet_digital', compact('usuario', 'photoUrl', 'pisos'));
             }
        } else if ($data['type'] === 'visitante') {
             if (isset($data['status']) && $data['status'] === 'disponible') { 
                 $carnet = json_decode(json_encode($data['carnet']));
                 return view('carnet.disponible', [
                     'carnet' => $carnet,
                     'codigo_carnet' => $data['codigo_carnet'],
                     'piso' => $data['piso']
                 ]);
             } else {
                 $visitante = json_decode(json_encode($data['visitante']));
                 $carnet = json_decode(json_encode($data['carnet'] ?? []));
                 return view('carnet.digital', [
                     'visitante' => $visitante,
                     'piso' => $data['piso'],
                     'carnet' => $carnet,
                     'codigo_carnet' => $data['codigo_carnet'] ?? null,
                     'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null
                 ]);
             }
        }
        
        return redirect("/carnet16.php?token={$token}");

    } catch (\Exception $e) {
        \Log::error("Error en carnet unificado: " . $e->getMessage());
        return redirect("/carnet16.php?token={$token}");
    }
})->where('token', '[A-Z0-9]{16}')
  ->middleware(\App\Http\Middleware\AllowCarnetDomain::class)
  ->name('carnet.unified');

// Redirección de compatibilidad para URLs antiguas
Route::get('/visitantes.php', [App\Http\Controllers\SecureCarnetController::class, 'redirectOldVisitante'])->name('visitantes.redirect');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    Route::get('/force-password-change', [AuthController::class, 'showForceChangeForm'])->name('password.force_change');
    Route::post('/force-password-change', [AuthController::class, 'forceChange'])->name('password.force_change.update');
    
    Route::get('/dashboard', function () {
        return redirect()->route('recepcion.dashboard');
    })->name('dashboard');

    Route::get('/home', function () {
        return redirect()->route('recepcion.dashboard');
    })->name('home');
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::match(['put', 'patch'], '/', [ProfileController::class, 'updateProfile'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])
            ->middleware('throttle:5,1')
            ->name('password');
    });
    
    // Shared API: CTSalud employee lookup 
    Route::get('/api/visitantes/datos-empleado', [CtsaludSearchController::class, 'getEmployeeData'])->name('visitantes.getEmployeeData');
    
    // Carnets de Visitantes (Gestión Administrativa)
    Route::middleware(['permission:acceso_total,carnet_visitante,imp_reversos'])->group(function () {
        Route::get('/admin/carnets/visitantes', [\App\Http\Controllers\Admin\CarnetVisitanteController::class, 'index'])->name('admin.carnets.visitantes.index');
        Route::post('/admin/carnets/visitantes/generar', [\App\Http\Controllers\Admin\CarnetVisitanteController::class, 'generarCarnets'])->name('admin.carnets.visitantes.generar');
        Route::post('/admin/carnets/visitantes/guardar', [\App\Http\Controllers\Admin\CarnetVisitanteController::class, 'guardarPDF'])->name('admin.carnets.visitantes.guardar');
        Route::post('/admin/carnets/visitantes/pdf', [\App\Http\Controllers\Admin\CarnetVisitanteController::class, 'obtenerPDFBase64'])->name('admin.carnets.visitantes.pdf');
    });

    // Recepción: panel y consultas
    Route::middleware(['permission:recepcion'])->group(function () {
        Route::get('/recepcion/dashboard', [RecepcionDashboardController::class, 'index'])->name('recepcion.dashboard');
        Route::post('/recepcion/dashboard/filtrar', [RecepcionDashboardController::class, 'filtrar'])->name('recepcion.dashboard.filtrar');
        Route::get('/recepcion/dashboard/limpiar', [RecepcionDashboardController::class, 'limpiarFiltros'])->name('recepcion.dashboard.limpiar');

        Route::get('/api/visitante-detalle/{id}', [VisitanteController::class, 'getVisitanteDetalle'])->name('api.visitante-detalle');
    });

    // Recepción: alta de visitantes y salidas
    Route::middleware(['permission:acceso_total,registrar_visitantes'])->group(function () {
        Route::get('/api/visitantes/buscar-empleado', [VisitanteController::class, 'searchEmployee'])->name('visitantes.searchEmployee');
        Route::post('/recepcion/store-selected-employee', [VisitanteController::class, 'storeSelectedEmployee'])->name('recepcion.store-selected-employee');

        Route::get('/visitantes/create', [VisitanteController::class, 'create'])->name('visitantes.create');
        Route::post('/visitantes', [VisitanteController::class, 'store'])->name('visitantes.store');
        Route::post('/visitantes/{visitante}/checkout', [VisitanteController::class, 'checkout'])->name('visitantes.checkout');

        Route::get('/api/buscar-cedula', [ApiController::class, 'buscarCedula'])->name('api.buscar-cedula');
        Route::post('/api/buscar-visitante-cedula', [VisitanteController::class, 'buscarPorCedula'])->name('api.buscar-visitante-cedula');
        Route::get('/api/carnets-por-piso', [VisitanteController::class, 'getCarnetsByPiso'])->name('api.carnets-por-piso');
    });

});
