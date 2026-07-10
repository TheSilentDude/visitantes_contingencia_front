<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * URL base del backend API.
     */
    protected function backendUrl(): string
    {
        return config('services.backend.url');
    }

    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $rules = [
            'usuario' => ['required', 'string'],
            'clave'   => ['required', 'string'],
        ];

        if (!app()->isLocal()) {
            $rules['g-recaptcha-response'] = ['required', 'recaptcha'];
        }

        $credentials = $request->validate($rules, [
            'g-recaptcha-response.required' => 'Por favor complete el captcha.',
            'g-recaptcha-response.recaptcha' => 'La verificación de captcha ha fallado. Por favor, inténtelo de nuevo.',
        ]);

        $response = Http::timeout(config('services.backend.timeout', 30))
            ->post($this->backendUrl() . '/api/login', [
                'usuario' => $credentials['usuario'],
                'clave'   => $credentials['clave'],
            ]);

        if ($response->failed()) {
            return back()->withErrors([
                'usuario' => $response->json('message') ?? 'Nombre de usuario o contraseña incorrectos.',
            ])->onlyInput('usuario');
        }

        $data = $response->json();

        // Guardar token Sanctum y datos del usuario en sesión
        Session::put('api_token', $data['access_token']);
        Session::put('user', $data['user']);
        Session::put('user_id', $data['user']['id']);
        Session::put('user_rol_id', $data['user']['rol_id']);
        Session::put('user_name', $data['user']['usuario']);
        Session::put('user_permissions', $data['permissions'] ?? []);

        // Regenerar sesión por seguridad
        $request->session()->regenerate();

        // Si el backend indica que el usuario debe cambiar contraseña (primer login)
        if (!empty($data['must_change_password'])) {
            return redirect()->route('password.force_change');
        }

        return $this->redirectBasedOnPermissions($data['permissions'] ?? []);
    }

    protected function redirectBasedOnPermissions(array $permisos)
    {
        // Hub Centralizado (Landing Page)
        // Todos los usuarios inician su experiencia en la Sala de Bienvenida, evitando Failsafes o embudos forzados.
        return redirect()->route('home');
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function logout(Request $request)
    {
        $token = Session::get('api_token');

        if ($token) {
            // Invalidar token en el backend
            Http::withToken($token)
                ->timeout(10)
                ->post($this->backendUrl() . '/api/logout');
        }

        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function checkForceLogout()
    {
        $token = Session::get('api_token');
        if (!$token) {
            return response()->json(['force_logout' => false]);
        }

        $response = Http::withToken($token)
            ->timeout(10)
            ->get($this->backendUrl() . '/api/check-force-logout');

        if ($response->ok() || $response->status() === 401) {
            $isForceLogout = $response->json('force_logout') === true || $response->status() === 401;
            
            if ($isForceLogout) {
                return response()->json(['force_logout' => true]);
            }
        }

        return response()->json(['force_logout' => false]);
    }

    public function forcedLogout()
    {
        return view('auth.forced_logout_warning');
    }

    public function executeForcedLogout(Request $request)
    {
        $token = Session::get('api_token');
        if ($token) {
            try {
                Http::withToken($token)
                    ->timeout(10)
                    ->post($this->backendUrl() . '/api/logout');
            } catch (\Exception $e) {
                // Continuar limpiando sesión local aunque falle la API
            }
        }

        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('error', 'Tu rol ha sido actualizado. Por favor, inicia sesión nuevamente para aplicar los cambios.');
    }

    // -------------------------------------------------------------------------
    // PASSWORD RESET
    // -------------------------------------------------------------------------

    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('login')->withErrors(['email' => 'Enlace de recuperación inválido']);
        }

        return view('auth.passwords.reset', ['token' => $token, 'email' => $email]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['credential' => 'required|string']);

        \Illuminate\Support\Facades\Log::info('FRONTEND: Enviando solicitud de reseteo al backend', [
            'url' => $this->backendUrl() . '/api/password/email',
            'credential' => $request->credential,
            'frontend_url' => url('/'),
        ]);

        $response = Http::timeout(30)
            ->post($this->backendUrl() . '/api/password/email', [
                'credential' => $request->credential,
                'frontend_url' => url('/'),
            ]);

        \Illuminate\Support\Facades\Log::info('FRONTEND: Respuesta del backend recibida', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->failed()) {
            return back()->withErrors([
                'credential' => $response->json('message') ?? 'No se pudo procesar la solicitud.',
            ]);
        }

        return back()->with('status', 'Se ha enviado un enlace de recuperación de contraseña a su correo electrónico.');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'password' => [
                'required', 'string', 'min:6', 'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ], [
            'password.regex'     => 'La contraseña debe contener al menos una mayúscula, un número y un símbolo especial.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        \Illuminate\Support\Facades\Log::info('FRONTEND: Enviando actualización de contraseña al backend', [
            'email' => $request->email,
            'token' => $request->token,
        ]);

        $response = Http::timeout(30)
            ->post($this->backendUrl() . '/api/password/reset', [
                'token'                 => $request->token,
                'email'                 => $request->email,
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

        \Illuminate\Support\Facades\Log::info('FRONTEND: Respuesta de actualización de contraseña', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->failed()) {
            return back()->withErrors(['email' => $response->json('message') ?? 'Error al restablecer la contraseña.']);
        }

        return redirect()->route('login')
            ->with('status', 'Su contraseña ha sido restablecida exitosamente. Por favor inicie sesión con su nueva contraseña.');
    }

    public function showForceChangeForm()
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }
        return view('auth.passwords.force-change');
    }

    public function forceChange(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required', 'string', 'min:6', 'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ], [
            'password.regex'     => 'La contraseña debe contener al menos una mayúscula, un número y un símbolo especial.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $response = Http::withToken(Session::get('api_token'))
            ->timeout(30)
            ->post($this->backendUrl() . '/api/password/force-change', [
                'current_password'      => $request->current_password,
                'password'              => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

        if ($response->failed()) {
            return back()->withErrors(['current_password' => $response->json('message') ?? 'Error al cambiar la contraseña.']);
        }

        Session::flush();
        $request->session()->invalidate();

        return redirect()->route('login')
            ->with('status', 'Su contraseña ha sido cambiada exitosamente. Por favor inicie sesión nuevamente.');
    }
}
