<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    protected function api(): string { return config('services.backend.url'); }
    protected function token(): string { return Session::get('api_token', ''); }

    /**
     * Convierte la respuesta JSON del API en objetos anidados (stdClass) y asegura claves que usa la vista.
     */
    private function userFromApiPayload(?array $data): object
    {
        if ($data === null || $data === []) {
            $data = [];
        }
        $user = json_decode(json_encode($data));
        if (! is_object($user)) {
            $user = (object) [];
        }
        if (! property_exists($user, 'empleado') || $user->empleado === []) {
            $user->empleado = null;
        }
        if (! property_exists($user, 'rol') || $user->rol === null || $user->rol === []) {
            $user->rol = (object) [
                'descripcion' => Session::get('user_rol', 'Usuario'),
                'rol' => Session::get('user_rol', 'Usuario'),
            ];
        }
        if (! property_exists($user, 'usuario') || $user->usuario === null) {
            $user->usuario = Session::get('user_name', 'Usuario');
        }
        if (! property_exists($user, 'created_at') || $user->created_at === null) {
            $user->created_at = now()->toIso8601String();
        }

        return $user;
    }

    private function profileRequestWantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function show()
    {
        $response = Http::withToken($this->token())
            ->get($this->api() . '/api/user');

        if ($response->ok()) {
            $user = $this->userFromApiPayload($response->json());
        } else {
            $user = $this->userFromApiPayload(Session::get('user', []));
        }

        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $wantsJson = $this->profileRequestWantsJson($request);

        $payload = $request->only(['usuario', 'email', 'telefono', 'direccion']);

        $pending = Http::withToken($this->token())->acceptJson();
        $url = $this->api() . '/api/profile';

        // Siempre POST hacia el API: PUT+multipart no popula archivos/campos en el servidor PHP.
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $response = $pending
                ->attach(
                    'avatar',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->post($url, $payload);
        } else {
            $response = $pending
                ->asForm()
                ->post($url, $payload);
        }

        if ($response->failed()) {
            $body = $response->json();
            $message = is_array($body) ? ($body['message'] ?? 'Error al actualizar perfil.') : $response->body();
            $errors = is_array($body) ? ($body['errors'] ?? []) : [];
            $firstFieldError = null;
            if (is_array($errors)) {
                foreach ($errors as $msgs) {
                    if (is_array($msgs) && isset($msgs[0])) {
                        $firstFieldError = $msgs[0];
                        break;
                    }
                }
            }

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $firstFieldError ?: $message,
                    'errors' => $errors,
                ], $response->status() >= 400 ? $response->status() : 422);
            }

            return back()->withErrors($errors ?: ['error' => $message]);
        }

        $userPayload = $response->json('user', []);
        if ($userPayload !== []) {
            Session::put('user', array_merge(Session::get('user', []), $userPayload));
        }

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => $response->json('message', 'Perfil actualizado correctamente.'),
                'user' => $userPayload,
            ]);
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $wantsJson = $this->profileRequestWantsJson($request);

        $response = Http::withToken($this->token())
            ->timeout(30)
            ->acceptJson()
            ->asJson()
            ->patch($this->api() . '/api/profile/password', $request->only([
                'current_password',
                'new_password',
                'new_password_confirmation',
            ]));

        if ($response->failed()) {
            $body = $response->json();
            $message = is_array($body) ? ($body['message'] ?? 'Error al cambiar contraseña.') : $response->body();

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => is_array($body) ? ($body['errors'] ?? []) : [],
                ], $response->status() >= 400 ? $response->status() : 422);
            }

            return back()->withErrors(is_array($body) ? ($body['errors'] ?? ['error' => $message]) : ['error' => $message]);
        }

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => $response->json('message', 'Contraseña actualizada correctamente.'),
            ]);
        }

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
