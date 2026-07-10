@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('content')
<form action="{{ route('password.email') }}" method="POST" novalidate>
    @csrf
    <div class="login_container">
        <img class="logo_mincyt" src="{{ asset('css/LOGO_login.svg') }}" alt="logo">
        <h2>Recuperar <br><span>Contraseña</span></h2>
        <div class="login_inputs">
            <p>Ingrese su usuario o correo electrónico</p>
            <div class="input-group">
                <input type="text" class="input-field" id="credential" name="credential" placeholder="Usuario o Correo" value="{{ old('credential') }}" required autofocus>
                <label for="credential" class="input-label">Usuario o Correo</label>
            </div>
            @if ($errors->any())
                <div class="login-message login-message--error" role="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            @if (session('status'))
                <div class="login-message login-message--success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <button class="login_button" type="submit">Enviar Enlace</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('login') }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                    Volver al Login
                </a>
            </div>
        </div>
    </div>
</form>
@endsection