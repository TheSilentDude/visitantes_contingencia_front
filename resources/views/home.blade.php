@extends('layouts.app')

@section('title', 'Inicio - Sistema de Carnetización')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center justify-content-center" style="margin-top: -80px;">
        <div class="col-xl-8 col-lg-9 col-md-10 text-center">
            
            <!-- Tarjeta de Bienvenida Principal -->
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,249,252,0.9) 100%); backdrop-filter: blur(10px);">
                
                <div class="card-body p-4 p-md-5">

                    <!-- Texto de Saludo -->
                    <h1 class="display-4 fw-bolder text-primary mb-3 font-weight-bold" style="letter-spacing: -1px;">
                        ¡Hola, {{ session('user_name', 'Usuario') }}!
                    </h1>
                    <p class="lead fw-normal text-muted mb-4 font-weight-light">
                        Bienvenido(a) a tu Escritorio Central del Sistema de Carnetización MINCYT.
                    </p>

                    <div class="my-4">
                        <hr class="border-primary" style="opacity: 0.15; width: 60px; border-width: 3px; border-radius: 5px;">
                    </div>

                    <!-- Mensaje Instructivo Neutro -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border-left-primary mb-4 text-left">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary-light">
                                    <i class="fas fa-compass text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-gray-800 mb-1">Tu Centro de Operaciones</h5>
                                <p class="text-gray-600 mb-0">Utiliza la <strong>Barra Lateral Izquierda</strong> para navegar hacia los módulos, herramientas y paneles en los que tienes permisos operativos autorizados por el Administrador.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Atajos Rápidos (Módulos Autorizados) -->
                    @php
                        $userPerms = session('user_permissions', []);
                        $hasAccess = count($userPerms) > 0;
                    @endphp
                    
                    @if(!$hasAccess)
                        <div class="alert alert-warning border-left-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle fa-2x mr-3 text-warning"></i>
                            <div class="text-left">
                                <strong>Aviso:</strong> Actualmente tu rol de acceso ('{{ session('user_rol', 'Básico') }}') no tiene módulos principales asociados. Si necesitas herramientas operativas, solicita su asignación.
                            </div>
                        </div>
                    @endif
                    
                </div>
                
                <!-- Footer Decorativo -->
                <div class="card-footer bg-white py-3 border-top-0 border-primary" style="border-top: 4px solid !important;">
                    <div class="d-flex align-items-center justify-content-center small text-muted">
                        <span class="mr-2"><i class="fas fa-clock text-primary"></i> Ingreso Registrado: <strong>{{ now()->format('d/m/Y h:i A') }}</strong></span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    body {
        /* Fondo visual agradable si se desea */
        background-color: #f8f9fc;
    }
    .bg-primary-light {
        background-color: rgba(78, 115, 223, 0.1);
        padding: 15px;
        border-radius: 50%;
    }
</style>
@endsection
