<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="{{ route('dashboard') }}">DEI<span>HEALTH</span></a>
        <a class="sidebar-brand brand-logo-mini" href="{{ route('dashboard') }}"><span class="text-success">D</span>H</a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc">
                <div class="profile-pic">
                    <div class="count-indicator">
                        <img class="img-xs rounded-circle" src="{{ auth()->user()->profile_photo_url }}" alt="Foto Profil" style="object-fit: cover;">
                        <span class="count bg-success"></span>
                    </div>
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal">{{ auth()->user()->name }}</h5>
                        <span>{{ auth()->user()->job_title ?: auth()->user()->role_label }}</span>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item nav-category">
            <span class="nav-link">Navigasi Utama</span>
        </li>
        <li class="nav-item menu-items {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        @can('manage-master-data')
        <li class="nav-item menu-items {{ request()->routeIs('santri*', 'classes*', 'majors*', 'dormitories*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ui-master" aria-expanded="{{ request()->routeIs('santri*', 'classes*', 'majors*', 'dormitories*') ? 'true' : 'false' }}" aria-controls="ui-master">
                <span class="menu-icon">
                    <i class="mdi mdi-database"></i>
                </span>
                <span class="menu-title">Master Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('santri*', 'classes*', 'majors*', 'dormitories*') ? 'show' : '' }}" id="ui-master">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('santri*') ? 'active' : '' }}" href="{{ route('santri.index') }}">Data Santri</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('classes*') ? 'active' : '' }}" href="{{ route('classes.index') }}">Data Kelas</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('majors*') ? 'active' : '' }}" href="{{ route('majors.index') }}">Data Jurusan</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('dormitories*') ? 'active' : '' }}" href="{{ route('dormitories.index') }}">Data Asrama</a></li>
                </ul>
            </div>
        </li>
        @endcan

        <li class="nav-item menu-items {{ request()->routeIs('medicines*', 'beds*', 'health-records*', 'sickness-cases*', 'referrals*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#ui-health" aria-expanded="{{ request()->routeIs('medicines*', 'beds*', 'health-records*', 'sickness-cases*', 'referrals*') ? 'true' : 'false' }}" aria-controls="ui-health">
                <span class="menu-icon">
                    <i class="mdi mdi-medical-bag"></i>
                </span>
                <span class="menu-title">Modul Kesehatan</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('medicines*', 'beds*', 'health-records*', 'sickness-cases*', 'referrals*') ? 'show' : '' }}" id="ui-health">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('medicines*') ? 'active' : '' }}" href="{{ route('medicines.index') }}">Stok Obat</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('beds*') ? 'active' : '' }}" href="{{ route('beds.index') }}">Kasur UKS</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('sickness-cases*') ? 'active' : '' }}" href="{{ route('sickness-cases.index') }}">Santri Sakit</a></li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('referrals*') ? 'active' : '' }}" href="{{ route('referrals.index') }}">Rujukan RS</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items {{ request()->routeIs('reports*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('reports.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document"></i>
                </span>
                <span class="menu-title">Laporan</span>
            </a>
        </li>

        @if(auth()->user()->isSuperAdmin())
        <li class="nav-item nav-category">
            <span class="nav-link">Administrasi</span>
        </li>
        <li class="nav-item menu-items {{ request()->routeIs('super-admin.users*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('super-admin.users.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-account-group"></i>
                </span>
                <span class="menu-title">Manajemen User</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->routeIs('super-admin.approvals*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('super-admin.approvals.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-check-decagram"></i>
                </span>
                <span class="menu-title">Persetujuan Akun</span>
            </a>
        </li>
        @endif

        <li class="nav-item menu-items {{ request()->routeIs('account.settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('account.settings.edit') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-account-cog"></i>
                </span>
                <span class="menu-title">Pengaturan Akun</span>
            </a>
        </li>

        <li class="nav-item nav-category">
            <span class="nav-link">Sesi</span>
        </li>
        <li class="nav-item menu-items">
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                @csrf
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <span class="menu-icon">
                        <i class="mdi mdi-logout text-danger"></i>
                    </span>
                    <span class="menu-title">Keluar Aplikasi</span>
                </a>
            </form>
        </li>
    </ul>
</nav>
