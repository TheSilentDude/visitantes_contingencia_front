<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema integral de gestión de recursos humanos y carnetización de personal. Búsqueda, edición y actualización de datos de empleados.">
    <meta name="author" content="Sistema de Carnetización">
    <meta name="keywords" content="RRHH, recursos humanos, carnetización, gestión personal, empleados">
    
    <title>@yield('title', 'Sistema de Carnetización')</title>
    
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Fuente Georama para carnets -->
    <style>
        @font-face {
            font-family: 'Georama';
            src: url('{{ asset('font/Georama/Georama-VariableFont_wdth,wght.ttf') }}') format('truetype');
            font-weight: 100 900;
            font-stretch: 62.5% 150%;
            font-display: swap;
        }
    </style>

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    
    <!-- Custom RRHH styles -->
    <link href="{{ asset('css/rrhh/responsive-improvements.css') }}" rel="stylesheet">
    
    <!-- Estilos personalizados para el header RRHH -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        
        .icon-circle {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.1) !important;
        }
        
        .input-group-lg .form-control {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }
        
        .input-group-text {
            border: 1px solid #d1d3e2;
        }
        
        .border-start-0 {
            border-left: 0 !important;
        }
        
        .border-end-0 {
            border-right: 0 !important;
        }
        
        .opacity-75 {
            opacity: 0.75;
        }
        
        .text-md-end {
            text-align: right !important;
        }
        
        @media (max-width: 768px) {
            .text-md-end {
                text-align: left !important;
                margin-top: 1rem;
            }
            
            .icon-circle {
                width: 50px !important;
                height: 50px !important;
            }
            
            .icon-circle i {
                font-size: 1.5rem !important;
            }
        }
        
        /* Animación para el contador */
        #searchCounter {
            transition: all 0.3s ease;
        }
        
        #searchCounter.updated {
            transform: scale(1.2);
            color: #28a745 !important;
        }
        
        /* Asegurar que el contenido no se oculte detrás del header */
        #content {
            margin-top: 0;
        }

        /*
         * Layout pantalla completa + menús desplegables:
         * sb-admin fuerza overflow-x:hidden en #content-wrapper, lo que en CSS hace que
         * overflow-y pase a "auto" y recorte dropdowns largos. Se anula con overflow visible.
         */
        html {
            height: 100%;
        }
        body {
            min-height: 100vh;
            background-color: #f8f9fc;
        }
        #wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #wrapper #content-wrapper {
            min-height: 100vh;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow-x: visible;
            overflow-y: visible;
            background-color: #f8f9fc;
        }
        #wrapper #content-wrapper #content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        #wrapper #content-wrapper #content > .container-fluid {
            flex: 1 1 auto;
        }

        /* Dropdown "Menú de Navegación": no usar position:static del .topbar (recorta con el nav) */
        .topbar .dropdown.dropdown-modulo-nav {
            position: relative !important;
        }
        .topbar .dropdown.dropdown-modulo-nav .dropdown-menu {
            z-index: 1060;
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    
    @stack('styles')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Barra del Gobierno -->
                <img src="{{ asset('BARRA_gob.svg') }}" alt="Barra del Gobierno" style="width: 100%; height: auto;">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="z-index: 1030;">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    @php
                        $userPerms = session('user_permissions', []);
                    @endphp

                    <!-- Botón Inicio -->
                    <a href="{{ route('home') }}" class="btn btn-light btn-sm d-flex align-items-center mr-2 shadow-sm" style="border-radius: 20px; padding: 6px 16px; border: 1px solid #e3e6f0;">
                        <i class="fas fa-home text-primary mr-1"></i>
                        <span class="d-none d-md-inline font-weight-bold text-gray-700">Inicio</span>
                    </a>

                    <!-- Dropdown Menú de Navegación -->
                    <div class="d-flex align-items-center dropdown dropdown-modulo-nav mr-3">
                        <a class="btn btn-light d-flex align-items-center text-decoration-none shadow-sm" href="#" id="moduloDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 20px; padding: 6px 16px; border: 1px solid #e3e6f0; color: inherit;">
                            <i class="fas fa-th-large text-primary mr-2"></i>
                            <div class="d-none d-md-block text-left pr-2" style="line-height: 1;">
                                <span class="font-weight-bold text-gray-700">Menú de Navegación <i class="fas fa-chevron-down ml-1" style="font-size: 0.8rem; color: #ccc;"></i></span><br>
                                <small class="text-muted" style="font-size: 0.7rem;">Accesos rápidos a módulos y funciones</small>
                            </div>
                        </a>

                        <div class="dropdown-menu shadow animated--grow-in" aria-labelledby="moduloDropdown" style="width: 380px; left: 0;">

                            {{-- ========== OPERATIVO ========== --}}
                            @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms) || in_array('carnet_visitante', $userPerms))
                            <h6 class="dropdown-header text-info font-weight-bold"><i class="fas fa-clipboard-list mr-1"></i> Operativo</h6>

                            @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms))
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('recepcion.dashboard') }}">
                                <div class="mr-3">
                                    <div class="icon-circle bg-white shadow-sm text-info" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-door-open fa-sm"></i>
                                    </div>
                                </div>
                                <span>Recepción</span>
                            </a>
                            @endif

                            @if(in_array('acceso_total', $userPerms) || in_array('carnet_visitante', $userPerms))
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('admin.carnets.visitantes.index') }}">
                                <div class="mr-3">
                                    <div class="icon-circle bg-white shadow-sm text-info" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-address-card fa-sm"></i>
                                    </div>
                                </div>
                                <span>Carnet Visitante</span>
                            </a>
                            @endif
                            @endif

                            {{-- Si no tiene ningún permiso --}}
                            @if(empty($userPerms))
                            <span class="dropdown-item text-muted small">Sin módulos asignados</span>
                            @endif
                        </div>
                    </div>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Información de Fecha -->
                        <li class="nav-item d-none d-lg-block">
                            <div class="nav-link text-gray-600 small">
                                <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>
                                <span id="currentDate"></span>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        @if(session('api_token'))
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                        <i class="fas fa-user-circle" style="margin-right: 6px;"></i>
                                        {{ session('user_name', 'Usuario') }}
                                    </span>
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Mi Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white mt-auto" style="position: relative; z-index: 1;">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; MINCYT {{ date('Y') }} - Sistema de Carnetización</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

    <!-- Sistema de notificaciones -->
    <script src="{{ asset('js/notifications.js') }}"></script>

    <!-- JavaScript para inicializar fecha -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar fecha actual
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const dateElement = document.getElementById('currentDate');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('es-ES', options);
            }

            // Polling para verificar force_logout
            @if(session('api_token'))
                setInterval(function() {
                    fetch('{{ route("check.force.logout") }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.force_logout) {
                            window.location.href = '{{ route("forced.logout") }}';
                        }
                    })
                    .catch(error => console.error('Error checking force logout:', error));
                }, 20000); // Cada 20 s si el usuario permanece en la misma página (sin navegar)
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>