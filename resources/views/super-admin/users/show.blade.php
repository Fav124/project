@extends('layouts.app')

@section('title', 'Kelola Pengguna - ' . $user->name)
@section('page-title', 'Otoritas & Detail Pengguna')

@section('content')
<div class="row">
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="text-center pb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 40px; font-weight: 800;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3 class="text-white mb-1">{{ $user->name }}</h3>
                    <p class="text-muted">{{ $user->email }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @php
                            $roleTheme = match($user->role) {
                                'admin' => ['class' => 'badge-outline-primary', 'label' => 'Administrator'],
                                'petugas_kesehatan' => ['class' => 'badge-outline-info', 'label' => 'Nakes'],
                                default => ['class' => 'badge-outline-secondary', 'label' => $user->role],
                            };
                            $statusTheme = match($user->status) {
                                'approved' => ['class' => 'badge-outline-success', 'label' => 'Aktif'],
                                'rejected' => ['class' => 'badge-outline-danger', 'label' => 'Ditolak'],
                                default => ['class' => 'badge-outline-warning', 'label' => 'Pending'],
                            };
                        @endphp
                        <span class="badge {{ $roleTheme['class'] }}">{{ $roleTheme['label'] }}</span>
                        <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
                    </div>
                </div>

                <div class="border-top border-secondary py-4">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Terdaftar Pada</small>
                            <span class="text-white">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Status Akses</small>
                            <span class="text-white">{{ ucfirst($user->status) }}</span>
                        </div>
                        @if($user->approved_at)
                            <div class="col-12 mb-3">
                                <small class="text-muted d-block">{{ $user->status === 'approved' ? 'Disetujui Pada' : 'Ditolak Pada' }}</small>
                                <span class="text-white">{{ $user->approved_at->translatedFormat('d F Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($user->rejection_reason)
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Alasan Penolakan:</h6>
                        <p class="mb-0">{{ $user->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7 grid-margin stretch-card">
        <div class="row">
            @if($user->isPending())
                <div class="col-12 grid-margin stretch-card">
                    <div class="card border border-warning">
                        <div class="card-body">
                            <h4 class="card-title text-warning"><i class="mdi mdi-gavel mr-2"></i> Keputusan Approval</h4>
                            <p class="text-muted">Tentukan akses sistem untuk pengguna ini.</p>
                            <div class="template-demo d-flex gap-2">
                                <form action="{{ route('super-admin.users.approve', $user) }}" method="POST" class="flex-grow-1" data-ajax="true">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="mdi mdi-check-circle"></i> Setujui & Beri Akses
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-lg" data-toggle="collapse" data-target="#rejectForm">
                                    <i class="mdi mdi-close-circle"></i> Tolak
                                </button>
                            </div>
                            <div class="collapse mt-3" id="rejectForm">
                                <form action="{{ route('super-admin.users.reject', $user) }}" method="POST" data-ajax="true">
                                    @csrf
                                    <div class="form-group">
                                        <label>Alasan Penolakan (Akan dikirim via email)</label>
                                        <textarea name="rejection_reason" class="form-control text-white" rows="3" placeholder="Contoh: Akun tidak valid atau identitas tidak dikenal..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Penolakan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12 grid-margin stretch-card">
                <x-ui.card title="Pengaturan Jabatan & Role">
                    <form action="{{ route('super-admin.users.change-role', $user) }}" method="POST" data-ajax="true">
                        @csrf
                        <div class="form-group">
                            <label>Pilih Role</label>
                            <select name="role" class="form-select text-white">
                                <option value="petugas_kesehatan" @selected($user->role === 'petugas_kesehatan')>Petugas Kesehatan (UKS)</option>
                                <option value="admin" @selected($user->role === 'admin')>Administrator (Master Data)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan Role</button>
                    </form>
                </x-ui.card>
            </div>

            <div class="col-12 grid-margin stretch-card">
                <x-ui.card title="Keamanan & Password">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-white">Quick Reset</h6>
                            <p class="text-muted text-small">Reset password menjadi string acak secara instan.</p>
                            <form action="{{ route('super-admin.users.quick-reset', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Reset password sekarang?')">
                                    <i class="mdi mdi-flash"></i> Quick Reset
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-white">Hapus Akun</h6>
                            <p class="text-muted text-small">Tindakan ini tidak dapat dibatalkan.</p>
                            <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user selamanya?')">
                                    <i class="mdi mdi-trash-can"></i> Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</div>
@endsection
