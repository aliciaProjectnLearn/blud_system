
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    
    <ul class="navbar-nav ml-auto">

        
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
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
            </div>
        </li>

        
        <div class="topbar-divider d-none d-sm-block"></div>

        
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo e(Auth::user()->name ?? 'User'); ?>

                </span>
                <div class="img-profile rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px">
                    <i class="fas fa-user text-white"></i>
                </div>
            </a>

            
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <?php if(auth()->guard()->check()): ?>
                    
                    <?php if(Auth::user()->hasRole('Superadmin')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Adminfutsal')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.futsal.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.futsal.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Adminkantin')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.kantin.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.kantin.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Adminac')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.ac.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.ac.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Adminservis')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.servis.profile') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.servis.profile')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('admin.servis.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('admin.servis.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Kasir')): ?>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('kasir.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('kasir.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Teknisi')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('teknisi.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('teknisi.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php elseif(Auth::user()->hasRole('Teknisi Motor') || Auth::user()->hasRole('Teknisi Mobil')): ?>
                        <a class="dropdown-item <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>"
                            href="<?php echo e(route('profile.edit')); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item <?php echo e(request()->routeIs('teknisi.servis.dashboard') ? 'active' : ''); ?>"
                            href="<?php echo e(route('teknisi.servis.dashboard')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a class="dropdown-item" href="<?php echo e(route('user.gateway')); ?>">
                            <i class="fas fa-tachometer-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Dashboard Utama
                        </a>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                <?php else: ?>
                    <a class="dropdown-item" href="<?php echo e(route('staff.login')); ?>">
                        <i class="fas fa-sign-in-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Login Admin/Staff
                    </a>
                <?php endif; ?>
            </div>
        </li>

    </ul>

</nav>

<?php /**PATH C:\laragon\www\blud_system\resources\views/partials/navbar.blade.php ENDPATH**/ ?>