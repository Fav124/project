@extends('layouts.app')

@section('title', 'Data Santri')
@section('page-title', 'Manajemen Data Santri')

@section('content')
<x-ui.card>
    <x-slot name="header">
        <h2><i class="fas fa-graduation-cap"></i> Daftar Santri Terdaftar</h2>
        <a href="{{ route('santri.index', array_merge(request()->query(), ['create' => 1])) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Santri
        </a>
    </x-slot>

    <x-ui.filter-bar method="GET">
        <x-form.input name="search" placeholder="Cari nama, NIS, atau asrama..." :value="request('search')" />
        <button type="submit" class="btn btn-outline"><i class="fas fa-search"></i> Cari</button>
        <a href="{{ route('santri.index') }}" class="btn btn-secondary">Reset</a>
    </x-ui.filter-bar>

    <x-ui.table>
        <thead>
            <tr>
                <th>Identitas Santri</th>
                <th>Kelas & Jurusan</th>
                <th>Lokasi Asrama</th>
                <th>Kontak Wali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($santris as $santri)
                <tr>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">{{ $santri->name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            <i class="fas fa-id-card" style="width: 14px;"></i> {{ $santri->nis ?: 'NIS Belum Ada' }} • 
                            <i class="fas {{ $santri->gender === 'L' ? 'fa-mars' : 'fa-venus' }}" style="width: 14px;"></i> {{ $santri->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ optional($santri->schoolClass)->name ?: '-' }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <span class="badge badge-info" style="font-size: 10px; padding: 2px 8px;">{{ optional($santri->major)->name ?: 'Umum' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 14px;"><i class="fas fa-building-user" style="color: var(--text-muted); margin-right: 6px;"></i>{{ $santri->dorm_room ?: '-' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; font-size: 14px;">{{ $santri->guardian_name ?: '-' }}</div>
                        <div style="font-size: 12px; color: var(--success); font-weight: 600;">
                            <i class="fab fa-whatsapp"></i> {{ $santri->guardian_phone ?: 'Tidak ada nomor' }}
                        </div>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('santri.index', array_merge(request()->query(), ['edit' => $santri->id])) }}" class="btn btn-xs btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('santri.destroy', $santri) }}" onsubmit="return confirm('Hapus data santri ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <x-ui.empty-state :colspan="5" message="Belum ada data santri yang terdaftar." />
            @endforelse
        </tbody>
    </x-ui.table>

    <x-slot name="footer">
        {{ $santris->links() }}
    </x-slot>
</x-ui.card>

@if($showForm)
    <x-ui.card class="mt-4">
        <x-slot name="header">
            <h2><i class="fas {{ $editSantri ? 'fa-user-pen' : 'fa-user-plus' }}"></i> {{ $editSantri ? 'Edit Profil Santri' : 'Tambah Santri Baru' }}</h2>
        </x-slot>
        <form method="POST" action="{{ $editSantri ? route('santri.update', $editSantri) : route('santri.store') }}">
            @csrf
            @if($editSantri)
                @method('PUT')
            @endif

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 24px;">
                <x-form.field name="nis" label="Nomor Induk Santri (NIS)">
                    <x-form.input name="nis" :value="$editSantri->nis ?? ''" placeholder="Contoh: 12345" />
                </x-form.field>
                <x-form.field name="name" label="Nama Lengkap">
                    <x-form.input name="name" :value="$editSantri->name ?? ''" placeholder="Masukkan nama lengkap santri" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1.5fr 1.5fr; gap: 24px;">
                <x-form.field name="gender" label="Jenis Kelamin">
                    <x-form.select name="gender">
                        <option value="">Pilih</option>
                        <option value="L" @selected(old('gender', $editSantri->gender ?? '') === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $editSantri->gender ?? '') === 'P')>Perempuan</option>
                    </x-form.select>
                </x-form.field>
                <x-form.field name="birth_place" label="Tempat Lahir">
                    <x-form.input name="birth_place" :value="$editSantri->birth_place ?? ''" placeholder="Kota lahir" />
                </x-form.field>
                <x-form.field name="birth_date" label="Tanggal Lahir">
                    <x-form.input name="birth_date" type="date" :value="optional($editSantri->birth_date ?? null)->format('Y-m-d')" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                <x-form.field name="class_id" label="Kelas Utama">
                    <x-form.select name="class_id">
                        <option value="">Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected((string) old('class_id', $editSantri->class_id ?? '') === (string) $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="major_id" label="Jurusan Spesifik">
                    <x-form.select name="major_id">
                        <option value="">Pilih jurusan</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" @selected((string) old('major_id', $editSantri->major_id ?? '') === (string) $major->id)>{{ $major->name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field name="dorm_room" label="Kamar / Gedung Asrama">
                    <x-form.input name="dorm_room" :value="$editSantri->dorm_room ?? ''" placeholder="Contoh: Gedung A, Kamar 04" />
                </x-form.field>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <x-form.field name="guardian_name" label="Nama Orang Tua / Wali">
                    <x-form.input name="guardian_name" :value="$editSantri->guardian_name ?? ''" placeholder="Nama wali santri" />
                </x-form.field>
                <x-form.field name="guardian_phone" label="Nomor WhatsApp Wali">
                    <x-form.input name="guardian_phone" :value="$editSantri->guardian_phone ?? ''" placeholder="Contoh: 62812345678" />
                </x-form.field>
            </div>

            <x-form.field name="notes" label="Catatan Riwayat Kesehatan / Alergi">
                <x-form.textarea name="notes" :value="$editSantri->notes ?? ''" placeholder="Informasi medis penting (misal: alergi kacang, asma, dll)" />
            </x-form.field>

            <x-form.actions>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ $editSantri ? 'Simpan Perubahan' : 'Tambah Santri' }}
                </button>
                <a href="{{ route('santri.index') }}" class="btn btn-secondary">Batal</a>
            </x-form.actions>
        </form>
    </x-ui.card>
@endif
@endsection
