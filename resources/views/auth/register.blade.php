<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DEI Health</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo mb-5">
                        <a href="/"><img src="{{ asset('assets/images/logo/dei.png') }}" alt="Logo" style="height: 50px;"> <span class="h3 fw-bold text-primary align-middle ms-2">DEI HEALTH</span></a>
                    </div>
                    <h1 class="auth-title">Daftar Akun</h1>
                    <p class="auth-subtitle mb-5">Daftarkan diri Anda untuk mengakses sistem kesehatan pondok.</p>

                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="name" class="form-control form-control-xl @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" name="email" class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="no_hp" class="form-control form-control-xl @error('no_hp') is-invalid @enderror" placeholder="Nomor WhatsApp (Contoh: 0812...)" value="{{ old('no_hp') }}" required>
                            <div class="form-control-icon">
                                <i class="bi bi-whatsapp"></i>
                            </div>
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" class="form-control form-control-xl @error('password') is-invalid @enderror" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password_confirmation" class="form-control form-control-xl" placeholder="Konfirmasi Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="agree_policy" id="agree_policy" required>
                            <label class="form-check-label text-gray-600" for="agree_policy">
                                Saya menyetujui <a href="{{ route('policy') }}" target="_blank" class="font-bold">Kebijakan & Aturan</a> penggunaan sistem.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-3">Daftar Sekarang</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class='text-gray-600'>Sudah punya akun? <a href="{{ route('login') }}" class="font-bold">Login</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" style="background: linear-gradient(45deg, #435ebe, #1c2d6a);">
                    <div class="h-100 d-flex flex-column justify-content-center align-items-center text-white p-5">
                        <i class="bi bi-heart-pulse fs-1 mb-4" style="font-size: 10rem !important;"></i>
                        <h2 class="text-white fw-bold display-4">DEI Health</h2>
                        <p class="fs-4 text-center">Sistem Informasi Kesehatan Terpadu untuk Pondok Pesantren Modern.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Pendaftaran Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonText: 'Hubungi Admin via WA',
            showCancelButton: true,
            cancelButtonText: 'Nanti Saja'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open("{{ session('waLink') }}", "_blank");
            }
        });
    </script>
    @endif
</body>
</html>
