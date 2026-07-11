@extends('layouts.auth')

@section('title', 'Cambio de Rol')

@section('content')
<div class="login_container" style="padding: 40px 30px; border-top: 5px solid #e74a3b;">
    <div style="text-align: center; margin-bottom: 20px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#e74a3b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
    </div>
    
    <h2 style="font-size: 1.5rem; margin-bottom: 20px; text-align: center; color: #333; font-weight: bold;">Permisos Actualizados</h2>
    
    <div style="text-align: center; margin-bottom: 20px; line-height: 1.6; color: #555; font-size: 16px;">
        <p style="margin-bottom: 15px;">Tu rol y nivel de acceso han sido modificados remotamente por un administrador.</p>
        <p>Por políticas de seguridad, el sistema debe recargar tus credenciales en</p>
        <strong id="logout_countdown" style="font-size: 2.5rem; color: #e74a3b; display: block; margin: 10px 0;">10</strong>
        <p style="font-size: 13px; color: #999;">Serás redirigido automáticamente...</p>
    </div>
    
    <form id="executeForcedLogoutForm" action="{{ route('execute.forced.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = 10;
    const countdownEl = document.getElementById('logout_countdown');
    const formEl = document.getElementById('executeForcedLogoutForm');
    
    const timer = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownEl.textContent = '...';
            formEl.submit();
        }
    }, 1000);
});
</script>
@endsection
