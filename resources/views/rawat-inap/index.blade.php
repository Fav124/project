@extends('layouts.app')

@section('title', 'Monitoring Santri Sakit')
@section('page_title', 'Pusat Monitoring Santri Sakit')
@section('page_description', 'Memantau kondisi santri di UKS, Rumah Sakit, maupun yang sedang izin pulang.')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
@endsection

@section('content')
<div class="row">
    {{-- List Santri Sakit --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Santri Dalam Perawatan</h4>
                <div class="badge bg-primary">Total: {{ $activeCases->count() }} Santri</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Lokasi & Status</th>
                                <th>Santri</th>
                                <th>Kondisi Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeCases as $case)
                            @php $riwayat = $case->riwayatAktif; @endphp
                            <tr>
                                <td>
                                    @if($riwayat->lokasi_perawatan === 'uks')
                                        <span class="badge bg-danger">UKS</span>
                                    @elseif($riwayat->lokasi_perawatan === 'rumah_sakit')
                                        <span class="badge bg-primary"><i class="bi bi-hospital me-1"></i> RUMAH SAKIT</span>
                                        @if($riwayat->nama_rs)
                                            <div class="mt-1 small fw-bold text-primary">{{ $riwayat->nama_rs }}</div>
                                        @endif
                                    @elseif($riwayat->lokasi_perawatan === 'rumah')
                                        <span class="badge bg-info"><i class="bi bi-house me-1"></i> RUMAH (PULANG)</span>
                                        @if($riwayat->penjemput)
                                            <div class="mt-1 small fw-bold text-info">Dijemput: {{ $riwayat->penjemput }}</div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">PONDOK (PEMULIHAN)</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">Mulai: {{ $case->tanggal_mulai->translatedFormat('d M, H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $case->santri->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $case->santri->kelas?->nama_kelas }}</small>
                                </td>
                                <td>
                                    <div class="small"><strong>Diagnosa:</strong> {{ $case->diagnosa_terakhir ?: '-' }}</div>
                                    <div class="small text-muted">{{ Str::limit($riwayat->kondisi_masuk, 50) }}</div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Tombol Sembuh --}}
                                        <form action="{{ route('rawat-inap.selesai', $case->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Dinyatakan Sehat">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        {{-- Tombol Edit Info --}}
                                        <button type="button" class="btn btn-sm btn-info text-white" 
                                            onclick="modalEdit({{ $case->id }}, '{{ $case->santri->nama_lengkap }}', '{{ $riwayat->kondisi_masuk }}', '{{ $riwayat->catatan }}')" 
                                            title="Edit Info Perawatan">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Tombol Pindah Lokasi --}}
                                        <button type="button" class="btn btn-sm btn-warning text-white" 
                                            onclick="modalPindah({{ $case->id }}, '{{ $case->santri->nama_lengkap }}', '{{ $riwayat->lokasi_perawatan }}', {{ $case->santri->waliSantris->toJson() }})" 
                                            title="Pindah Lokasi">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="mb-3"><i class="bi bi-shield-check text-success fs-1"></i></div>
                                    <p class="text-muted">Alhamdulillah, tidak ada santri yang sedang sakit.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Pindah Lokasi --}}
