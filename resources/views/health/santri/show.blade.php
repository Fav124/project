@extends('layouts.app')

@section('title', 'Detail Santri - ' . $santri->name)
@section('page-title', 'Profil & Rekam Jejak Santri')

@section('page-actions')
    <a href="{{ route('santri.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
    </a>
    <a href="{{ route('santri.edit', $santri) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">

    {{-- ===================== SIDEBAR KIRI ===================== --}}
    <div style="display:flex; flex-direction:column; gap:32px;">

        {{-- Profile Card --}}
        <x-ui.card>
            <div style="padding: 40px 32px; text-align: center; background: linear-gradient(to bottom, var(--bg-main), white); border-bottom: 1px solid var(--border);">
                <div class="user-avatar" style="width: 100px; height: 100px; font-size: 40px; margin: 0 auto 20px; box-shadow: var(--shadow-lg); border: 4px solid white; background: var(--brand-start); color: white;">
                    {{ strtoupper(substr($santri->name, 0, 1)) }}
                </div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">{{ $santri->name }}</h2>
                <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 16px;">NIS: {{ $santri->nis ?: 'Belum diatur' }}</p>

                <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    <span class="badge badge-info" style="padding: 6px 14px;">
                        <i class="fas fa-school"></i> {{ optional($santri->schoolClass)->name ?: '-' }}
                    </span>
                    <span class="badge badge-primary" style="padding: 6px 14px;">
                        <i class="fas fa-microscope"></i> {{ optional($santri->major)->name ?: '-' }}
                    </span>
                </div>
            </div>

            <div style="padding: 24px 32px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Jenis Kelamin</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tempat, Tanggal Lahir</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->birth_place ?: '-' }}, {{ optional($santri->birth_date)->translatedFormat('d F Y') ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Ruangan Kelas</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->class_room ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Ringkasan Kesehatan --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-heartbeat"></i> Ringkasan Kesehatan</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div style="background: var(--bg-main); text-align: center; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Gol. Darah</div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--danger);">{{ $santri->blood_type ?: '-' }}</div>
                        </div>
                        <div style="background: var(--bg-main); text-align: center; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tinggi</div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--brand-start);">{{ $santri->height ? $santri->height . ' cm' : '-' }}</div>
                        </div>
                        <div style="background: var(--bg-main); text-align: center; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Berat</div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--brand-start);">{{ $santri->weight ? $santri->weight . ' kg' : '-' }}</div>
                        </div>
                        <div style="background: var(--bg-main); text-align: center; padding: 16px; border-radius: 12px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tek. Darah</div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--warning);">{{ $santri->blood_pressure ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- ===================== KONTEN UTAMA ===================== --}}
    <div style="display:flex; flex-direction:column; gap:32px;">

        {{-- A. DATA DIRI --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-id-card"></i> A. Data Diri</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Nama Lengkap</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">NIS</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->nis ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Jenis Kelamin</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Ruangan Kelas</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->class_room ?: '-' }}</div>
                    </div>
                </div>

                @if($santri->guardians->isNotEmpty())
                    <h3 style="font-size: 13px; font-weight: 700; color: var(--text-main); border-top: 1px solid var(--border); padding-top: 20px; margin-top: 24px; margin-bottom: 16px;">
                        <i class="fas fa-users"></i> Data Orang Tua / Wali
                    </h3>
                    @foreach($santri->guardians as $guardian)
                        <div style="background: var(--bg-main); border-radius: 12px; padding: 16px; border: 1px solid var(--border); margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 700; color: var(--text-main);">{{ $guardian->name }}</span>
                                @if($guardian->is_primary)
                                    <span class="badge badge-primary">Utama</span>
                                @endif
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 14px;">
                                <div><span style="color: var(--text-muted);">Hubungan:</span> {{ $guardian->relationship ?: '-' }}</div>
                                <div><span style="color: var(--text-muted);">Telepon:</span>
                                    @if($guardian->phone)
                                        <a href="https://wa.me/{{ $guardian->phone }}" target="_blank" style="color: var(--success); font-weight: 600;">{{ $guardian->phone }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div><span style="color: var(--text-muted);">Pekerjaan:</span> {{ $guardian->job ?: '-' }}</div>
                                <div style="grid-column: 1 / -1;"><span style="color: var(--text-muted);">Alamat:</span> {{ $guardian->address ?: '-' }}</div>
                            </div>
                        </div>
                    @endforeach
                @elseif($santri->guardian_name)
                    <h3 style="font-size: 13px; font-weight: 700; color: var(--text-main); border-top: 1px solid var(--border); padding-top: 20px; margin-top: 24px; margin-bottom: 16px;">
                        <i class="fas fa-user"></i> Data Orang Tua / Wali
                    </h3>
                    <div style="background: var(--bg-main); border-radius: 12px; padding: 16px; border: 1px solid var(--border);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 14px;">
                            <div style="grid-column: 1 / -1;"><span style="color: var(--text-muted);">Nama:</span> <span style="font-weight: 600;">{{ $santri->guardian_name }}</span></div>
                            <div><span style="color: var(--text-muted);">Telepon:</span>
                                @if($santri->guardian_phone)
                                    <a href="https://wa.me/{{ $santri->guardian_phone }}" target="_blank" style="color: var(--success); font-weight: 600;">{{ $santri->guardian_phone }}</a>
                                @else
                                    -
                                @endif
                            </div>
                            <div><span style="color: var(--text-muted);">Hubungan:</span> {{ $santri->guardian_relationship ?: '-' }}</div>
                            <div><span style="color: var(--text-muted);">Pekerjaan:</span> {{ $santri->guardian_job ?: '-' }}</div>
                            <div style="grid-column: 1 / -1;"><span style="color: var(--text-muted);">Alamat:</span> {{ $santri->guardian_address ?: '-' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- B. DATA AKADEMIK --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-graduation-cap"></i> B. Data Akademik</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Kelas</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ optional($santri->schoolClass)->name ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Jurusan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ optional($santri->major)->name ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Ruangan Kelas</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->class_room ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- C. DATA KESEHATAN --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-notes-medical"></i> C. Data Kesehatan</h2>
            </x-slot>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Golongan Darah</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->blood_type ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tekanan Darah</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->blood_pressure ?: '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tinggi Badan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->height ? $santri->height . ' cm' : '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Berat Badan</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $santri->weight ? $santri->weight . ' kg' : '-' }}</div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Alergi</div>
                        @if($santri->allergies)
                            <div style="padding: 12px 16px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px;">
                                <p style="margin: 0; color: #92400e; line-height: 1.6;">{{ $santri->allergies }}</p>
                            </div>
                        @else
                            <span style="color: var(--text-muted);">Tidak ada alergi tercatat</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Riwayat Penyakit</div>
                        @if($santri->medical_history)
                            <div style="padding: 12px 16px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px;">
                                <p style="margin: 0; color: #991b1b; line-height: 1.6;">{{ $santri->medical_history }}</p>
                            </div>
                        @else
                            <span style="color: var(--text-muted);">Tidak ada riwayat penyakit tercatat</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Kondisi Khusus</div>
                        @if($santri->special_condition)
                            <div style="padding: 12px 16px; background: #ede9fe; border-left: 4px solid #8b5cf6; border-radius: 8px;">
                                <p style="margin: 0; color: #5b21b6; line-height: 1.6;">{{ $santri->special_condition }}</p>
                            </div>
                        @else
                            <span style="color: var(--text-muted);">Tidak ada kondisi khusus</span>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Catatan Medis</div>
                        @if($santri->notes)
                            <div style="padding: 12px 16px; background: #e0f2fe; border-left: 4px solid #0ea5e9; border-radius: 8px;">
                                <p style="margin: 0; color: #0c4a6e; line-height: 1.6;">{{ $santri->notes }}</p>
                            </div>
                        @else
                            <span style="color: var(--text-muted);">Tidak ada catatan medis</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- D. RIWAYAT KESEHATAN --}}
        <x-ui.card>
            <x-slot name="header">
                <h2><i class="fas fa-history"></i> D. Riwayat Kesehatan</h2>
            </x-slot>
            <div style="padding: 24px;">
                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 12px;">
                    <i class="fas fa-thermometer-half"></i> Kunjungan Sakit Terakhir
                </h3>
                @if($santri->sicknessCases->isNotEmpty())
                    <table class="table" style="color: var(--text-main);">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($santri->sicknessCases as $case)
                                <tr>
                                    <td>{{ $case->visit_date->translatedFormat('d M Y') }}</td>
                                    <td>{{ Str::limit($case->complaint, 40) }}</td>
                                    <td>
                                        @php
                                            $statusMap = ['observed' => 'badge-warning', 'handled' => 'badge-info', 'recovered' => 'badge-success', 'referred' => 'badge-danger'];
                                        @endphp
                                        <span class="badge {{ $statusMap[$case->status] ?? 'badge-secondary' }}">
                                            {{ $case->status_label ?? $case->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 8px;">
                        <a href="{{ route('sickness-cases.index', ['search' => $santri->name]) }}" class="btn btn-xs btn-outline-info">
                            Lihat Semua Riwayat Kunjungan
                        </a>
                    </div>
                @else
                    <x-ui.empty-state message="Belum ada riwayat kunjungan sakit." />
                @endif

                <h3 style="font-size: 13px; font-weight: 700; color: var(--text-main); margin-top: 32px; margin-bottom: 12px;">
                    <i class="fas fa-hospital"></i> Rujukan Rumah Sakit Terakhir
                </h3>
                @if($santri->hospitalReferrals->isNotEmpty())
                    <table class="table" style="color: var(--text-main);">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>RS Tujuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($santri->hospitalReferrals as $referral)
                                <tr>
                                    <td>{{ $referral->referral_date->translatedFormat('d M Y') }}</td>
                                    <td>{{ $referral->hospital_name }}</td>
                                    <td>
                                        <span class="badge {{ $referral->status == 'completed' ? 'badge-success' : ($referral->status == 'ongoing' ? 'badge-info' : 'badge-warning') }}">
                                            {{ $referral->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 8px;">
                        <a href="{{ route('referrals.index', ['search' => $santri->name]) }}" class="btn btn-xs btn-outline-info">
                            Lihat Semua Riwayat Rujukan
                        </a>
                    </div>
                @else
                    <x-ui.empty-state message="Belum ada riwayat rujukan RS." />
                @endif
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
