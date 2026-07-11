@extends('layouts.auth')

@section('title', 'Carnetización')

@section('content')
<form action="{{ route('login.post') }}" method="POST" novalidate id="loginForm">
    @csrf 
    <div class="login_container">
        <img class="logo_mincyt" src="{{ asset('css/LOGO_login.svg') }}" alt="logo">
        <h2>Identificación <br><span>MINCYT</span></h2>
        <div class="login_inputs">
            <p>Iniciar sesión</p>
            <div class="input-group">
                <input type="text" class="input-field" id="usuario" name="usuario" placeholder="Usuario" value="{{ old('usuario') }}" required autofocus>
                <label for="usuario" class="input-label">Usuario</label>
            </div>
            <div class="input-group">
                <input type="password" class="input-field" id="clave" name="clave" placeholder="Contraseña" required>
                <label for="clave" class="input-label">Contraseña</label>
            </div>
            <div id="mensaje" style="display:none;"></div>
            @if ($errors->any())
                <div class="login-message login-message--error" role="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            @if (session('error'))
                <div class="login-message login-message--error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('debug_response') || session('debug_exception'))
                <script>
                    console.error("====== DEBUG LOGIN BACKEND ======");
                    console.error("URL Backend:", {!! json_encode(session('debug_url')) !!});
                    @if(session('debug_status'))
                        console.error("HTTP Status:", {{ session('debug_status') }});
                        console.error("Response Body:", {!! json_encode(session('debug_response')) !!});
                    @endif
                    @if(session('debug_exception'))
                        console.error("Exception:", {!! json_encode(session('debug_exception')) !!});
                    @endif
                    console.error("=================================");
                </script>
            @endif

            <button class="login_button" type="submit" id="loginButton">Entrar</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('password.request') }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                    ¿Olvidó su contraseña?
                </a>
            </div>
        </div>
    </div>
</form>


@endsection
