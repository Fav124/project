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
    <!-- End layout styles -->

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <link rel="shortcut icon" href="{{ asset('template-assets/images/favicon.png') }}" />
    
    <!-- NProgress for loading bar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary: #4f7df0;
            --primary-glow: rgba(79, 125, 240, 0.2);
            --secondary: #64748b;
            --dark: #cfd8e3;
            --card-bg: #ffffff;
            --glass-bg: rgba(244, 247, 251, 0.9);
            --border: rgba(71, 85, 105, 0.18);
            --text: #1f2937;
            --text-muted: #475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 125, 240, 0.08) 0px, transparent 55%),
                radial-gradient(at 100% 100%, rgba(148, 163, 184, 0.14) 0px, transparent 55%);
            color: var(--text);
            min-height: 100vh;
        }

        .main-panel,
        .content-wrapper {
            background: transparent !important;
        }

        h1, h2, h3, h4, h5, h6, .page-title, .menu-title {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        /* GLASSMORPHISM CARD */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            transition: all 0.25s ease;
        }
        .card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(79, 125, 240, 0.16);
        }

        .sidebar {
            background: #e8eef6 !important;
            backdrop-filter: blur(12px);
            border-right: 1px solid var(--border);
        }

        .navbar {
            background: rgba(244, 247, 251, 0.96) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }
        .navbar .nav-link,
        .navbar .navbar-profile-name,
        .navbar i {
            color: #1f2937 !important;
        }
        .navbar .form-control {
            background: #f5f8fc !important;
            color: #1f2937 !important;
        }

        .sidebar .nav .nav-item .nav-link,
        .sidebar .nav.sub-menu .nav-item .nav-link {
            color: #1f2937 !important;
        }
        .sidebar .nav .nav-item .nav-link:hover,
        .sidebar .nav .nav-item.active > .nav-link {
            background: rgba(79, 125, 240, 0.26) !important;
            color: #0f172a !important;
            border-radius: 10px;
        }
        .sidebar .nav .nav-category .nav-link,
        .sidebar .profile-name span {
            color: #64748b !important;
        }
        .sidebar .profile-name h5 {
            color: #0f172a !important;
        }

        .table {
            color: #1f2937;
        }
        .table tbody tr {
            background: #ffffff !important;
        }
        .table tbody tr:nth-child(even) {
            background: #f8fbff !important;
        }
        .table thead th {
            background: #e8eef6;
            color: #334155;
            font-weight: 700;
            border-top: none;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1.5px;
            padding: 15px;
        }
        .table td {
            border-bottom: 1px solid var(--border);
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody tr:hover td,
        .table tbody tr:hover th {
            background: rgba(79, 125, 240, 0.16) !important;
            color: #0f172a !important;
        }
        .table .btn:hover i {
            color: inherit !important;
        }

        .content-wrapper {
            background: rgba(236, 241, 247, 0.92);
            border-radius: 16px;
            border: 1px solid rgba(71, 85, 105, 0.15);
            padding: 22px;
        }

        .page-title {
            color: #1e293b !important;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .modal-content {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(71, 85, 105, 0.2);
            border-radius: 18px;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
        }
        .modal-header,
        .modal-header.bg-dark {
            background: #eef3fa !important;
            border-bottom: 1px solid rgba(71, 85, 105, 0.18);
            color: #0f172a !important;
        }
        .modal-footer {
            background: #f8fbff !important;
            border-top: 1px solid rgba(71, 85, 105, 0.15);
        }
        .modal-title,
        .modal .text-white,
        .modal .close,
        .modal .close.text-white {
            color: #0f172a !important;
            opacity: 1;
        }
        .main-panel .bg-dark {
            background: #eef3fa !important;
            color: #1f2937 !important;
        }
        .main-panel .table.table-sm.text-white,
        .main-panel .table.text-white {
            color: #1f2937 !important;
        }

        .form-control, .form-select {
            background-color: #ffffff !important;
            border: 1px solid rgba(100, 116, 139, 0.45) !important;
            color: #0f172a !important;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background-color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .form-control::placeholder {
            color: #64748b !important;
            opacity: 0.85;
        }

        .btn {
            border-radius: 12px;
            font-weight: 700;
            padding: 10px 24px;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #3f72d7);
            border: none;
            box-shadow: 0 4px 15px rgba(79, 140, 255, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 140, 255, 0.35);
        }
        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-info,
        .btn-outline-warning,
        .btn-outline-danger {
            color: #1f2937;
            border-width: 1px;
        }
        .btn-outline-primary:hover,
        .btn-outline-secondary:hover,
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-danger:hover {
            color: #ffffff !important;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: rgba(79, 125, 240, 0.16) !important;
            color: #0f172a !important;
        }

        /* Tom Select Premium Dark Mode */
        .ts-wrapper.single .ts-control {
            background-color: #ffffff !important;
            border: 1px solid rgba(100, 116, 139, 0.45) !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            color: #0f172a !important;
            transition: all 0.3s;
        }
        .ts-wrapper.single.focus .ts-control {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px var(--primary-glow) !important;
        }
        .ts-dropdown {
            background: #ffffff !important;
            backdrop-filter: blur(20px);
            border: 1px solid var(--primary) !important;
            border-radius: 16px !important;
            margin-top: 8px !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15) !important;
            overflow: hidden;
        }
        .ts-dropdown .option {
            padding: 12px 16px !important;
            transition: all 0.2s;
        }
        .ts-dropdown .active {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        .ts-dropdown .create:hover, .ts-dropdown .option:hover {
            background-color: rgba(79, 125, 240, 0.12) !important;
            color: #1e3a8a !important;
        }
        .ts-control input {
            color: #0f172a !important;
        }
        
        /* Force readable text in light content area */
        .main-panel .text-white,
        .main-panel .card-title,
        .main-panel .preview-subject,
        .main-panel h1,
        .main-panel h2,
        .main-panel h3,
        .main-panel h4,
        .main-panel h5,
        .main-panel h6 {
            color: #0f172a !important;
        }
        .main-panel .text-muted,
        .main-panel small,
        .main-panel .page-title + * {
            color: #5b6472 !important;
        }
        .main-panel .badge {
            font-weight: 700;
        }
        .print-header {
            background: linear-gradient(135deg, #f8fbff, #e8eef7);
            border: 1px solid rgba(71, 85, 105, 0.18);
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 18px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
        }
        .print-header h2 {
            color: #1e293b;
            margin-bottom: 6px;
            font-size: 1.9rem;
            letter-spacing: 0.01em;
        }
        .print-header p {
            color: #475569;
            margin-bottom: 4px;
            font-weight: 500;
        }
        /* Search input inside dropdown */
        .ts-dropdown-content {
            padding: 5px;
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
                <div class="print-header">
                    <h2>DEI HEALTH MANAGEMENT SYSTEM</h2>
                    <p>Layanan Kesehatan Terpadu Santri Pondok Pesantren Ma'had Dar El-Ilmi Sumatera Barat</p>
                    <p>Unit Kesehatan Pondok | Sistem Informasi Resmi</p>
                </div>
                <div class="content-wrapper">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="page-header">
                        <h3 class="page-title text-focus"> @yield('page-title', 'Dashboard') </h3>
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
            // Global AJAX Settings
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Start NProgress on AJAX start
            $(document).ajaxStart(function() {
                NProgress.start();
            });

            // Done NProgress on AJAX stop
            $(document).ajaxStop(function() {
                NProgress.done();
            });

            // Global Tom Select Initialization
            window.initTomSelect = function(selector = 'select:not(.no-select)') {
                document.querySelectorAll(selector).forEach((el) => {
                    if (!el.tomselect) {
                        new TomSelect(el, {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            allowEmptyOption: true,
                            maxOptions: null
                        });
                    }
                });
            };

            initTomSelect();

            // Global Form AJAX Handler
            $(document).on('submit', 'form[data-ajax="true"]', function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const method = form.attr('method');
                const formData = new FormData(this);

                // Disable submit button
                const submitBtn = form.find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Memproses...');

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Data berhasil disimpan.',
                            background: '#f4f7fb',
                            color: '#1f2937',
                            confirmButtonColor: '#4f7df0'
                        }).then(() => {
                            // Close modal if exists
                            $('.modal').modal('hide');
                            // Reload page or update UI
                            location.reload(); 
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = 'Terjadi kesalahan.';
                        if (errors) {
                            errorMsg = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMsg,
                            background: '#f4f7fb',
                            color: '#1f2937',
                            confirmButtonColor: '#ff6258'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
        });
    </script>
    
    <!-- Custom js for this page -->
    @stack('scripts')
    <!-- End custom js for this page -->
</body>
</html>
