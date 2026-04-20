@extends('layouts.app')

@section('title', 'Santri Sakit')
@section('page-title', 'Manajemen Pasien UKS')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-user-nurse"></i> Pemantauan Santri Sakit</h2>
        <a href="{{ route('sickness-cases.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Kasus Baru
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari santri..." :value="request('search')" style="flex:1;" />
        <x-form.select name="status">
            <option value="">Semua Status</option>
            <option value="observed" @selected(request('status') === 'observed')>Observasi</option>
            <option value="handled" @selected(request('status') === 'handled')>Ditangani</option>
            <option value="recovered" @selected(request('status') === 'recovered')>Sembuh</option>
            <option value="referred" @selected(request('status') === 'referred')>Dirujuk</option>
        </x-form.select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('sickness-cases.index') }}" class="btn btn-outline">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Identitas Pasien</th>
                <th>Status</th>
                <th>Fasilitas & Obat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cases as $case)
                <tr>
                    <td style="white-space:nowrap; font-weight:700;">{{ $case->visit_date->format('d/m/Y') }}</td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">{{ $case->santri->name }}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($case->complaint, 40) }}</div>
                    </td>
                    <td>
                        @php
                            $statusTheme = match($case->status) {
                                'observed' => ['class' => 'badge-warning', 'label' => 'Observasi'],
                                'handled' => ['class' => 'badge-info', 'label' => 'Ditangani'],
                                'recovered' => ['class' => 'badge-success', 'label' => 'Sembuh'],
                                'referred' => ['class' => 'badge-danger', 'label' => 'Dirujuk'],
                                default => ['class' => 'badge-outline', 'label' => $case->status],
                            };
                        @endphp
                        <span class="badge {{ $statusTheme['class'] }}">{{ $statusTheme['label'] }}</span>
                    </td>
                    <td style="font-size:12px;">
                        <div><i class="fas fa-bed" style="width:16px;"></i> {{ optional($case->bed)->code ?: 'Tanpa Kasur' }}</div>
                        <div style="color:var(--text-muted);"><i class="fas fa-pills" style="width:16px;"></i> {{ optional($case->medicine)->name ?: '-' }}</div>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('sickness-cases.index', array_merge(request()->query(), ['edit' => $case->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('sickness-cases.notify', $case) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-success" title="WhatsApp Orang Tua">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </form>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('sickness-cases.destroy', $case) }}" onsubmit="return confirm('Hapus data kasus sakit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Belum ada data kunjungan santri sakit." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $cases->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editCase ? 'fa-pen-to-square' : 'fa-plus' }}"></i> {{ $editCase ? 'Edit Data Pasien' : 'Input Kasus Sakit Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editCase ? route('sickness-cases.update', $editCase) : route('sickness-cases.store') }}">
            @csrf
            @if($editCase)
                @method('PUT')
            @endif

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <x-form.field name="santri_id" label="Nama Pasien (Santri)">
                    <x-form.select name="santri_id">
                        <option value="">Cari santri...</option>
                        @foreach($santris as $santri)
                            <option value="{{ $santri->id }}" @selected((string) old('santri_id', $editCase->santri_id ?? '') === (string) $santri->id)>{{ $santri->name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="visit_date" label="Tanggal Masuk UKS">
                    <x-form.input name="visit_date" type="date" :value="old('visit_date', optional($editCase->visit_date ?? null)->format('Y-m-d') ?: now()->toDateString())" />
                </x-form.field>
            </div>

            <x-form.field name="complaint" label="Keluhan Utama">
                <x-form.textarea name="complaint" :value="$editCase->complaint ?? ''" rows="2" />
            </x-form.field>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="diagnosis" label="Hasil Diagnosis">
                    <x-form.input name="diagnosis" :value="$editCase->diagnosis ?? ''" placeholder="Misal: Demam tinggi, Gejala Maag" />
                </x-form.field>
                <x-form.field name="action_taken" label="Tindakan Awal">
                    <x-form.textarea name="action_taken" :value="$editCase->action_taken ?? ''" rows="1" placeholder="Misal: Kompres hangat, Istirahat" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="medicine_id" label="Obat yang Diberikan">
                    <x-form.select name="medicine_id">
                        <option value="">Pilih obat (Opsional)</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" @selected((string) old('medicine_id', $editCase->medicine_id ?? '') === (string) $medicine->id)>{{ $medicine->name }} (Stok: {{ $medicine->stock }})</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="infirmary_bed_id" label="Penempatan Kasur UKS">
                    <x-form.select name="infirmary_bed_id">
                        <option value="">Rawat Jalan (Tanpa Kasur)</option>
                        @foreach($beds as $bed)
                            <option value="{{ $bed->id }}" @selected((string) old('infirmary_bed_id', $editCase->infirmary_bed_id ?? '') === (string) $bed->id)>{{ $bed->code }} - {{ $bed->room_name }} ({{ $bed->status }})</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="status" label="Status Pasien Saat Ini">
                    <x-form.select name="status">
                        <option value="observed" @selected(old('status', $editCase->status ?? 'observed') === 'observed')>Observasi / Perlu Dipantau</option>
                        <option value="handled" @selected(old('status', $editCase->status ?? '') === 'handled')>Sudah Ditangani</option>
                        <option value="recovered" @selected(old('status', $editCase->status ?? '') === 'recovered')>Sembuh / Kembali ke Kamar</option>
                        <option value="referred" @selected(old('status', $editCase->status ?? '') === 'referred')>Perlu Dirujuk ke RS</option>
                    </x-form.select>
                </x-form.field>
                <x-form.field name="return_date" label="Tanggal Selesai / Sembuh">
                    <x-form.input name="return_date" type="date" :value="old('return_date', optional($editCase->return_date ?? null)->format('Y-m-d'))" />
                </x-form.field>
            </div>

            <label class="badge badge-outline" style="display:flex; align-items:center; gap:10px; padding:12px; border:1px solid var(--border); border-radius:12px; cursor:pointer; margin-bottom:24px;">
                <input type="checkbox" name="notify_guardian" value="1" {{ old('notify_guardian') ? 'checked' : '' }}>
                <span style="font-weight:600; color:var(--text-main);"><i class="fab fa-whatsapp" style="color:#25d366;"></i> Beri tahu orang tua melalui WhatsApp otomatis</span>
            </label>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editCase ? 'Simpan Perubahan' : 'Input Data' }}
                </button>
                <a href="{{ route('sickness-cases.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
