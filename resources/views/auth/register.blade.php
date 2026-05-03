@extends('layouts.auth')

@section('title', 'Daftar Akun')

@push('styles')
<style>
    #auth #auth-right {
        background: linear-gradient(135deg, rgba(45, 73, 157, 0.4), rgba(63, 84, 145, 0.4)), url('{{ asset('assets/images/bg/pondok_health.png') }}');
        background-size: cover;
        background-position: center;
    }
    
    #auth #auth-left {
        padding: 2rem 5rem;
        height: 100vh;
        overflow-y: auto; /* Allow scroll for registration form */
    }

    /* Custom scrollbar for left side if needed */
    #auth #auth-left::-webkit-scrollbar {
        width: 5px;
    }
    #auth #auth-left::-webkit-scrollbar-thumb {
        background: #eee;
    }
</style>
@endpush

@section('content')
<div class="row h-100 m-0">
    <div class="col-lg-5 col-12 p-0">
        <div id="auth-left">
            <div class="auth-logo mb-4">
                <a href="{{ url('/') }}" class="d-flex align-items-center">
                    <img src="{{ asset('assets/images/logo/dei.png') }}" alt="Logo" style="height: 60px; width: auto;">
                    <h4 class="text-primary fw-bold mb-0 ms-3">DEI HEALTH</h4>
                </a>
            </div>
            <h1 class="auth-title" style="font-size: 1.8rem;">Daftar Akun.</h1>
            <p class="auth-subtitle mb-3">Lengkapi formulir di bawah untuk mendaftarkan akun petugas kesehatan baru.</p>

            @if($errors->any())
                <div class="alert alert-danger py-1 px-3 shadow-sm mb-2" style="font-size: 0.8rem;">
                    <ul class="mb-0 p-0" style="list-style: none;">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="form-group position-relative has-icon-left mb-2">
                    <input type="text" name="name" class="form-control shadow-sm" placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                
                <div class="form-group position-relative has-icon-left mb-2">
                    <input type="email" name="email" class="form-control shadow-sm" placeholder="Alamat Email" value="{{ old('email') }}" required style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>

                <div class="form-group position-relative has-icon-left mb-2">
                    <input type="text" name="no_hp" class="form-control shadow-sm" placeholder="Nomor WhatsApp (Contoh: 0812...)" value="{{ old('no_hp') }}" required style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                </div>

                <div class="form-group position-relative has-icon-left mb-2">
                    <input type="password" name="password" class="form-control shadow-sm" placeholder="Password" required style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>

                <div class="form-group position-relative has-icon-left mb-3">
                    <input type="password" name="password_confirmation" class="form-control shadow-sm" placeholder="Konfirmasi Password" required style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>

                <div class="form-check form-check-md d-flex align-items-center mb-3">
                    <input class="form-check-input me-2" type="checkbox" name="agree_policy" id="agree_policy" required>
                    <label class="form-check-label text-gray-600" for="agree_policy" style="font-size: 0.8rem;">
                        Saya menyetujui <a href="{{ route('policy') }}" target="_blank" class="font-bold text-primary">Kebijakan & Aturan</a> penggunaan sistem.
                    </label>
                </div>

                <button class="btn btn-primary btn-block btn-lg shadow-lg py-2 fw-bold border-0" style="background: linear-gradient(90deg, #2d499d, #3f5491); transition: all 0.3s;">Daftar Sekarang</button>
            </form>
            
            <div class="text-center mt-3">
                <p class="text-gray-600" style="font-size: 0.85rem;">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold">Masuk Sekarang</a>.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-7 d-none d-lg-block p-0">
        <div id="auth-right">
            <div class="auth-right-logo">
                <img src="{{ asset('assets/images/logo/dei.png') }}" alt="Auth Logo" class="img-fluid" style="max-width: 250px;">
                <h2 class="text-center mt-3 fw-bold text-primary" style="letter-spacing: 2px;">DEI HEALTH</h2>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Pendaftaran Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonText: 'Hubungi Admin via WA',
        showCancelButton: true,
        cancelButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open("{{ session('waLink') }}", "_blank");
        }
    });
</script>
@endif
@endpush
