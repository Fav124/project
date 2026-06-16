<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DEIHealth') | Aplikasi Kesehatan</title>
    
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('template-assets/css/style.css') }}">

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <link rel="shortcut icon" href="{{ asset('template-assets/images/favicon.png') }}" />
    
    <!-- NProgress for loading bar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* ═══════════════════════════════════════════════════════════════
           DEI HEALTH — Modern Clean UI
           Matching Android: #0090E7 · #12306F · #FF8C00 · #F8FAFC
        ═══════════════════════════════════════════════════════════════ */
        :root {
            --primary:        #0278D4;
            --primary-hover:  #0264B3;
            --primary-light:  #E8F4FD;
            --primary-navy:   #12306F;
            --primary-glow:   rgba(2, 120, 212, 0.12);
            --accent:         #FF8C00;
            --accent-light:   #FFF3E0;
            --success:        #10B981;
            --success-light:  #D1FAE5;
            --warning:        #F59E0B;
            --warning-light:  #FEF3C7;
            --danger:         #EF4444;
            --danger-light:   #FEE2E2;
            --bg:             #F1F5F9;
            --surface:        #FFFFFF;
            --border:         #E2E8F0;
            --border-light:   #F1F5F9;
            --text:           #0F172A;
            --text-secondary: #64748B;
            --text-muted:     #94A3B8;
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
            --shadow:         0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.03);
            --shadow-md:      0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.03);
            --shadow-lg:      0 20px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.03);
            --radius:         12px;
            --radius-lg:      16px;
            --radius-xl:      20px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── SIDEBAR ───────────────────────────────────────────── */
        .sidebar {
            background: var(--surface) !important;
            border-right: 1px solid var(--border) !important;
            box-shadow: var(--shadow-sm) !important;
        }

        .sidebar .sidebar-brand-wrapper {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-navy) 100%) !important;
            box-shadow: none !important;
            padding: 0 20px !important;
        }
        .sidebar .sidebar-brand,
        .navbar .navbar-brand {
            color: #fff !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            letter-spacing: -0.03em;
            font-size: 1.25rem !important;
        }
        .sidebar .sidebar-brand span { color: rgba(255,255,255,0.85) !important; }

        /* Profile section */
        .sidebar .nav .nav-item.profile {
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }
        .sidebar .profile-name h5 { color: var(--text) !important; font-weight: 600; }
        .sidebar .profile-name span { color: var(--text-secondary) !important; font-weight: 500; font-size: 0.75rem; }

        /* Nav Categories */
        .sidebar .nav .nav-category .nav-link {
            color: var(--text-muted) !important;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 12px 20px 6px !important;
        }

        /* Nav Items */
        .sidebar .nav .nav-item .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            font-size: 0.82rem;
            padding: 10px 16px !important;
            margin: 1px 10px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .sidebar .nav .nav-item .nav-link:hover {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
        }
        .sidebar .nav .nav-item.active > .nav-link,
        .sidebar .nav .nav-item .nav-link.active {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
            font-weight: 600;
        }
        .sidebar .nav .nav-item .nav-link .menu-icon {
            background: transparent !important;
            color: var(--text-muted) !important;
            width: 28px;
            height: 28px;
        }
        .sidebar .nav .nav-item .nav-link .menu-icon i {
            color: inherit !important;
            font-size: 18px;
        }
        .sidebar .nav .nav-item.active > .nav-link .menu-icon,
        .sidebar .nav .nav-item .nav-link:hover .menu-icon {
            color: var(--primary) !important;
        }

        /* Sub menu */
        .sidebar .nav.sub-menu .nav-item .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 400;
            font-size: 0.78rem;
            padding: 8px 12px 8px 48px !important;
            margin: 0 10px;
        }
        .sidebar .nav.sub-menu .nav-item .nav-link:hover,
        .sidebar .nav.sub-menu .nav-item .nav-link.active {
            color: var(--primary) !important;
            background: transparent !important;
            font-weight: 500;
        }
        
        .sidebar .nav.sub-menu {
            background: transparent !important;
        }
        .sidebar .nav-item.menu-items:hover {
            background: transparent !important;
        }

        /* ── NAVBAR ─────────────────────────────────────────────── */
        .navbar {
            background: var(--surface) !important;
            border-bottom: 1px solid var(--border) !important;
            box-shadow: var(--shadow-sm) !important;
            backdrop-filter: none !important;
        }
        .navbar .nav-link, .navbar .navbar-profile-name, .navbar i {
            color: var(--text) !important;
        }
        .navbar .form-control {
            background: var(--bg) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
            border-radius: 10px;
            font-size: 0.85rem;
        }

        /* ── MAIN PANEL ──────────────────────────────────────── */
        .main-panel, .content-wrapper {
            background: transparent !important;
        }
        .content-wrapper {
            background: var(--bg) !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 24px !important;
        }

        /* Print Header */
        .print-header {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            margin: 20px;
            box-shadow: var(--shadow-sm);
        }
        .print-header h2 {
            color: var(--primary-navy);
            margin-bottom: 4px;
            font-size: 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .print-header p {
            color: var(--text-secondary);
            margin-bottom: 2px;
            font-weight: 400;
            font-size: 0.85rem;
        }

        /* Page Header */
        .page-header { margin-bottom: 20px; }
        .page-title {
            color: var(--text) !important;
            font-family: 'Outfit', sans-serif;
            font-weight: 700 !important;
            font-size: 1.35rem !important;
            letter-spacing: -0.02em;
        }

        /* ── CARDS ─────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
            border-color: var(--border) !important;
        }
        .card .card-body { padding: 20px; }
        .card .card-title, .card h1, .card h2, .card h3, .card h4, .card h5, .card h6 {
            color: var(--text) !important;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }
        .card .text-muted, .card small { color: var(--text-secondary) !important; }

        /* Stat Cards — Minimal Style with Left Border */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.25s ease;
        }
        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            border-radius: 4px 0 0 4px;
        }
        .stat-card.stat-primary::before { background: var(--primary); }
        .stat-card.stat-success::before { background: var(--success); }
        .stat-card.stat-warning::before { background: var(--accent); }
        .stat-card.stat-danger::before  { background: var(--danger); }

        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .stat-card.stat-primary .stat-icon { background: var(--primary-light); color: var(--primary); }
        .stat-card.stat-success .stat-icon { background: var(--success-light); color: var(--success); }
        .stat-card.stat-warning .stat-icon { background: var(--accent-light); color: var(--accent); }
        .stat-card.stat-danger  .stat-icon { background: var(--danger-light); color: var(--danger); }

        .stat-card .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }
        .stat-card .stat-label {
            font-size: 0.78rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 2px;
        }
        .stat-card .stat-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* Gradient Cards for Dashboard (override) */
        .card-gradient-primary,
        .card-gradient-success,
        .card-gradient-info,
        .card-gradient-danger {
            border: none !important;
        }
        .card-gradient-primary { background: linear-gradient(135deg, #0278D4, #12306F) !important; }
        .card-gradient-success { background: linear-gradient(135deg, #10B981, #047857) !important; }
        .card-gradient-info    { background: linear-gradient(135deg, #0EA5E9, #0278D4) !important; }
        .card-gradient-danger  { background: linear-gradient(135deg, #F59E0B, #EA580C) !important; }
        .card-gradient-primary *, .card-gradient-success *, .card-gradient-info *, .card-gradient-danger * {
            color: #fff !important;
        }

        /* ── TABLE ──────────────────────────────────────────────── */
        .table { color: var(--text); }
        .table thead th {
            background: var(--bg);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border);
            border-top: none;
            padding: 12px 16px;
        }
        .table tbody tr { background: var(--surface) !important; }
        .table tbody tr:hover td { background: var(--primary-light) !important; }
        .table td {
            border-bottom: 1px solid var(--border-light);
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        /* ── FORMS ──────────────────────────────────────────────── */
        .form-control, .form-select {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            color: var(--text) !important;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-glow) !important;
        }
        .form-control::placeholder { color: var(--text-muted) !important; }
        label, .form-label {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.8rem;
            margin-bottom: 4px;
        }

        /* ── BUTTONS ────────────────────────────────────────────── */
        .btn {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 8px 18px;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
            text-transform: none;
        }
        .btn-primary {
            background: var(--primary) !important;
            border: none;
            color: #fff !important;
            box-shadow: 0 2px 8px rgba(2, 120, 212, 0.25);
        }
        .btn-primary:hover {
            background: var(--primary-hover) !important;
            box-shadow: 0 4px 12px rgba(2, 120, 212, 0.35);
            transform: translateY(-1px);
        }
        .btn-outline-primary { color: var(--primary) !important; border-color: var(--primary) !important; background: transparent; }
        .btn-outline-primary:hover { background: var(--primary) !important; color: #fff !important; }
        .btn-outline-secondary { color: var(--text-secondary) !important; border-color: var(--border) !important; background: transparent; }
        .btn-outline-danger { color: var(--danger) !important; border-color: var(--danger) !important; background: transparent; }
        .btn-outline-danger:hover { background: var(--danger) !important; color: #fff !important; }
        .btn-success { background: var(--success); border: none; color: #fff !important; }
        .btn-danger { background: var(--danger); border: none; color: #fff !important; }

        /* ── BADGES ──────────────────────────────────────────────── */
        .badge {
            font-weight: 600;
            font-size: 0.7rem;
            padding: 5px 10px;
            border-radius: 6px;
        }
        .badge-outline-warning { background: var(--warning-light); color: #92400E; border: none; }
        .badge-outline-info { background: var(--primary-light); color: var(--primary); border: none; }
        .badge-outline-success { background: var(--success-light); color: #065F46; border: none; }
        .badge-outline-danger { background: var(--danger-light); color: #991B1B; border: none; }
        .badge-outline-secondary { background: var(--bg); color: var(--text-secondary); border: none; }

        /* ── MODAL ───────────────────────────────────────────────── */
        .modal-content {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
        }
        .modal-header, .modal-header.bg-dark {
            background: var(--bg) !important;
            border-bottom: 1px solid var(--border);
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
            padding: 16px 24px;
        }
        .modal-title, .modal .text-white, .modal .close { color: var(--text) !important; opacity: 1; }
        .modal-footer {
            background: var(--bg) !important;
            border-top: 1px solid var(--border);
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        }
        .main-panel .bg-dark { background: var(--bg) !important; color: var(--text) !important; }

        /* ── TOM SELECT ──────────────────────────────────────────── */
        .ts-wrapper.single .ts-control {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 10px !important;
            padding: 10px 14px !important;
            color: var(--text) !important;
        }
        .ts-wrapper.single.focus .ts-control {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px var(--primary-glow) !important;
        }
        .ts-dropdown {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            margin-top: 4px !important;
            box-shadow: var(--shadow-md) !important;
        }
        .ts-dropdown .option { padding: 10px 14px !important; color: var(--text) !important; }
        .ts-dropdown .active { background: var(--primary) !important; color: #fff !important; }
        .ts-dropdown .option:hover { background: var(--primary-light) !important; color: var(--primary) !important; }
        .ts-control input { color: var(--text) !important; }

        /* ── ALERTS ──────────────────────────────────────────────── */
        .alert { border-radius: var(--radius); border: none; }
        .alert-success { background: var(--success-light); color: #065F46; }
        .alert-danger { background: var(--danger-light); color: #991B1B; }

        /* ── DROPDOWN ────────────────────────────────────────────── */
        .dropdown-menu {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
        }
        .dropdown-menu .dropdown-item, .dropdown-menu .preview-subject {
            color: var(--text) !important;
        }
        .dropdown-menu h6 { color: var(--text) !important; font-weight: 600; }
        .dropdown-item:hover, .dropdown-item:focus {
            background: var(--primary-light) !important;
            color: var(--primary) !important;
        }

        /* ── CHARTS ──────────────────────────────────────────────── */
        .apexcharts-text, .apexcharts-legend-text {
            fill: var(--text-secondary) !important;
            color: var(--text-secondary) !important;
        }
        .apexcharts-gridline { stroke: var(--border) !important; }

        /* ── PREVIEW LIST ────────────────────────────────────────── */
        .preview-list .preview-item { border-bottom: 1px solid var(--border-light); }
        .preview-subject { color: var(--text) !important; font-weight: 500; }
        .preview-list .text-small, .preview-list .text-muted { color: var(--text-secondary) !important; }

        /* ── BREADCRUMB ───────────────────────────────────────────── */
        .breadcrumb { background: transparent; }
        .breadcrumb-item a { color: var(--primary); }
        .breadcrumb-item.active { color: var(--text-secondary); }

        /* ── FORCE LIGHT TEXT INSIDE GRADIENTS ──────────────────── */
        .main-panel .text-white { color: var(--text) !important; }
        .main-panel .card-gradient-primary .text-white,
        .main-panel .card-gradient-success .text-white,
        .main-panel .card-gradient-info .text-white,
        .main-panel .card-gradient-danger .text-white { color: #fff !important; }

        /* ── NPROGRESS ───────────────────────────────────────────── */
        #nprogress .bar { background: var(--primary); }
        #nprogress .peg { box-shadow: 0 0 10px var(--primary), 0 0 5px var(--primary); }

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            .content-wrapper { padding: 16px !important; }
            .print-header { margin: 12px; padding: 16px; }
        }

        /* ── FOOTER ──────────────────────────────────────────────── */
        .footer {
            background: #f8f9fa !important;
            border-top: 1px solid var(--border) !important;
            padding: 20px 24px !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-scroller">
        <!-- partial:partials/_sidebar.blade.php -->
        @include('layouts.partials.sidebar')
        
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_navbar.blade.php -->
            @include('layouts.partials.navbar')
            
            <!-- partial -->
            <div class="main-panel">
                <div class="print-header" style="margin: 20px;">
                    <h2>DEI HEALTH MANAGEMENT SYSTEM</h2>
                    <p>Layanan Kesehatan Terpadu Santri Pondok Pesantren Ma'had Dar El-Ilmi Sumatera Barat</p>
                    <p>Unit Kesehatan Pondok | Sistem Informasi Resmi</p>
                </div>
                <div class="content-wrapper">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="page-header">
                        <h3 class="page-title"> @yield('page-title', 'Dashboard') </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    </div>

                    @yield('content')
                </div>
                <!-- content-wrapper ends -->
                
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © DEIHealth {{ date('Y') }}</span>
                   </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    
    <!-- plugins:js -->
    <script src="{{ asset('template-assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    
    <!-- NProgress & SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Plugin js for this page -->
    @stack('plugin-scripts')
    <!-- End plugin js for this page -->
    
    <!-- inject:js -->
    <script src="{{ asset('template-assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('template-assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('template-assets/js/misc.js') }}"></script>
    <script src="{{ asset('template-assets/js/settings.js') }}"></script>
    <script src="{{ asset('template-assets/js/todolist.js') }}"></script>
    <!-- endinject -->

    <script>
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $(document).ajaxStart(function() { NProgress.start(); });
            $(document).ajaxStop(function() { NProgress.done(); });

            window.initTomSelect = function(selector = 'select:not(.no-select)') {
                document.querySelectorAll(selector).forEach((el) => {
                    if (!el.tomselect) {
                        new TomSelect(el, { create: false, sortField: { field: "text", direction: "asc" }, allowEmptyOption: true, maxOptions: null });
                    }
                });
            };
            initTomSelect();

            $(document).on('submit', 'form[data-ajax="true"]', function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.attr('method');
                const formData = new FormData(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

                $.ajax({
                    url: url, type: method, data: formData, processData: false, contentType: false,
                    success: function(response) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Data berhasil disimpan.',
                            background: '#fff', color: '#0F172A', confirmButtonColor: '#0278D4'
                        }).then(() => { $('.modal').modal('hide'); location.reload(); });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let errorMsg = 'Terjadi kesalahan.';
                        if (errors) errorMsg = Object.values(errors).flat().join('<br>');
                        else if (xhr.responseJSON?.message) errorMsg = xhr.responseJSON.message;
                        Swal.fire({ icon: 'error', title: 'Gagal!', html: errorMsg, background: '#fff', color: '#0F172A', confirmButtonColor: '#EF4444' });
                    },
                    complete: function() { submitBtn.prop('disabled', false).html(originalBtnText); }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
