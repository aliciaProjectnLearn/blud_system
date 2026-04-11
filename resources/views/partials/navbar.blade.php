{{-- Topbar --}}
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    {{-- Sidebar Toggle (Mobile) --}}
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    {{-- Topbar Search --}}
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Cari..."
                aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Topbar Navbar --}}
    <ul class="navbar-nav ml-auto">

        {{-- Search Mobile --}}
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Cari..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Divider --}}
        <div class="topbar-divider d-none d-sm-block"></div>

        {{-- User Info --}}
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    {{ Auth::user()->name ?? 'User' }}
                </span>
                <div class="img-profile rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px">
                    <i class="fas fa-user text-white"></i>
                </div>
            </a>

            {{-- Dropdown User Menu --}}
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">

                {{-- Link Profile sesuai role --}}
                @if(Auth::user()->hasRole('Superadmin'))
                    <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Dashboard
                    </a>
                @elseif(Auth::user()->hasRole('Adminfutsal'))
                    <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('adminfutsal.dashboard') ? 'active' : '' }}"
                       href="{{ route('adminfutsal.dashboard') }}">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Dashboard
                    </a>
                @elseif(Auth::user()->hasRole('Adminkantin'))
                    <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('adminkantin.dashboard') ? 'active' : '' }}"
                       href="{{ route('adminkantin.dashboard') }}">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Dashboard
                    </a>
                @elseif(Auth::user()->hasRole('Adminac'))
                    <a class="dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                       href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item {{ request()->routeIs('adminac.dashboard') ? 'active' : '' }}"
                       href="{{ route('adminac.dashboard') }}">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Dashboard
                    </a>
                @else
                    <a class="dropdown-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}"
                       href="{{ route('user.profile.index') }}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                        <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Dashboard Saya
                    </a>
                @endif

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>
{{-- End of Topbar --}}
