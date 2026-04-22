<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DeisaHealth') | Aplikasi Kesehatan</title>
    
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template-assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    
    <!-- Plugin css for this page -->
    @stack('plugin-styles')
    <!-- End plugin css for this page -->
    
    <!-- inject:css -->
    <!-- endinject -->
    
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
        /* Custom adjustment for DeisaHealth branding */
        .sidebar .sidebar-brand-wrapper .sidebar-brand.brand-logo {
            color: #fff;
            font-weight: 800;
            text-decoration: none;
            font-size: 1.5rem;
        }
        .sidebar .sidebar-brand-wrapper .sidebar-brand.brand-logo span {
            color: #00d25b;
        }

        /* BETTER CONTRAST & FOCUS */
        body {
            background-color: #0b0c10;
        }
        .card {
            background-color: #191c24;
            border: 1px solid #2c2e33;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }
        .card:hover {
            border-color: #00d25b;
        }
        .table {
            color: #e4e4e4;
        }
        .table thead th {
            background: #2c2e33;
            color: #00d25b;
            font-weight: 700;
            border-top: none;
        }
        .modal-content {
            background-color: #191c24;
            border: 1px solid #00d25b;
            box-shadow: 0 0 30px rgba(0, 210, 91, 0.2);
        }
        .modal-header {
            border-bottom: 1px solid #2c2e33;
            background: #111318;
        }
        .modal-footer {
            border-top: 1px solid #2c2e33;
            background: #111318;
        }
        .form-control, .form-select {
            background-color: #2a3038 !important;
            border: 1px solid #2c2e33 !important;
            color: #ffffff !important;
        }
        .form-control:focus {
            border-color: #00d25b !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 210, 91, 0.25);
        }
        .btn-primary {
            background-color: #00d25b;
            border-color: #00d25b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background-color: #00ad4b;
            border-color: #00ad4b;
            transform: translateY(-1px);
            box-shadow: 0 5px 10px rgba(0,210,91,0.3);
        }
        
        /* Progress Bar Customization */
        #nprogress .bar {
            background: #00d25b !important;
            height: 3px !important;
        }
        #nprogress .spinner-icon {
            border-top-color: #00d25b !important;
            border-left-color: #00d25b !important;
        }

        /* Focus points */
        .text-focus {
            color: #00d25b !important;
            font-weight: bold;
        }
        .badge-outline-success {
            border: 1px solid #00d25b;
            color: #00d25b;
        }

        /* SIDEBAR ENHANCEMENT */
        .sidebar .nav .nav-item.active > .nav-link {
            background: rgba(0, 210, 91, 0.1) !important;
            border-radius: 0 50px 50px 0;
            color: #00d25b !important;
        }
        .sidebar .nav .nav-item.active > .nav-link .menu-icon i {
            color: #00d25b !important;
        }
        .sidebar .nav .nav-item .nav-link:hover {
            color: #00d25b !important;
        }
        .sidebar .nav.sub-menu .nav-item .nav-link.active {
            color: #00d25b !important;
            font-weight: 700;
        }
        .sidebar .nav.sub-menu .nav-item .nav-link:before {
            background: #00d25b !important;
        }

        /* PRINT STYLES - A4 FORMAL */
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            body {
                background: white !important;
                color: black !important;
                font-family: 'Times New Roman', serif !important;
            }
            .sidebar, .navbar, .footer, .btn, .modal-header .close, .nav-tabs, .card-title .btn {
                display: none !important;
            }
            .content-wrapper {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                background: white !important;
            }
            .card-body {
                padding: 0 !important;
            }
            .text-white, .text-muted, .text-primary, .text-success, .text-warning, .text-danger {
                color: black !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table th, .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                color: black !important;
            }
            .badge {
                border: 1px solid #000 !important;
                color: black !important;
                background: transparent !important;
            }
            
            /* Institutional Header (Kop Surat) */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px double #000;
                padding-bottom: 10px;
            }
            .print-header h2 { margin: 0; font-size: 24px; font-weight: bold; }
            .print-header p { margin: 0; font-size: 14px; }
            
            .main-panel { width: 100% !important; }
            .page-body-wrapper { padding-top: 0 !important; }
        }
        
        .print-header { display: none; }

        /* Tom Select Dark Mode Adjustment */
        .ts-control {
            background-color: #2a3038 !important;
            color: #ffffff !important;
            border: 1px solid #2c2e33 !important;
            padding: 10px 15px !important;
        }
        .ts-dropdown {
            background-color: #191c24 !important;
            color: #ffffff !important;
            border: 1px solid #2c2e33 !important;
        }
        .ts-dropdown .active {
            background-color: #00d25b !important;
            color: #000000 !important;
        }
        .ts-dropdown .option {
            color: #ffffff !important;
        }
        .ts-control input {
            color: #ffffff !important;
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
                    <h2>DEISA HEALTH MANAGEMENT SYSTEM</h2>
                    <p>Layanan Kesehatan Terpadu Santri Pondok Pesantren Deisa</p>
                    <p>Jl. Kesehatan No. 123, Kota Pendidikan | Telp: (021) 555-0123</p>
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
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © DeisaHealth {{ date('Y') }}</span>
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
                            background: '#191c24',
                            color: '#fff',
                            confirmButtonColor: '#00d25b'
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
                            background: '#191c24',
                            color: '#fff',
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
