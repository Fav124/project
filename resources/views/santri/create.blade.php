@extends('layouts.app')

@section('title', 'Tambah Santri')
@section('page_title', 'Registrasi Santri Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('santri.index') }}">Santri</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<form action="{{ route('santri.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
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
                            <input type="text" name="nis" class="form-control" required placeholder="Contoh: 2024001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="Contoh: 0012345678">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jenis_kelamin" class="form-select select2" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status_santri" class="form-select select2" required>
                                <option value="aktif">Aktif</option>
                                <option value="cuti">Cuti</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
            </div>

            {{-- Data Wali --}}
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Data Wali Santri</h4>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addWaliRow()">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Wali
                    </button>
                </div>
                <div class="card-body">
                    <div id="wali-container">
                        <div class="wali-row border rounded p-3 mb-3 position-relative shadow-sm" id="wali-row-0">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeWaliRow(0)" title="Hapus Wali"></button>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Nama Wali</label>
                                    <input type="text" name="walis[0][nama_wali]" class="form-control" required placeholder="Nama ayah/ibu/wali">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Hubungan</label>
                                    <select name="walis[0][hubungan_wali]" class="form-select">
                                        <option value="Ayah">Ayah</option>
                                        <option value="Ibu">Ibu</option>
                                        <option value="Kakek/Nenek">Kakek/Nenek</option>
                                        <option value="Paman/Bibi">Paman/Bibi</option>
                                        <option value="Saudara">Saudara</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">No. WhatsApp Wali</label>
                                    <input type="text" name="walis[0][no_hp]" class="form-control" placeholder="08xxx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Pekerjaan</label>
                                    <input type="text" name="walis[0][pekerjaan]" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Alamat Wali</label>
                                    <textarea name="walis[0][alamat]" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Kesehatan Awal --}}
            <div class="card mt-3 border-start border-4 border-danger">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-danger"><i class="bi bi-heart-pulse-fill me-2"></i>Informasi Kesehatan Awal</h4>
                    <span class="badge bg-light-danger text-danger">Penting</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Golongan Darah</label>
                            <select name="golongan_darah" class="form-select select2">
                                <option value="">-</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tinggi (cm)</label>
                            <input type="number" name="tinggi_badan" class="form-control" placeholder="cm">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" name="berat_badan" class="form-control" placeholder="kg">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alergi (Obat/Makanan)</label>
                            <input type="text" name="alergi" class="form-control" placeholder="Jika ada, tuliskan di sini">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Riwayat Penyakit Kronis</label>
                            <textarea name="riwayat_penyakit" class="form-control" rows="2" placeholder="Contoh: Asma, Jantung, dsb"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kondisi Khusus Lainnya</label>
                            <textarea name="kondisi_khusus" class="form-control" rows="2" placeholder="Contoh: Memakai kacamata, pasca operasi, dsb"></textarea>
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
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan_id" class="form-select select2">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kamar / Asrama</label>
                        <select name="kamar_id" class="form-select select2">
                            <option value="">-- Pilih Kamar --</option>
                            @foreach($kamars as $km)
                                <option value="{{ $km->id }}">{{ $km->nama_kamar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto" class="form-control">
                        <small class="text-muted">Maksimal 2MB (JPG/PNG)</small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data Santri</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let waliCount = 1;

    function addWaliRow() {
        const index = waliCount++;
        const html = `
            <div class="wali-row border rounded p-3 mb-3 position-relative shadow-sm" id="wali-row-${index}">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeWaliRow(${index})" title="Hapus Wali"></button>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Nama Wali</label>
                        <input type="text" name="walis[${index}][nama_wali]" class="form-control" required placeholder="Nama ayah/ibu/wali">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Hubungan</label>
                        <select name="walis[${index}][hubungan_wali]" class="form-select">
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Kakek/Nenek">Kakek/Nenek</option>
                            <option value="Paman/Bibi">Paman/Bibi</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">No. WhatsApp Wali</label>
                        <input type="text" name="walis[${index}][no_hp]" class="form-control" placeholder="08xxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Pekerjaan</label>
                        <input type="text" name="walis[${index}][pekerjaan]" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Alamat Wali</label>
                        <textarea name="walis[${index}][alamat]" class="form-control" rows="1"></textarea>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('wali-container').insertAdjacentHTML('beforeend', html);
    }

    function removeWaliRow(index) {
        if (document.querySelectorAll('.wali-row').length > 1) {
            document.getElementById(`wali-row-${index}`).remove();
        } else {
            alert('Minimal harus ada satu data wali.');
        }
    }
</script>
@endpush
