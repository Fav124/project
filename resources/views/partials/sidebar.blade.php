<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo d-flex align-items-center">
                    <img src="{{ asset('assets/images/logo/dei.png') }}" alt="Logo" style="width: 40px; height: 40px;" class="me-2 rounded shadow-sm">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none">
                        <h4 class="text-primary fw-bold mb-0" style="letter-spacing: 1px;">DEI HEALTH</h4>
                    </a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu Utama</li>

                <li class="sidebar-item {{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if(Auth::user()->role->value === 'super_admin' || Auth::user()->role->value === 'admin')
                <li class="sidebar-item has-sub {{ Request::is('santri*') || Request::is('obat*') || Request::is('master*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-database-fill"></i>
                        <span>Data Master</span>
                    </a>
                    <ul class="submenu {{ Request::is('santri*') || Request::is('obat*') || Request::is('master*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('santri*') ? 'active' : '' }}">
                            <a href="{{ route('santri.index') }}">Data Santri</a>
                        </li>
                        <li class="submenu-item {{ Request::is('obat*') ? 'active' : '' }}">
                            <a href="{{ route('obat.index') }}">Data Obat</a>
                        </li>
                        <li class="submenu-item {{ Request::is('master/kelas*') ? 'active' : '' }}">
                            <a href="{{ route('kelas.index') }}">Data Kelas</a>
                        </li>
                        <li class="submenu-item {{ Request::is('master/jurusan*') ? 'active' : '' }}">
                            <a href="{{ route('jurusan.index') }}">Data Jurusan</a>
                        </li>
                        <li class="submenu-item {{ Request::is('master/kamar*') ? 'active' : '' }}">
                            <a href="{{ route('kamar.index') }}">Data Kamar</a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="sidebar-item has-sub {{ Request::is('kunjungan*') || Request::is('rawat-inap*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-heart-pulse-fill"></i>
                        <span>Pelayanan Medis</span>
                    </a>
                    <ul class="submenu {{ Request::is('kunjungan*') || Request::is('rawat-inap*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('kunjungan*') ? 'active' : '' }}">
                            <a href="{{ route('kunjungan.index') }}">Kunjungan (Pemeriksaan)</a>
                        </li>
                        <li class="submenu-item {{ Request::is('rawat-inap*') ? 'active' : '' }}">
                            <a href="{{ route('rawat-inap.index') }}">Monitoring Santri Sakit</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub {{ Request::is('laporan*') || Request::is('audit-logs*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-bar-graph-fill"></i>
                        <span>Laporan & Audit</span>
                    </a>
                    <ul class="submenu {{ Request::is('laporan*') || Request::is('audit-logs*') ? 'active' : '' }}">
                        <li class="submenu-item {{ Request::is('laporan*') ? 'active' : '' }}">
                            <a href="{{ route('laporan.index') }}">Rekap Laporan</a>
                        </li>
                        @if(Auth::user()->role->value === 'super_admin' || Auth::user()->role->value === 'admin')
                        <li class="submenu-item {{ Request::is('audit-logs*') ? 'active' : '' }}">
                            <a href="{{ route('audit-logs.index') }}">Audit Log Aktivitas</a>
                        </li>
                        @endif
                    </ul>
                </li>

                <li class="sidebar-item has-sub {{ Request::is('approvals*') || Request::is('backups*') || Request::is('users*') || Request::is('settings*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-gear-fill"></i>
                        <span>Manajemen Sistem</span>
                    </a>
                    <ul class="submenu {{ Request::is('approvals*') || Request::is('backups*') || Request::is('users*') || Request::is('settings*') ? 'active' : '' }}">
                        @if(Auth::user()->role->value === 'super_admin' || Auth::user()->role->value === 'admin')
                        <li class="submenu-item {{ Request::is('approvals*') ? 'active' : '' }}">
                            <a href="{{ route('approvals.index') }}" class="d-flex justify-content-between align-items-center">
                                <span>Persetujuan (Approval)</span>
                                @if($pendingApprovalsCount > 0)
                                    <span class="badge bg-danger rounded-pill" style="font-size: 10px;">{{ $pendingApprovalsCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->role->value === 'super_admin')
                        <li class="submenu-item {{ Request::is('users*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}">Data Pengguna</a>
                        </li>
                        <li class="submenu-item {{ Request::is('settings*') ? 'active' : '' }}">
                            <a href="{{ route('settings.index') }}">Pengaturan Aplikasi</a>
                        </li>
                        <li class="submenu-item {{ Request::is('backups*') ? 'active' : '' }}">
                            <a href="{{ route('backups.index') }}">Backup Database</a>
                        </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
