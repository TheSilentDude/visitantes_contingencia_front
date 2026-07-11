<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Carnetización MINCYT')</title>
    
    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Custom RRHH styles -->
    <link href="{{ asset('css/rrhh/responsive-improvements.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="sidebar-brand-text mx-3">MINCYT</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard Base -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Mi Escritorio Principal</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Gestión
            </div>

            @php $userPerms = session('user_permissions', []); @endphp
            @if(in_array('acceso_total', $userPerms) || in_array('ver_dashboard', $userPerms)) <!-- Admin -->
                <!-- Nav Item - Carnets -->
                <li class="nav-item {{ request()->routeIs('admin.carnets.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.carnets.create') }}">
                        <i class="fas fa-fw fa-id-card"></i>
                        <span>Carnets</span></a>
                </li>
            @endif

            @if(in_array('acceso_total', $userPerms) || in_array('ver_usuarios', $userPerms)) <!-- RRHH -->
                <!-- Nav Item - RRHH -->
                <li class="nav-item {{ request()->routeIs('rrhh.*') ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRRHH"
                        aria-expanded="true" aria-controls="collapseRRHH">
                        <i class="fas fa-fw fa-user-tie"></i>
                        <span>Recursos Humanos</span>
                    </a>
                    <div id="collapseRRHH" class="collapse {{ request()->routeIs('rrhh.*') ? 'show' : '' }}" aria-labelledby="headingRRHH" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Gestión de Personal:</h6>
                            <a class="collapse-item {{ request()->routeIs('rrhh.dashboard') || request()->routeIs('rrhh.usuarios.*') ? 'active' : '' }}" href="{{ route('rrhh.dashboard') }}">
                                <i class="fas fa-users me-1"></i> Empleados
                            </a>
                            <a class="collapse-item {{ request()->routeIs('rrhh.carnets.*') ? 'active' : '' }}" href="{{ route('rrhh.carnets.index') }}">
                                <i class="fas fa-id-card me-1"></i> Carnets
                            </a>
                        </div>
                    </div>
                </li>
            @endif

            @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms)) <!-- Recepción -->
                <!-- Nav Item - Recepción -->
                <li class="nav-item {{ request()->routeIs('recepcion.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('recepcion.dashboard') }}">
                        <i class="fas fa-fw fa-door-open"></i>
                        <span>Recepción</span></a>
                </li>
            @endif

            @if(in_array('acceso_total', $userPerms) || in_array('supervisar_rotacion', $userPerms)) <!-- Rotación -->
                <!-- Nav Item - Rotación -->
                <li class="nav-item {{ request()->routeIs('rotacion.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('rotacion.dashboard') }}">
                        <i class="fas fa-fw fa-sync-alt"></i>
                        <span>Rotación</span></a>
                </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ session('user_name', 'Usuario') }}
                                    <span class="badge badge-primary">{{ ucfirst(session('user.rol.descripcion', 'Usuario')) }}</span>
                                </span>
                                <i class="fas fa-user-circle fa-fw fa-2x text-gray-400"></i>
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
            <footer class="sticky-footer bg-white">
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

    @stack('scripts')

</body>
</html>