@extends('layouts.app')

@section('title', 'Manajemen Akses')
@section('page-title', 'Otoritas Pengguna Sistem')

@section('content')

{{-- Filter & Search --}}
<x-ui.card>
    <form method="GET" action="{{ route('super-admin.users') }}">
        <x-ui.filter-bar>
            <x-form.input name="search" placeholder="Cari nama atau email..." :value="request('search')" style="flex:2;" />
            <x-form.select name="status" style="flex:1;">
                <option value="">Semua Status</option>
                <option value="pending"  @selected(request('status') === 'pending')>Menunggu Approval</option>
                <option value="approved" @selected(request('status') === 'approved')>Sudah Aktif</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
            </x-form.select>
            <x-form.select name="role" style="flex:1;">
                <option value="">Semua Jabatan</option>
                <option value="petugas_kesehatan" @selected(request('role') === 'petugas_kesehatan')>Petugas Kesehatan</option>
                <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
            </x-form.select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('super-admin.users') }}" class="btn btn-outline">Reset</a>
        </x-ui.filter-bar>
    </form>
</x-ui.card>

<x-ui.card class="mt-4">
    <x-slot name="header">
        <h2><i class="fas fa-users-gear"></i> Direktori Pengguna</h2>
        <span class="badge badge-outline">{{ $users->total() }} Total Entri</span>
    </x-slot>

    <x-ui.table>
        <thead>
            <tr>
                <th>Identitas Pengguna</th>
                <th>Jabatan / Role</th>
                <th>Status Akses</th>
                <th>Bergabung</th>
                <th style="text-align:right;">Opsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="user-avatar" style="width:36px; height:36px; font-size:14px; background:var(--bg-main); color:var(--primary); border:1px solid var(--border);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700; color:var(--text-main);">{{ $user->name }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $roleTheme = match($user->role) {
                            'admin' => ['class' => 'badge-primary', 'icon' => 'fa-user-tie', 'label' => 'Administrator'],
                            'petugas_kesehatan' => ['class' => 'badge-info', 'icon' => 'fa-stethoscope', 'label' => 'Nakes'],
                            default => ['class' => 'badge-outline', 'icon' => 'fa-user', 'label' => $user->role],
                        };
                    @endphp
                    <span class="badge {{ $roleTheme['class'] }}">
                        <i class="fas {{ $roleTheme['icon'] }}"></i> {{ $roleTheme['label'] }}
                    </span>
                </td>
                <td>
                    @php
                        $statusTheme = match($user->status) {
                            'approved' => ['class' => 'badge-success', 'label' => 'Aktif'],
                            'rejected' => ['class' => 'badge-danger', 'label' => 'Ditolak'],
                            default => ['class' => 'badge-warning', 'label' => 'Pending'],
                        };
                    @endphp
                    <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
                </td>
                <td style="font-size:13px; color:var(--text-muted);">{{ $user->created_at->format('d M Y') }}</td>
                <td style="text-align:right;">
                    <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-xs btn-outline">
                        <i class="fas fa-arrow-right"></i> Kelola
                    </a>
                </td>
            </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Tidak ada pengguna yang sesuai kriteria." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $users->links() }}
    </x-slot>
</x-ui.card>
@endsection
