@extends('layouts.app')

@section('title', 'Edit Santri - ' . $santri->name)
@section('page-title', 'Edit Data Santri')

@section('page-actions')
    <a href="{{ route('santri.show', $santri) }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <x-ui.card>
        <x-slot name="header">
            <h2><i class="fas fa-edit"></i> Edit Data Santri</h2>
        </x-slot>
        <div style="padding: 24px;">
            <form action="{{ route('santri.update', $santri) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- A. DATA DIRI --}}
                <h3 style="font-size: 14px; font-weight: 700; color: var(--brand-start); margin-bottom: 20px;">
                    <i class="fas fa-id-card"></i> A. Data Diri
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control text-white" value="{{ $santri->name }}" required>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">NIS</label>
                        <input type="text" name="nis" class="form-control text-white" value="{{ $santri->nis }}">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Jenis Kelamin</label>
                        <select name="gender" class="form-select text-white" required>
                            <option value="L" {{ $santri->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $santri->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control text-white" value="{{ $santri->birth_place }}">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control text-white" value="{{ $santri->birth_date ? $santri->birth_date->format('Y-m-d') : '' }}">
                    </div>
                </div>

                {{-- B. DATA AKADEMIK --}}
                <h3 style="font-size: 14px; font-weight: 700; color: var(--brand-start); border-top: 1px solid var(--border); padding-top: 24px; margin-bottom: 20px;">
                    <i class="fas fa-graduation-cap"></i> B. Data Akademik
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Kelas</label>
                        <select name="class_id" class="form-select text-white">
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $santri->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Jurusan</label>
                        <select name="major_id" class="form-select text-white">
                            <option value="">Pilih Jurusan</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ $santri->major_id == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Ruangan Kelas</label>
                        <input type="text" name="class_room" class="form-control text-white" value="{{ $santri->class_room }}">
                    </div>
                </div>

                {{-- C. DATA ORANG TUA / WALI --}}
                <h3 style="font-size: 14px; font-weight: 700; color: var(--brand-start); border-top: 1px solid var(--border); padding-top: 24px; margin-bottom: 20px;">
                    <i class="fas fa-users"></i> C. Data Orang Tua / Wali
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Nama Wali</label>
                        <input type="text" name="guardian_name" class="form-control text-white" value="{{ $santri->guardian_name }}">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">No. WhatsApp / Telepon</label>
                        <input type="text" name="guardian_phone" class="form-control text-white" value="{{ $santri->guardian_phone }}" placeholder="628xxx">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Hubungan dengan Santri</label>
                        <input type="text" name="guardian_relationship" class="form-control text-white" value="{{ $santri->guardian_relationship }}" placeholder="Ayah / Ibu / Wali">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Pekerjaan Wali</label>
                        <input type="text" name="guardian_job" class="form-control text-white" value="{{ $santri->guardian_job }}">
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Alamat Wali</label>
                    <textarea name="guardian_address" class="form-control text-white" rows="2">{{ $santri->guardian_address }}</textarea>
                </div>

                {{-- D. DATA KESEHATAN --}}
                <h3 style="font-size: 14px; font-weight: 700; color: var(--brand-start); border-top: 1px solid var(--border); padding-top: 24px; margin-bottom: 20px;">
                    <i class="fas fa-heartbeat"></i> D. Data Kesehatan
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Gol. Darah</label>
                        <select name="blood_type" class="form-select text-white">
                            <option value="">Pilih</option>
                            @foreach(['A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                <option value="{{ $bt }}" {{ $santri->blood_type == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tekanan Darah</label>
                        <input type="text" name="blood_pressure" class="form-control text-white" value="{{ $santri->blood_pressure }}" placeholder="120/80">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Tinggi (cm)</label>
                        <input type="number" name="height" class="form-control text-white" value="{{ $santri->height }}" step="0.1" min="0">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Berat (kg)</label>
                        <input type="number" name="weight" class="form-control text-white" value="{{ $santri->weight }}" step="0.1" min="0">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Alergi</label>
                        <textarea name="allergies" class="form-control text-white" rows="2" placeholder="Makanan, obat, dll.">{{ $santri->allergies }}</textarea>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Riwayat Penyakit</label>
                        <textarea name="medical_history" class="form-control text-white" rows="2" placeholder="Riwayat penyakit yang pernah diderita">{{ $santri->medical_history }}</textarea>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Kondisi Khusus</label>
                    <textarea name="special_condition" class="form-control text-white" rows="2" placeholder="Disabilitas, kebutuhan khusus, dll.">{{ $santri->special_condition }}</textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Catatan Medis Tambahan</label>
                    <textarea name="notes" class="form-control text-white" rows="3" placeholder="Catatan umum...">{{ $santri->notes }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px; border-top: 1px solid var(--border); padding-top: 24px;">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('santri.show', $santri) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
