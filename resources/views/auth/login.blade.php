@extends('layouts.auth')

@section('title', 'Login')

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
            <h1 class="auth-title">Log in.</h1>
            <p class="auth-subtitle">Silakan masuk menggunakan akun yang telah terdaftar.</p>

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 shadow-sm mb-3" style="font-size: 0.9rem;">
                    <ul class="mb-0 p-0" style="list-style: none;">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group position-relative has-icon-left mb-3">
                    <input type="email" name="email" class="form-control form-control-lg shadow-sm" placeholder="Email" value="{{ old('email') }}" required autofocus style="padding-top: 1rem; padding-bottom: 1rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-3">
                    <input type="password" name="password" class="form-control form-control-lg shadow-sm" placeholder="Password" required style="padding-top: 1rem; padding-bottom: 1rem;">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <div class="form-check form-check-md d-flex align-items-center mb-4">
                    <input class="form-check-input me-2" type="checkbox" name="remember" id="flexCheckDefault">
                    <label class="form-check-label text-gray-600" for="flexCheckDefault" style="font-size: 0.9rem;">
                        Tetap masuk
                    </label>
                </div>
                <button class="btn btn-primary btn-block btn-lg shadow-lg py-3 fw-bold border-0" style="background: linear-gradient(90deg, #2d499d, #3f5491); transition: all 0.3s;">Masuk Ke Sistem</button>
            </form>
            <div class="text-center mt-4 text-md">
                <p class="text-gray-600 mb-1" style="font-size: 0.9rem;">Belum punya akun? <a href="{{ route('register') }}" class="font-bold">Daftar Sekarang</a>.</p>
                <p style="font-size: 0.9rem;"><a class="font-bold" href="#">Lupa password?</a></p>
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