<div class="modal fade" id="modalPindah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="#" method="POST" id="formPindah">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white">Pindah Lokasi Perawatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Santri: <strong id="namaSantriPindah"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Perawatan Baru <span class="text-danger">*</span></label>
                        <select name="lokasi" id="selectLokasi" class="form-select" onchange="toggleExtraFields(this.value)" required>
                            <option value="uks">UKS (Observasi)</option>
                            <option value="rumah_sakit">Rumah Sakit (Rujuk)</option>
                            <option value="rumah">Rumah (Izin Pulang)</option>
                            <option value="pondok">Kembali ke Pondok (Pemulihan)</option>
                        </select>
                    </div>

                    {{-- Bed Selection for UKS / Pondok --}}
                    <div id="bedFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Pilih Kasur (Opsional)</label>
                            <select name="kasur_id" class="form-select">
                                <option value="">-- Tanpa Kasur / Tetap --</option>
                                @foreach($availableBeds as $bed)
                                    <option value="{{ $bed->id }}">{{ $bed->kode_kasur }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="rsFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Nama Rumah Sakit <span class="text-danger">*</span></label>
                            <input type="text" name="nama_rs" id="nama_rs" class="form-control border-danger" placeholder="Misal: RSUD Balaraja">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Informasi Penting / Diagnosa RS</label>
                            <textarea name="info_rs" id="info_rs" class="form-control border-danger" rows="2" placeholder="Detail rujukan atau hasil pemeriksaan RS"></textarea>
                        </div>
                    </div>

                    <div id="pulangFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label text-info fw-bold">Pilih Wali Penjemput <span class="text-danger">*</span></label>
                            <select name="penjemput_select" id="penjemput_select" class="form-select border-info" onchange="updateWaliFields(this)">
                                <option value="">-- Pilih Wali --</option>
                                <option value="custom">-- Input Manual --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-info small">Nama Penjemput</label>
                            <input type="text" name="penjemput" id="penjemput" class="form-control border-info" placeholder="Nama Penjemput">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-info small">Hubungan</label>
                                <input type="text" name="hubungan_penjemput" id="hubungan_penjemput" class="form-control border-info" placeholder="Ayah, Ibu, dll">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-info small">No. WhatsApp/Telp</label>
                                <input type="text" name="kontak_penjemput" id="kontak_penjemput" class="form-control border-info" placeholder="08xxx">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Pindah</label>
                        <input type="text" name="alasan_pindah" id="alasan_pindah" class="form-control" placeholder="Misal: Perlu rujukan lanjut">
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('alasan_pindah', 'Perlu observasi lebih lanjut')">Observasi Lanjut</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('alasan_pindah', 'Orang tua meminta pulang')">Permintaan Wali</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('alasan_pindah', 'Kondisi memburuk')">Kondisi Memburuk</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('alasan_pindah', 'Perlu rujukan RS')">Rujukan RS</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('alasan_pindah', 'Pemulihan di Pondok')">Pemulihan Pondok</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kondisi Terakhir</label>
                        <textarea name="kondisi_terakhir" id="kondisi_terakhir" class="form-control" rows="2" placeholder="Detail kondisi saat ini..."></textarea>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('kondisi_terakhir', 'Demam sudah turun')">Demam Turun</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('kondisi_terakhir', 'Masih lemas')">Masih Lemas</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('kondisi_terakhir', 'Nafsu makan membaik')">Makan Membaik</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('kondisi_terakhir', 'Vital sign stabil')">Tanda Vital Stabil</span>
                            <span class="badge bg-light text-dark border pointer me-1 mb-1" onclick="addTagToField('kondisi_terakhir', 'Nyeri berkurang')">Nyeri Berkurang</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Pindahkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Info --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="#" method="POST" id="formEdit">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white">Edit Informasi Perawatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Santri: <strong id="namaSantriEdit"></strong></p>
                    


                    <div class="mb-3">
                        <label class="form-label">Update Kondisi</label>
                        <textarea name="kondisi_terakhir" id="editKondisi" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea name="catatan" id="editCatatan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pointer { cursor: pointer; }
    .pointer:hover { background-color: #e9ecef !important; }
</style>
@endpush

@push('scripts')
<script>
    function addTagToField(fieldId, text) {
        const field = document.getElementById(fieldId);
        if (field.value.trim() === '') {
            field.value = text;
        } else {
            // Check if already exists
            if (!field.value.includes(text)) {
                field.value += ', ' + text;
            }
        }
        field.focus();
    }

    let currentWalis = [];

    function modalPindah(id, nama, lokasiSekarang, walis) {
        currentWalis = walis || [];
        document.getElementById('namaSantriPindah').innerText = nama;
        document.getElementById('formPindah').action = `/rawat-inap/${id}/pindah`;
        document.getElementById('selectLokasi').value = lokasiSekarang;
        
        // Update Wali Dropdown
        const waliSelect = document.getElementById('penjemput_select');
        waliSelect.innerHTML = '<option value="">-- Pilih Wali --</option><option value="custom">-- Input Manual --</option>';
        currentWalis.forEach(w => {
            waliSelect.innerHTML += `<option value="${w.id}">${w.nama_wali} (${w.hubungan_wali})</option>`;
        });

        toggleExtraFields(lokasiSekarang);
        
        var myModal = new bootstrap.Modal(document.getElementById('modalPindah'));
        myModal.show();
    }

    function updateWaliFields(select) {
        const val = select.value;
        const inputNama = document.getElementById('penjemput');
        const inputHub = document.getElementById('hubungan_penjemput');
        const inputKontak = document.getElementById('kontak_penjemput');

        if (val === 'custom' || val === '') {
            inputNama.value = '';
            inputHub.value = '';
            inputKontak.value = '';
            if (val === 'custom') inputNama.focus();
        } else {
            const wali = currentWalis.find(w => w.id == val);
            if (wali) {
                inputNama.value = wali.nama_wali;
                inputHub.value = wali.hubungan_wali;
                inputKontak.value = wali.no_hp || '';
            }
        }
    }

    function toggleExtraFields(lokasi) {
        const rsFields = document.getElementById('rsFields');
        const pulangFields = document.getElementById('pulangFields');
        const bedFields = document.getElementById('bedFields');
        const inputRs = document.getElementById('nama_rs');
        const inputPenjemput = document.getElementById('penjemput');

        // Hide all first
        rsFields.style.display = 'none';
        pulangFields.style.display = 'none';
        bedFields.style.display = 'none';
        inputRs.removeAttribute('required');
        inputPenjemput.removeAttribute('required');

        if (lokasi === 'rumah_sakit') {
            rsFields.style.display = 'block';
            inputRs.setAttribute('required', 'required');
        } else if (lokasi === 'rumah') {
            pulangFields.style.display = 'block';
            inputPenjemput.setAttribute('required', 'required');
        } else if (lokasi === 'uks' || lokasi === 'pondok') {
            bedFields.style.display = 'block';
        }
    }

    function modalEdit(id, nama, kondisi, catatan) {
        document.getElementById('namaSantriEdit').innerText = nama;
        document.getElementById('formEdit').action = `/rawat-inap/${id}/update`;
        document.getElementById('editKondisi').value = kondisi;
        document.getElementById('editCatatan').value = catatan;
        
        var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
        myModal.show();
    }

</script>
@endpush
