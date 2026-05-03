@extends('layouts.app')

@section('title', 'Edit Santri')
@section('page_title', 'Perbarui Data Santri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('santri.index') }}">Santri</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<form action="{{ route('santri.update', $santri->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        {{-- Data Pokok --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Identitas</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control" value="{{ old('nis', $santri->nis) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $santri->nisn) }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $santri->nama_lengkap) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select select2" required>
                                <option value="L" {{ $santri->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $santri->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status_santri" class="form-select select2" required>
                                <option value="aktif" {{ $santri->status_santri == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="cuti" {{ $santri->status_santri == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="lulus" {{ $santri->status_santri == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="pindah" {{ $santri->status_santri == 'pindah' ? 'selected' : '' }}>Pindah</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $santri->tanggal_lahir?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $santri->alamat) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Kesehatan --}}
            <div class="card mt-3 border-start border-4 border-danger">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-danger"><i class="bi bi-heart-pulse-fill me-2"></i>Informasi Kesehatan</h4>
                    <a href="{{ route('santri.edit-health', $santri->id) }}" class="btn btn-sm btn-outline-danger">Edit Detail Klinis</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select select2">
                                <option value="">-</option>
                                <option value="A" {{ $santri->kesehatan?->golongan_darah == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ $santri->kesehatan?->golongan_darah == 'B' ? 'selected' : '' }}>B</option>
                                <option value="AB" {{ $santri->kesehatan?->golongan_darah == 'AB' ? 'selected' : '' }}>AB</option>
                                <option value="O" {{ $santri->kesehatan?->golongan_darah == 'O' ? 'selected' : '' }}>O</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tinggi (cm)</label>
                            <input type="number" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $santri->kesehatan?->tinggi_badan) }}" placeholder="cm">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" name="berat_badan" class="form-control" value="{{ old('berat_badan', $santri->kesehatan?->berat_badan) }}" placeholder="kg">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alergi (Obat/Makanan)</label>
                            <input type="text" name="alergi" class="form-control" value="{{ old('alergi', $santri->kesehatan?->alergi) }}" placeholder="Jika ada, tuliskan di sini">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Riwayat Penyakit Kronis</label>
                            <textarea name="riwayat_penyakit" class="form-control" rows="2" placeholder="Contoh: Asma, Jantung, dsb">{{ old('riwayat_penyakit', $santri->kesehatan?->riwayat_penyakit) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kondisi Khusus Lainnya</label>
                            <textarea name="kondisi_khusus" class="form-control" rows="2" placeholder="Contoh: Memakai kacamata, pasca operasi, dsb">{{ old('kondisi_khusus', $santri->kesehatan?->kondisi_khusus) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            @php $wali = $santri->waliSantris->first(); @endphp
            {{-- Data Wali --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title">Data Wali Santri</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $wali?->nama_wali) }}" placeholder="Nama ayah/ibu/wali">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hubungan</label>
                            <input type="text" name="hubungan_wali" class="form-control" value="{{ old('hubungan_wali', $wali?->hubungan_wali) }}" placeholder="Ayah, Ibu, Kakak, dsb">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. WhatsApp Wali</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $wali?->no_hp) }}" placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Wali</label>
                            <textarea name="alamat_wali" class="form-control" rows="2" placeholder="Alamat lengkap wali">{{ old('alamat_wali', $wali?->alamat) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Penempatan --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Penempatan & Akademik</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-select select2" required>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $santri->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" class="form-select select2">
                            <option value="">-- Tanpa Jurusan --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ $santri->jurusan_id == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kamar / Asrama</label>
                        <select name="kamar_id" class="form-select select2">
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($kamars as $km)
                                <option value="{{ $km->id }}" {{ $santri->kamar_id == $km->id ? 'selected' : '' }}>{{ $km->nama_kamar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil (Biarkan kosong jika tidak diubah)</label>
                        @if($santri->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto Santri" width="100" class="rounded shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="foto" class="form-control">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('santri.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning text-white">Perbarui Data Santri</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
