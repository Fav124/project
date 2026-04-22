@extends('layouts.app')

@section('title', 'Manajemen Akses')
@section('page-title', 'Otoritas Pengguna Sistem')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <x-ui.card title="Daftar Pengguna">
            <x-slot name="header">
                <h4 class="card-title">Daftar Pengguna Sistem</h4>
            </x-slot>

            <form method="GET" action="{{ route('super-admin.users.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="search" class="form-control text-white" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="status" class="form-select text-white">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Menunggu Approval</option>
                            <option value="approved" @selected(request('status') === 'approved')>Sudah Aktif</option>
                            <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="role" class="form-select text-white">
                            <option value="">Semua Jabatan</option>
                            <option value="petugas_kesehatan" @selected(request('role') === 'petugas_kesehatan')>Petugas Kesehatan</option>
                            <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <x-ui.table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-white">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleTheme = match($user->role) {
                                        'admin' => ['class' => 'badge-outline-primary', 'label' => 'Administrator'],
                                        'petugas_kesehatan' => ['class' => 'badge-outline-info', 'label' => 'Nakes'],
                                        default => ['class' => 'badge-outline-secondary', 'label' => $user->role],
                                    };
                                @endphp
                                <span class="badge {{ $roleTheme['class'] }}">{{ $roleTheme['label'] }}</span>
                            </td>
                            <td>
                                @php
                                    $statusTheme = match($user->status) {
                                        'approved' => ['class' => 'badge-outline-success', 'label' => 'Aktif'],
                                        'rejected' => ['class' => 'badge-outline-danger', 'label' => 'Ditolak'],
                                        default => ['class' => 'badge-outline-warning', 'label' => 'Pending'],
                                    };
                                @endphp
                                <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="mdi mdi-account-cog"></i> Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada pengguna yang ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <x-slot name="footer">
                {{ $users->links() }}
            </x-slot>
        </x-ui.card>
    </div>
</div>
@endsection
