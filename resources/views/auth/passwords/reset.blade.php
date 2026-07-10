@extends('layouts.auth')

@section('title', 'Restablecer Contraseña')

@section('content')
<form action="{{ route('password.update', ['token' => $token, 'email' => $email]) }}" method="POST" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
    <div class="login_container">
        <img class="logo_mincyt" src="{{ asset('css/LOGO_login.svg') }}" alt="logo">
        <h2>Nueva <br><span>Contraseña</span></h2>
        <div class="login_inputs">
            <p>Restablecer contraseña para:</p>
            <div class="input-group">
                <input type="email" class="input-field" value="{{ $email }}" disabled>
                <label class="input-label">Correo Electrónico</label>
            </div>
            <div class="input-group">
                <input type="password" class="input-field" id="password" name="password" placeholder="Nueva Contraseña" required>
                <label for="password" class="input-label">Nueva Contraseña</label>
            </div>
            <div class="input-group">
                <input type="password" class="input-field" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                <label for="password_confirmation" class="input-label">Confirmar Contraseña</label>
            </div>
            
            <div style="font-size: 12px; color: #666; margin-bottom: 15px; text-align: left; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                <strong>Requisitos de contraseña:</strong>
                <ul style="margin: 5px 0 0 20px; padding: 0; list-style-type: none;">
                    <li id="req-length" style="color: #dc3545;">✘ Mínimo 6 caracteres</li>
                    <li id="req-upper" style="color: #dc3545;">✘ Al menos una mayúscula</li>
                    <li id="req-number" style="color: #dc3545;">✘ Al menos un número</li>
                    <li id="req-special" style="color: #dc3545;">✘ Al menos un símbolo especial</li>
                    <li id="req-match" style="color: #dc3545;">✘ Las contraseñas coinciden</li>
                </ul>
            </div>
            @if ($errors->any())
                <div class="login-message login-message--error" role="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif
            <button class="login_button" type="submit" id="submit-btn" disabled style="opacity: 0.6; cursor: not-allowed;">Restablecer Contraseña</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="{{ route('login') }}" style="color: #007bff; text-decoration: none; font-size: 14px;">
                    Volver al Login
                </a>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submit-btn');
    
    const reqLength = document.getElementById('req-length');
    const reqUpper = document.getElementById('req-upper');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    const reqMatch = document.getElementById('req-match');

    function validatePassword() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        let valid = true;

        // Length
        if (password.length >= 6) {
            reqLength.innerHTML = '✔ Mínimo 6 caracteres';
            reqLength.style.color = '#28a745';
        } else {
            reqLength.innerHTML = '✘ Mínimo 6 caracteres';
            reqLength.style.color = '#dc3545';
            valid = false;
        }

        // Uppercase
        if (/[A-Z]/.test(password)) {
            reqUpper.innerHTML = '✔ Al menos una mayúscula';
            reqUpper.style.color = '#28a745';
        } else {
            reqUpper.innerHTML = '✘ Al menos una mayúscula';
            reqUpper.style.color = '#dc3545';
            valid = false;
        }

        // Number
        if (/\d/.test(password)) {
            reqNumber.innerHTML = '✔ Al menos un número';
            reqNumber.style.color = '#28a745';
        } else {
            reqNumber.innerHTML = '✘ Al menos un número';
            reqNumber.style.color = '#dc3545';
            valid = false;
        }

        // Special char
        if (/[\W_]/.test(password)) {
            reqSpecial.innerHTML = '✔ Al menos un símbolo especial';
            reqSpecial.style.color = '#28a745';
        } else {
            reqSpecial.innerHTML = '✘ Al menos un símbolo especial';
            reqSpecial.style.color = '#dc3545';
            valid = false;
        }

        // Match
        if (password && password === confirm) {
            reqMatch.innerHTML = '✔ Las contraseñas coinciden';
            reqMatch.style.color = '#28a745';
        } else {
            reqMatch.innerHTML = '✘ Las contraseñas coinciden';
            reqMatch.style.color = '#dc3545';
            valid = false;
        }

        if (valid) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    passwordInput.addEventListener('input', validatePassword);
    confirmInput.addEventListener('input', validatePassword);
});
</script>
@endsection