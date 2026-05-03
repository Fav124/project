@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Profil')
@section('page_description', 'Kelola informasi personal dan keamanan akun Anda.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Profil</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                    <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="User Avatar">
                </div>
                <h5 class="fw-bold">{{ $user->name }}</h5>
                <p class="text-muted">{{ Str::headline($user->role->value) }}</p>
                <hr>
                <p class="small text-muted">Terdaftar sejak: {{ $user->created_at->format('d F Y') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Informasi Akun</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3">Ganti Password (Kosongkan jika tidak ingin mengubah)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
