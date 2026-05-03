@extends('layouts.app')

@section('title', 'Pemeriksaan Baru')
@section('page_title', 'Form Pemeriksaan Santri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kunjungan.index') }}">Kunjungan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Baru</li>
@endsection

@section('content')
<form action="{{ route('kunjungan.store') }}" method="POST">
    @csrf
    <div class="row">
        {{-- Kiri: Data Santri & Pemeriksaan --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Anamnesis & Fisik</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Santri <span class="text-danger">*</span></label>
                        <select name="santri_id" class="form-select select2" required>
                            <option value="">-- Cari Santri --</option>
                            @foreach($santris as $s)
                                <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama_lengkap }} ({{ $s->kelas?->nama_kelas }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keluhan Utama <span class="text-danger">*</span></label>
                        <textarea name="keluhan" class="form-control" rows="2" placeholder="Apa yang dirasakan santri?" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anamnesis / Riwayat Sekarang</label>
                        <textarea name="anamnesis" class="form-control" rows="3" placeholder="Detail keluhan, durasi, dll..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diagnosa Sementara</label>
                            <input type="text" name="diagnosa_sementara" class="form-control" placeholder="Misal: Febris, Dyspepsia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tindakan Medis</label>
                            <input type="text" name="tindakan" class="form-control" placeholder="Misal: Kompres, Istirahat">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rencana Tindak Lanjut --}}
            <div class="card border-primary">
                <div class="card-header bg-primary py-2">
                    <h5 class="card-title text-white mb-0">Rencana Tindak Lanjut</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tindakan Selanjutnya <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tindak_lanjut" id="tl_kamar" value="kembali_kamar" checked>
                                <label class="form-check-label" for="tl_kamar">Boleh Kembali ke Kamar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tindak_lanjut" id="tl_uks" value="rawat_inap">
                                <label class="form-check-label text-warning fw-bold" for="tl_uks">Istirahat di UKS (Rawat Inap)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tindak_lanjut" id="tl_rs" value="rujuk_rs">
                                <label class="form-check-label text-danger" for="tl_rs">Rujuk ke Rumah Sakit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tindak_lanjut" id="tl_pulang" value="pulang">
                                <label class="form-check-label text-info" for="tl_pulang">Pulang ke Rumah</label>
                            </div>
                        </div>
                    </div>

                    <div id="sectionRawatInap" class="mt-3 d-none border-top pt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilih Kasur UKS</label>
                                <select name="kasur_id" class="form-select">
                                    <option value="">-- Pilih Kasur --</option>
                                    @foreach($kasurs as $k)
                                        <option value="{{ $k->id }}">{{ $k->kode_kasur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi Santri Saat Masuk</label>
                                <input type="text" name="kondisi_masuk" class="form-control" placeholder="Misal: Lemas, Pucat, Sadar">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimasi Kembali ke Pondok</label>
                                <input type="datetime-local" name="estimasi_kembali" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alasan Rawat / Observasi</label>
                                <input type="text" name="alasan_rawat" class="form-control" placeholder="Kondisi yang mewajibkan rawat inap (Misal: Demam tinggi > 39C)">
                            </div>
                        </div>
                    </div>

                    <div id="sectionLuar" class="mt-3 d-none border-top pt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimasi Kembali ke Pondok <small class="text-muted">(Opsional)</small></label>
                                <input type="datetime-local" name="estimasi_kembali_luar" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alasan / Catatan Izin Keluar</label>
                                <input type="text" name="alasan_luar" class="form-control" placeholder="Misal: Dirujuk ke RSUD, Dijemput Orang Tua">
                            </div>
                            
                            {{-- Additional Hospital Fields --}}
                            <div id="extraRSFields" class="d-none">
                                <div class="col-12 mb-3">
                                    <label class="form-label text-danger fw-bold">Nama Rumah Sakit <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_rs" id="input_nama_rs" class="form-control border-danger" placeholder="Misal: RSUD Balaraja">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-danger fw-bold">Informasi Penting / Diagnosa RS</label>
                                    <textarea name="info_rs" class="form-control border-danger" rows="2" placeholder="Detail rujukan atau hasil pemeriksaan RS"></textarea>
                                </div>
                            </div>

                            {{-- Additional Pickup Fields --}}
                            <div id="extraPulangFields" class="d-none">
                                <div class="col-12 mb-3">
                                    <label class="form-label text-info fw-bold">Nama Penjemput <span class="text-danger">*</span></label>
                                    <input type="text" name="penjemput" id="input_penjemput" class="form-control border-info" placeholder="Misal: Ayah Kandung, Paman">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-info small fw-bold">Hubungan</label>
                                        <input type="text" name="hubungan_penjemput" class="form-control border-info" placeholder="Ayah, Ibu, dll">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-info small fw-bold">No. WhatsApp/Telp</label>
                                        <input type="text" name="kontak_penjemput" class="form-control border-info" placeholder="08xxx">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan / Pesan untuk Wali</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan untuk pemantauan santri..."></textarea>
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="notify_guardian" id="notify_guardian" value="1">
                        <label class="form-check-label" for="notify_guardian">Kirim Notifikasi WhatsApp ke Wali Santri</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Pemberian Obat --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Resep / Pemberian Obat</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Pilih obat yang tersedia di stok inventaris.</p>
                    
                    <div id="obatContainer">
                        <div class="row mb-3 pb-3 border-bottom obat-item">
                            <div class="col-12 mb-2">
                                <select name="obats[0][id]" class="form-select">
                                    <option value="">-- Pilih Obat --</option>
                                    @foreach($obats as $o)
                                        <option value="{{ $o->id }}">{{ $o->nama_obat }} (Sisa: {{ $o->stok }} {{ $o->satuan }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="obats[0][jumlah]" class="form-control" placeholder="Jml">
                            </div>
                            <div class="col-6">
                                <input type="text" name="obats[0][aturan]" class="form-control" placeholder="3x1, Sesudah Makan">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-info w-100 mb-3" id="btnAddObat">
                        <i class="bi bi-plus"></i> Tambah Obat Lain
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Simpan Pemeriksaan</button>
                        <a href="{{ route('kunjungan.index') }}" class="btn btn-light">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Handle Rencana Tindak Lanjut Visibility
    document.querySelectorAll('input[name="tindak_lanjut"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const sectionRawatInap = document.getElementById('sectionRawatInap');
            const sectionLuar = document.getElementById('sectionLuar');
            const extraRSFields = document.getElementById('extraRSFields');
            const extraPulangFields = document.getElementById('extraPulangFields');
            const inputNamaRs = document.getElementById('input_nama_rs');
            const inputPenjemput = document.getElementById('input_penjemput');
            
            sectionRawatInap.classList.add('d-none');
            sectionLuar.classList.add('d-none');
            extraRSFields.classList.add('d-none');
            extraPulangFields.classList.add('d-none');
            inputNamaRs.removeAttribute('required');
            inputPenjemput.removeAttribute('required');

            if (this.value === 'rawat_inap') {
                sectionRawatInap.classList.remove('d-none');
            } else if (this.value === 'rujuk_rs' || this.value === 'pulang') {
                sectionLuar.classList.remove('d-none');
                if (this.value === 'rujuk_rs') {
                    extraRSFields.classList.remove('d-none');
                    inputNamaRs.setAttribute('required', 'required');
                } else if (this.value === 'pulang') {
                    extraPulangFields.classList.remove('d-none');
                    inputPenjemput.setAttribute('required', 'required');
                }
            }
        });
    });

    let obatIndex = 1;
    document.getElementById('btnAddObat').addEventListener('click', function() {
        const container = document.getElementById('obatContainer');
        const firstItem = container.querySelector('.obat-item');
        
        // Destroy select2 if active before cloning to avoid issues
        const newItem = firstItem.cloneNode(true);
        
        newItem.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${obatIndex}]`));
            }
            input.value = '';
            // Remove select2 initialization classes if any
            input.classList.remove('select2-hidden-accessible');
            const nextSibling = input.nextSibling;
            if (nextSibling && nextSibling.classList && nextSibling.classList.contains('select2-container')) {
                nextSibling.remove();
            }
        });
        
        container.appendChild(newItem);
        
        // Re-initialize select2 for the new select
        $(newItem).find('select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        obatIndex++;
    });

    // Initialize first select2 for obat
    $(document).ready(function() {
        $('#obatContainer select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush
