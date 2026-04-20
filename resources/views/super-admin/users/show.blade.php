@extends('layouts.app')

@section('title', 'Kelola Pengguna - ' . $user->name)
@section('page-title', 'Otoritas & Detail Pengguna')

@section('page-actions')
    <a href="{{ route('super-admin.users') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1.2fr 1fr; gap: 32px; align-items: start;">
    
    <div style="display:flex; flex-direction:column; gap:32px;">
        {{-- Profile Hero Card --}}
        <x-ui.card>
            <div style="padding: 40px 32px; text-align: center; background: linear-gradient(to bottom, var(--bg-main), white); border-bottom: 1px solid var(--border);">
                <div class="user-avatar" style="width: 100px; height: 100px; font-size: 40px; margin: 0 auto 20px; box-shadow: var(--shadow-lg); border: 4px solid white;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $user->name }}</h2>
                <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 16px;">{{ $user->email }}</p>
                
                <div style="display: flex; justify-content: center; gap: 12px;">
                    @php
                        $roleTheme = match($user->role) {
                            'admin' => ['class' => 'badge-primary', 'icon' => 'fa-user-tie', 'label' => 'Administrator'],
                            'petugas_kesehatan' => ['class' => 'badge-info', 'icon' => 'fa-stethoscope', 'label' => 'Petugas Kesehatan'],
                            default => ['class' => 'badge-outline', 'icon' => 'fa-user', 'label' => 'User'],
                        };
                        $statusTheme = match($user->status) {
                            'approved' => ['class' => 'badge-success', 'icon' => 'fa-check-circle', 'label' => 'Aktif'],
                            'rejected' => ['class' => 'badge-danger', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                            default => ['class' => 'badge-warning', 'icon' => 'fa-clock', 'label' => 'Menunggu Approval'],
                        };
                    @endphp
                    <span class="badge {{ $roleTheme['class'] }}" style="padding: 6px 14px; font-size: 13px;">
                        <i class="fas {{ $roleTheme['icon'] }}"></i> {{ $roleTheme['label'] }}
                    </span>
                    <span class="badge {{ $statusTheme['class'] }}" style="padding: 6px 14px; font-size: 13px;">
                        <i class="fas {{ $statusTheme['icon'] }}"></i> {{ $statusTheme['label'] }}
                    </span>
                </div>
            </div>
            
            <div style="padding: 24px 32px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Tanggal Registrasi</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $user->created_at->format('d F Y, H:i') }}</div>
                    </div>
                    @if($user->approved_at)
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">{{ $user->status === 'approved' ? 'Waktu Persetujuan' : 'Waktu Penolakan' }}</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $user->approved_at->format('d F Y, H:i') }}</div>
                        </div>
                    @endif
                    @if($user->approvedBy)
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Diproses Oleh</div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $user->approvedBy->name }}</div>
                        </div>
                    @endif
                </div>

                @if($user->rejection_reason)
                    <div style="margin-top: 24px; padding: 16px; background: #fef2f2; border-radius: 12px; border: 1px solid #fee2e2;">
                        <div style="font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase; margin-bottom: 6px;">Alasan Penolakan</div>
                        <div style="font-size: 14px; color: #991b1b; line-height: 1.5;">{{ $user->rejection_reason }}</div>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- Security Card --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-shield-halved"></i> Keamanan & Kata Sandi</h2>
            </x-slot>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="padding: 20px; background: var(--bg-main); border-radius: 16px; border: 1px solid var(--border);">
                    <div style="font-weight: 700; margin-bottom: 4px;">Reset Otomatis</div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">Generate password random dan simpan secara langsung.</p>
                    <form method="POST" action="{{ route('super-admin.users.quick-reset', $user) }}" onsubmit="return confirm('Reset password sekarang?')">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: #f59e0b;">
                            <i class="fas fa-bolt"></i> Quick Reset
                        </button>
                    </form>
                </div>
                <div style="padding: 20px; background: var(--bg-main); border-radius: 16px; border: 1px solid var(--border);">
                    <div style="font-weight: 700; margin-bottom: 4px;">Reset Manual</div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">Tentukan kata sandi khusus untuk pengguna ini.</p>
                    <button type="button" class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="document.getElementById('manual-reset-form').style.display='block'; this.style.display='none';">
                        <i class="fas fa-keyboard"></i> Set Manual
                    </button>
                    <div id="manual-reset-form" style="display: none;">
                        <form method="POST" action="{{ route('super-admin.users.reset-password', $user) }}">
                            @csrf
                            <input type="password" name="new_password" class="form-input" placeholder="Password baru" style="margin-bottom: 8px;">
                            <input type="password" name="new_password_confirmation" class="form-input" placeholder="Ulangi password" style="margin-bottom: 12px;">
                            <div style="display: flex; gap: 8px;">
                                <button type="submit" class="btn btn-xs btn-primary" style="flex: 1; justify-content: center;">Simpan</button>
                                <button type="button" class="btn btn-xs btn-outline" onclick="location.reload()">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div style="display:flex; flex-direction:column; gap:32px;">
        {{-- Decision Panel --}}
        @if($user->isPending() || $user->isRejected())
            <x-ui.card style="border: 2px solid var(--warning);">
                <x-slot name="header">
                    <h2 style="color: var(--warning);"><i class="fas fa-gavel"></i> Panel Keputusan</h2>
                </x-slot>
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 20px;">
                    Tentukan apakah pengguna ini layak mendapatkan akses ke sistem DeisaHealth.
                </p>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <form method="POST" action="{{ route('super-admin.users.approve', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; background: var(--success); font-size: 16px; padding: 14px;">
                            <i class="fas fa-check-circle"></i> Setujui & Beri Akses
                        </button>
                    </form>
                    
                    @if(!$user->isRejected())
                        <button type="button" class="btn btn-outline" style="width: 100%; justify-content: center; color: var(--danger); border-color: var(--danger);" onclick="document.getElementById('reject-form-panel').style.display='block'; this.style.display='none';">
                            <i class="fas fa-times-circle"></i> Tolak Pendaftaran
                        </button>
                        <div id="reject-form-panel" style="display: none; padding-top: 12px; border-top: 1px solid var(--border);">
                            <form method="POST" action="{{ route('super-admin.users.reject', $user) }}">
                                @csrf
                                <label class="form-label">Alasan Penolakan</label>
                                <textarea name="rejection_reason" class="form-input" rows="3" placeholder="Misal: Data tidak valid..." style="margin-bottom: 12px;"></textarea>
                                <div style="display: flex; gap: 8px;">
                                    <button type="submit" class="btn btn-danger" style="flex: 1; justify-content: center;">Konfirmasi Tolak</button>
                                    <button type="button" class="btn btn-outline" style="flex: 1; justify-content: center;" onclick="location.reload()">Batal</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        @endif

        {{-- Role Management --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-user-tag"></i> Pengaturan Jabatan</h2>
            </x-slot>
            <form method="POST" action="{{ route('super-admin.users.change-role', $user) }}">
                @csrf
                <x-form.field name="role" label="Pilih Role Pengguna">
                    <x-form.select name="role">
                        <option value="petugas_kesehatan" @selected($user->role === 'petugas_kesehatan')>Petugas Kesehatan (UKS)</option>
                        <option value="admin" @selected($user->role === 'admin')>Administrator (Data Master)</option>
                    </x-form.select>
                </x-form.field>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-save"></i> Perbarui Role
                </button>
            </form>
        </x-ui.card>

        {{-- Dangerous Zone --}}
        <x-ui.card style="background: #fff1f2; border: 1px solid #fecdd3;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-weight: 800; color: #9f1239; font-size: 15px;">Hapus Akun Permanen</div>
                    <div style="font-size: 12px; color: #be123c;">Seluruh data terkait user ini akan hilang.</div>
                </div>
                <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini selamanya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="background: #e11d48;">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </form>
            </div>
        </x-ui.card>

        {{-- WhatsApp Action --}}
        <div style="padding: 24px; background: #25d366; border-radius: 20px; color: white; text-align: center; box-shadow: 0 10px 15px -3px rgba(37, 211, 102, 0.3);">
            <div style="font-size: 24px; margin-bottom: 8px;"><i class="fab fa-whatsapp"></i></div>
            <div style="font-weight: 700; margin-bottom: 4px;">Hubungi Pengguna</div>
            <p style="font-size: 12px; opacity: 0.9; margin-bottom: 16px;">Kirim pesan konfirmasi akun secara manual melalui WhatsApp.</p>
            <a href="https://wa.me/{{ $user->phone ?? '' }}?text=Halo%20{{ urlencode($user->name) }},%20pendaftaran%20akun%20DeisaHealth%20Anda%20sedang%20kami%20proses." target="_blank" class="btn" style="background: white; color: #25d366; width: 100%; justify-content: center;">
                Buka WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection
