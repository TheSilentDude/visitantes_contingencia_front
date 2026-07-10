@php $userPerms = session('user_permissions', []); @endphp

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="sidebar-brand-text mx-3">MINCYT</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Inicio (siempre visible) -->
    <li class="nav-item {{ request()->routeIs('home') || request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Inicio</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    {{-- ===================== ADMINISTRACIÓN ===================== --}}
    @if(in_array('acceso_total', $userPerms) || in_array('ver_dashboard', $userPerms) || in_array('gestionar_roles', $userPerms) || in_array('estructura_logos', $userPerms))
    <div class="sidebar-heading">
        Administración
    </div>

    @if(in_array('acceso_total', $userPerms) || in_array('ver_dashboard', $userPerms))
    <!-- Nav Item - Dashboard Admin -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard', 'admin.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('gestionar_roles', $userPerms))
    <!-- Nav Item - Gestionar Roles -->
    <li class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.roles.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Gestionar Roles</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('estructura_logos', $userPerms))
    <!-- Nav Item - Estructura/Logos -->
    <li class="nav-item {{ request()->routeIs('admin.instituciones.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.instituciones.logos.index') }}">
            <i class="fas fa-fw fa-building"></i>
            <span>Estructura/Logos</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider">
    @endif

    {{-- ===================== RRHH / USUARIOS ===================== --}}
    @if(in_array('acceso_total', $userPerms) || in_array('ver_usuarios', $userPerms) || in_array('crear_personal', $userPerms) || in_array('generar_carnets', $userPerms))
    <div class="sidebar-heading">
        RRHH / Usuarios
    </div>

    @if(in_array('acceso_total', $userPerms) || in_array('ver_usuarios', $userPerms))
    <!-- Nav Item - Empleados -->
    <li class="nav-item {{ request()->routeIs('rrhh.dashboard') || request()->routeIs('rrhh.usuarios.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('rrhh.dashboard') }}">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Empleados</span>
        </a>
    </li>

    <!-- Nav Item - Carnets RRHH -->
    <li class="nav-item {{ request()->routeIs('rrhh.carnets.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('rrhh.carnets.index') }}">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Carnets Empleados</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('generar_carnets', $userPerms))
    <!-- Nav Item - Generar Carnets -->
    <li class="nav-item {{ request()->routeIs('admin.carnets.empleados.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.carnets.empleados.index') }}">
            <i class="fas fa-fw fa-print"></i>
            <span>Generar Carnets</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider">
    @endif

    {{-- ===================== OPERATIVO ===================== --}}
    @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms) || in_array('rotacion', $userPerms) || in_array('imp_reversos', $userPerms) || in_array('carnet_visitante', $userPerms))
    <div class="sidebar-heading">
        Operativo
    </div>

    @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms))
    <!-- Nav Item - Recepción -->
    <li class="nav-item {{ request()->routeIs('recepcion.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('recepcion.dashboard') }}">
            <i class="fas fa-fw fa-door-open"></i>
            <span>Recepción</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('carnet_visitante', $userPerms))
    <!-- Nav Item - Carnet Visitante -->
    <li class="nav-item {{ request()->routeIs('admin.carnets.visitantes.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.carnets.visitantes.index') }}">
            <i class="fas fa-fw fa-address-card"></i>
            <span>Carnet Visitante</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('rotacion', $userPerms))
    <!-- Nav Item - Rotación -->
    <li class="nav-item {{ request()->routeIs('rotacion.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('rotacion.index') }}">
            <i class="fas fa-fw fa-sync-alt"></i>
            <span>Rotación</span>
        </a>
    </li>
    @endif

    @if(in_array('acceso_total', $userPerms) || in_array('imp_reversos', $userPerms))
    <!-- Nav Item - Imp. Reversos -->
    <li class="nav-item {{ request()->routeIs('impresiones_reversa.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('impresiones_reversa.dashboard') }}">
            <i class="fas fa-fw fa-print"></i>
            <span>Imp. Reversos</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">
    @endif

    {{-- ===================== VEHÍCULOS ===================== --}}
    @if(in_array('acceso_total', $userPerms) || in_array('registro_vehiculos', $userPerms) || in_array('asignar_puestos', $userPerms) || in_array('asignar_cupos', $userPerms))
    <div class="sidebar-heading">
        Vehículos
    </div>

    <!-- Nav Item - Vehículos Collapse Menu -->
    <li class="nav-item {{ request()->routeIs('vehiculos.*', 'admin.puestos.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVehiculos" aria-expanded="true" aria-controls="collapseVehiculos">
            <i class="fas fa-fw fa-car"></i>
            <span>Vehículos</span>
        </a>
        <div id="collapseVehiculos" class="collapse" aria-labelledby="headingVehiculos" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Opciones:</h6>
                @if(in_array('acceso_total', $userPerms) || in_array('registro_vehiculos', $userPerms))
                <a class="collapse-item" href="{{ route('vehiculos.registro') }}">Registro de Vehículos</a>
                @endif
                @if(in_array('acceso_total', $userPerms) || in_array('monitorear_accesos_vehiculares', $userPerms))
                <a class="collapse-item text-success font-weight-bold" href="{{ route('vehiculos.accesos') }}"><i class="fas fa-video fa-sm mr-1"></i> Accesos en Vivo</a>
                @endif
                @if(in_array('acceso_total', $userPerms) || in_array('asignar_puestos', $userPerms))
                <a class="collapse-item" href="{{ route('admin.puestos.index') }}">Asignar Puestos</a>
                @endif
            </div>
        </div>
    </li>

    @if(in_array('acceso_total', $userPerms) || in_array('asignar_cupos', $userPerms))
    <!-- Nav Item - Asignar Cupos -->
    <li class="nav-item {{ request()->routeIs('admin.cupos.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.cupos.index') }}">
            <i class="fas fa-fw fa-list-ol"></i>
            <span>Asignar Cupos</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">
    @endif

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
