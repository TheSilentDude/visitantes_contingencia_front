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

    {{-- ===================== OPERATIVO ===================== --}}
    @if(in_array('acceso_total', $userPerms) || in_array('recepcion', $userPerms) || in_array('carnet_visitante', $userPerms))
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

    <hr class="sidebar-divider d-none d-md-block">
    @endif

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
