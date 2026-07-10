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
            
            <!-- ReCAPTCHA Widget -->
       <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
@error('g-recaptcha-response')
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
@enderror


            <button class="login_button" type="submit" id="loginButton">Entrar</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('password.request') }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                    ¿Olvidó su contraseña?
                </a>
            </div>
        </div>
    </div>
</form>

<script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const captcha = document.querySelector('[name="g-recaptcha-response"]');
        if (!captcha || !captcha.value) {
            e.preventDefault();
            const mensajeDiv = document.getElementById('mensaje');
            mensajeDiv.style.display = 'block';
            mensajeDiv.style.backgroundColor = '#f8d7da';
            mensajeDiv.style.color = '#721c24';
            mensajeDiv.style.border = '1px solid #f5c6cb';
            mensajeDiv.style.padding = '10px';
            mensajeDiv.style.marginTop = '10px';
            mensajeDiv.style.borderRadius = '6px';
            mensajeDiv.innerHTML = '<strong>Error:</strong> Debes completar el captcha.';
            return;
        }
    });
</script>

@endsection
