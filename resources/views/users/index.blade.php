@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Daftar Pengguna Sistem')
@section('page_description', 'Kelola hak akses, role, dan status aktifasi pengguna DEI Health.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Data Pengguna</h4>
        <form action="{{ route('users.index') }}" method="GET" class="d-flex gap-2">
            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Role</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas_kesehatan" {{ request('role') == 'petugas_kesehatan' ? 'selected' : '' }}>Petugas Kesehatan</option>
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama/email..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary">Cari</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama & Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tgl Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <img src="{{ asset('assets/images/faces/1.jpg') }}">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('users.update-role', $user->id) }}" method="POST">
                                @csrf
                                <select name="role" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="super_admin" {{ $user->role->value == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="admin" {{ $user->role->value == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="petugas_kesehatan" {{ $user->role->value == 'petugas_kesehatan' ? 'selected' : '' }}>Petugas Kesehatan</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('users.change-status', $user->id) }}" method="POST">
                                @csrf
                                @php
                                    $statusColor = match($user->status) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'frozen' => 'info',
                                        'blocked' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <select name="status" class="form-select form-select-sm border-0 bg-light-{{ $statusColor }} text-{{ $statusColor }} fw-bold" 
                                        onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="frozen" {{ $user->status == 'frozen' ? 'selected' : '' }}>Dibekukan</option>
                                    <option value="blocked" {{ $user->status == 'blocked' ? 'selected' : '' }}>Diblokir</option>
                                </select>
                            </form>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $user->is_approved ? 'outline-warning' : 'outline-success' }} me-1" 
                                            title="{{ $user->is_approved ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Hapus pengguna ini permanen?')"
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
