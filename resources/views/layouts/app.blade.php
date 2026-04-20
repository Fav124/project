<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $currentUser = auth()->user();
        $roleTheme = match ($currentUser->role ?? 'petugas_kesehatan') {
            'super_admin' => [
                'start' => '#0f172a',    // Slate 900
                'end' => '#312e81',      // Indigo 900
                'accent' => '#38bdf8',   // Sky 400
                'glow' => 'rgba(56, 189, 248, 0.2)',
                'label' => 'Super Administrator',
                'class' => 'theme-super-admin'
            ],
            'admin' => [
                'start' => '#1e1b4b',    // Indigo 950
                'end' => '#4338ca',      // Indigo 700
                'accent' => '#c084fc',   // Purple 400
                'glow' => 'rgba(192, 132, 252, 0.2)',
                'label' => 'Administrator',
                'class' => 'theme-admin'
            ],
            default => [
                'start' => '#064e3b',    // Emerald 950
                'end' => '#059669',      // Emerald 600
                'accent' => '#34d399',   // Emerald 400
                'glow' => 'rgba(52, 211, 153, 0.2)',
                'label' => 'Petugas UKS',
                'class' => 'theme-petugas'
            ],
        };
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DeisaHealth') | Aplikasi Kesehatan</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --brand-start: {{ $roleTheme['start'] }};
            --brand-end: {{ $roleTheme['end'] }};
            --brand-accent: {{ $roleTheme['accent'] }};
            --brand-glow: {{ $roleTheme['glow'] }};
            
            --primary: {{ $roleTheme['start'] }};
            --primary-light: {{ $roleTheme['accent'] }}22;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            
            --sidebar-w: 280px;
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-sm: 8px;
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-glow: 0 0 20px var(--brand-glow);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Layout ────────────────────────────────────── */
        .app-container { display: flex; min-height: 100vh; }

        /* ─── Sidebar (Glassmorphism) ───────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--brand-start);
            background-image: radial-gradient(at top left, rgba(255,255,255,0.05), transparent),
                              linear-gradient(to bottom, var(--brand-start), var(--brand-end));
            position: fixed;
            top: 16px; bottom: 16px; left: 16px;
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .sidebar-header {
            padding: 32px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-box {
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            color: var(--brand-accent);
            box-shadow: var(--shadow-glow);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .brand-name { color: white; font-weight: 700; font-size: 20px; letter-spacing: -0.02em; }
        .brand-role { color: var(--brand-accent); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; margin-top: -2px; }

        .sidebar-nav {
            flex: 1;
            padding: 0 16px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.4);
            margin: 24px 0 12px 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .nav-link.active {
            background: rgba(255,255,255,1);
            color: var(--brand-start);
            font-weight: 700;
            box-shadow: var(--shadow-md);
        }

        .sidebar-footer {
            padding: 24px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 14px;
            color: white;
        }

        .user-avatar {
            width: 36px; height: 36px;
            background: var(--brand-accent);
            color: var(--brand-start);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        /* ─── Main Content ──────────────────────────────── */
        .main-content {
            flex: 1;
            margin-left: calc(var(--sidebar-w) + 32px);
            padding: 32px 32px 32px 0;
            max-width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; }
        .page-title p { color: var(--text-muted); font-size: 14px; margin-top: 4px; }

        /* ─── Glass Components ─────────────────────────── */
        .glass-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .glass-card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 { font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        /* ─── Premium Tables ───────────────────────────── */
        .table-container { overflow-x: auto; }
        .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .premium-table th {
            padding: 12px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background: var(--bg-main);
            border-bottom: 1px solid var(--border);
        }
        .premium-table td {
            padding: 12px 20px;
            font-size: 13.5px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            transition: all 0.2s;
        }
        .premium-table tr:hover td { background: var(--bg-main); }

        /* ─── Badges & Buttons ─────────────────────────── */
        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-primary { background: var(--primary-light); color: var(--primary); }
        .badge-success { background: #ecfdf5; color: #059669; }
        .badge-danger { background: #fef2f2; color: #dc2626; }
        .badge-warning { background: #fffbeb; color: #d97706; }
        .badge-info { background: #eff6ff; color: #2563eb; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
        }
        .btn-primary { background: var(--brand-start); color: white; box-shadow: var(--shadow-sm); }
        .btn-primary:hover { background: var(--brand-end); transform: translateY(-1px); }
        
        .btn-outline { background: white; border: 1px solid var(--border); color: var(--text-main); }
        .btn-outline:hover { background: var(--bg-main); border-color: var(--text-muted); }

        .btn-xs { padding: 6px 10px; font-size: 12px; border-radius: 8px; }

        /* ─── Forms ────────────────────────────────────── */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; }
        .form-input {
            width: 100%;
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: var(--brand-start); box-shadow: 0 0 0 3px var(--brand-glow); }

        /* ─── Stats Cards ──────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon-box {
            width: 54px; height: 54px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
        }
        .stat-content .value { font-size: 24px; font-weight: 800; color: var(--text-main); line-height: 1; }
        .stat-content .label { font-size: 13px; color: var(--text-muted); font-weight: 500; margin-top: 4px; }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-110%); }
            .main-content { margin-left: 16px; padding-right: 16px; }
        }
        /* ─── Modals ───────────────────────────────────── */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            z-index: 1000; display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content {
            background: white; border-radius: 20px; width: 100%; max-width: 600px;
            box-shadow: var(--shadow-lg); border: 1px solid var(--border);
            transform: scale(0.95); transition: transform 0.3s ease;
            max-height: 90vh; display: flex; flex-direction: column;
        }
        .modal-overlay.show .modal-content { transform: scale(1); }
        .modal-header {
            padding: 24px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-header h3 { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0; }
        .modal-close {
            background: transparent; border: none; font-size: 20px; color: var(--text-muted);
            cursor: pointer; transition: 0.2s;
        }
        .modal-close:hover { color: var(--danger); }
        .modal-body { padding: 24px; overflow-y: auto; }
        .modal-footer {
            padding: 24px; border-top: 1px solid var(--border); background: var(--bg-main);
            border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;
            display: flex; justify-content: flex-end; gap: 12px;
        }
    </style>
    @stack('styles')
</head>
<body class="{{ $roleTheme['class'] }}">
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-box">
                    <i class="fas fa-hand-holding-medical"></i>
                </div>
                <div>
                    <div class="brand-name">DeisaHealth</div>
                    <div class="brand-role">{{ $roleTheme['label'] }}</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                @if($currentUser->isSuperAdmin())
                    <div class="nav-label">Master Data</div>
                    <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a href="{{ route('super-admin.users') }}" class="nav-link {{ request()->routeIs('super-admin.users*') ? 'active' : '' }}">
                        <i class="fas fa-users-gear"></i> Manajemen User
                    </a>
                @endif

                @if($currentUser->isAdmin() || $currentUser->isPetugas())
                    <div class="nav-label">Main Dashboard</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-house"></i> Overview
                    </a>

                    <div class="nav-label">Kependudukan</div>
                    <a href="{{ route('santri.index') }}" class="nav-link {{ request()->routeIs('santri.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> Data Santri
                    </a>
                    <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                        <i class="fas fa-school"></i> Data Kelas
                    </a>
                    <a href="{{ route('majors.index') }}" class="nav-link {{ request()->routeIs('majors.*') ? 'active' : '' }}">
                        <i class="fas fa-microscope"></i> Jurusan
                    </a>

                    <div class="nav-label">Operasional UKS</div>
                    <a href="{{ route('sickness-cases.index') }}" class="nav-link {{ request()->routeIs('sickness-cases.*') ? 'active' : '' }}">
                        <i class="fas fa-user-nurse"></i> Santri Sakit
                    </a>
                    <a href="{{ route('health-records.index') }}" class="nav-link {{ request()->routeIs('health-records.*') ? 'active' : '' }}">
                        <i class="fas fa-file-medical"></i> Rekam Medis
                    </a>
                    <a href="{{ route('referrals.index') }}" class="nav-link {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
                        <i class="fas fa-truck-medical"></i> Rujukan RS
                    </a>

                    <div class="nav-label">Inventori & Aset</div>
                    <a href="{{ route('medicines.index') }}" class="nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}">
                        <i class="fas fa-pills"></i> Stok Obat
                    </a>
                    <a href="{{ route('beds.index') }}" class="nav-link {{ request()->routeIs('beds.*') ? 'active' : '' }}">
                        <i class="fas fa-bed"></i> Kasur UKS
                    </a>

                    <div class="nav-label">Pelaporan</div>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Laporan Analitik
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="user-pill">
                    <div class="user-avatar">
                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                    </div>
                    <div style="flex:1; overflow:hidden;">
                        <div style="font-weight:700; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $currentUser->name }}</div>
                        <div style="font-size:11px; opacity:0.6; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $currentUser->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background:none; border:none; color:white; cursor:pointer; opacity:0.5; transition:0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">
                            <i class="fas fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <div class="page-title">
                    <p>DeisaHealth Management System</p>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="page-actions">
                    @yield('page-actions')
                </div>
            </header>

            @if(session('success'))
                <div style="background:#ecfdf5; color:#065f46; padding:16px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:12px; border:1px solid #d1fae5;">
                    <i class="fas fa-check-circle"></i>
                    <span style="font-weight:600; font-size:14px;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fef2f2; color:#991b1b; padding:16px; border-radius:12px; margin-bottom:24px; display:flex; align-items:center; gap:12px; border:1px solid #fee2e2;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span style="font-weight:600; font-size:14px;">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.style.display = 'flex';
                // Small timeout to allow display:flex to apply before adding opacity class
                setTimeout(() => modal.classList.add('show'), 10);
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.remove('show');
                setTimeout(() => modal.style.display = 'none', 300);
            }
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(m => closeModal(m.id));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
