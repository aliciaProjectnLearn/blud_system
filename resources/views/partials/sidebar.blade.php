{{-- Sidebar --}}
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    {{-- Sidebar Brand --}}
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-fw fa-layer-group"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name', 'MyApp') }}</div>
        
    </a>

    {{-- Divider --}}
    <hr class="sidebar-divider my-0">

    {{-- Dashboard --}}
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
    </li>
    <li class="nav-item">
    <a class="nav-link" href="{{ route('monitoring.index') }}">
        <i class="fas fa-fw fa-eye"></i>
        <span>Monitoring Aktivitas</span>
    </a>
    </li>   

    {{-- Divider --}}
    <hr class="sidebar-divider">

    {{-- Heading --}}
    <div class="sidebar-heading">
        Menu Utama
    </div>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.index') }}">                    
            <i class="fas fa-users"></i>
            <span>Kelola User</span></a>
    </li>

    {{-- Contoh: Menu dengan Submenu --}}
    {{--
    <li class="nav-item {{ request()->routeIs('contoh.*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseContoh"
            aria-expanded="true" aria-controls="collapseContoh">
            <i class="fas fa-fw fa-cog"></i>
            <span>Contoh Menu</span>
        </a>
        <div id="collapseContoh" class="collapse {{ request()->routeIs('contoh.*') ? 'show' : '' }}"
            aria-labelledby="headingContoh" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Sub Menu:</h6>
                <a class="collapse-item {{ request()->routeIs('contoh.index') ? 'active' : '' }}"
                    href="{{ route('contoh.index') }}">List</a>
                <a class="collapse-item {{ request()->routeIs('contoh.create') ? 'active' : '' }}"
                    href="{{ route('contoh.create') }}">Tambah</a>
            </div>
        </div>
    </li>
    --}}

    {{-- Divider --}}
    <hr class="sidebar-divider d-none d-md-block">

    {{-- Sidebar Toggle Button --}}
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
{{-- End of Sidebar --}}
