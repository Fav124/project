@extends('layouts.app')

@section('title', 'Rujukan Rumah Sakit')
@section('page-title', 'Rujukan & Evakuasi Medis')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-truck-medical"></i> Log Rujukan Luar</h2>
        <a href="{{ route('referrals.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Rujukan
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari santri / RS..." :value="request('search')" style="flex:1;" />
        <x-form.select name="status">
            <option value="">Semua Status</option>
            <option value="referred" @selected(request('status') === 'referred')>Dirujuk</option>
            <option value="treated" @selected(request('status') === 'treated')>Ditangani RS</option>
            <option value="returned" @selected(request('status') === 'returned')>Sudah Kembali</option>
        </x-form.select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('referrals.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Santri & Keluhan</th>
                <th>Rumah Sakit</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrals as $referral)
                <tr>
                    <td style="white-space:nowrap; font-weight:700;">{{ $referral->referral_date->format('d/m/Y') }}</td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">{{ $referral->santri->name }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($referral->complaint, 40) }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $referral->hospital_name }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">Pendamping: {{ $referral->companion_name ?: '-' }}</div>
                    </td>
                    <td>
                        @php
                            $statusTheme = match($referral->status) {
                                'referred' => ['class' => 'badge-warning', 'label' => 'Dirujuk'],
                                'treated' => ['class' => 'badge-info', 'label' => 'Ditangani'],
                                'returned' => ['class' => 'badge-success', 'label' => 'Kembali'],
                                default => ['class' => 'badge-outline', 'label' => $referral->status],
                            };
                        @endphp
                        <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('referrals.index', array_merge(request()->query(), ['edit' => $referral->id])) }}" class="btn btn-xs btn-info" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('referrals.notify', $referral) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-success" title="Kirim WA Wali">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </form>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('referrals.destroy', $referral) }}" onsubmit="return confirm('Hapus data rujukan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Belum ada data rujukan rumah sakit." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $referrals->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editReferral ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editReferral ? 'Edit Data Rujukan' : 'Tambah Rujukan Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editReferral ? route('referrals.update', $editReferral) : route('referrals.store') }}">
            @csrf
            @if($editReferral)
                @method('PUT')
            @endif

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <x-form.field name="santri_id" label="Santri yang Dirujuk">
                    <x-form.select name="santri_id">
                        <option value="">Pilih santri...</option>
                        @foreach($santris as $santri)
                            <option value="{{ $santri->id }}" @selected((string) old('santri_id', $editReferral->santri_id ?? '') === (string) $santri->id)>{{ $santri->name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="referral_date" label="Tanggal Rujukan">
                    <x-form.input name="referral_date" type="date" :value="old('referral_date', optional($editReferral->referral_date ?? null)->format('Y-m-d') ?: now()->toDateString())" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="hospital_name" label="Nama Rumah Sakit / Klinik">
                    <x-form.input name="hospital_name" :value="$editReferral->hospital_name ?? ''" placeholder="Contoh: RSUD Dr. Soetomo" />
                </x-form.field>
                <x-form.field name="diagnosis" label="Diagnosis Sementara">
                    <x-form.input name="diagnosis" :value="$editReferral->diagnosis ?? ''" placeholder="Alasan rujukan" />
                </x-form.field>
            </div>

            <x-form.field name="complaint" label="Keluhan Utama">
                <x-form.textarea name="complaint" :value="$editReferral->complaint ?? ''" rows="3" />
            </x-form.field>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                <x-form.field name="transport" label="Transportasi">
                    <x-form.input name="transport" :value="$editReferral->transport ?? ''" placeholder="Ambulans / Mobil" />
                </x-form.field>
                <x-form.field name="companion_name" label="Nama Pendamping">
                    <x-form.input name="companion_name" :value="$editReferral->companion_name ?? ''" placeholder="Nama petugas" />
                </x-form.field>
                <x-form.field name="status" label="Status Saat Ini">
                    <x-form.select name="status">
                        <option value="referred" @selected(old('status', $editReferral->status ?? 'referred') === 'referred')>Dirujuk</option>
                        <option value="treated" @selected(old('status', $editReferral->status ?? '') === 'treated')>Ditangani RS</option>
                        <option value="returned" @selected(old('status', $editReferral->status ?? '') === 'returned')>Sudah Kembali</option>
                    </x-form.select>
                </x-form.field>
            </div>

            <x-form.field name="notes" label="Catatan Tambahan (Obat RS, dll)">
                <x-form.textarea name="notes" :value="$editReferral->notes ?? ''" rows="3" />
            </x-form.field>

            <label class="badge badge-outline" style="display:flex; align-items:center; gap:10px; padding:12px; border:1px solid var(--border); border-radius:12px; cursor:pointer; margin-bottom:24px;">
                <input type="checkbox" name="notify_guardian" value="1" {{ old('notify_guardian') ? 'checked' : '' }}>
                <span style="font-weight:600; color:var(--text-main);"><i class="fab fa-whatsapp" style="color:#25d366;"></i> Kirim notifikasi WhatsApp ke wali santri otomatis</span>
            </label>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editReferral ? 'Simpan Perubahan' : 'Buat Rujukan' }}
                </button>
                <a href="{{ route('referrals.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
