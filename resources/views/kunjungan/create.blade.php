@extends('layouts.app')
@section('title','Pemeriksaan Baru')
@section('page_title','Form Pemeriksaan Santri')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kunjungan.index') }}">Kunjungan</a></li>
    <li class="breadcrumb-item active">Baru</li>
@endsection

@push('styles')
<style>
.tag-pool { display:flex; flex-wrap:wrap; gap:6px; max-height:160px; overflow-y:auto; padding:4px; }
.tag-chip { cursor:pointer; padding:4px 10px; border-radius:20px; font-size:.78rem; border:1px solid #dee2e6; background:#f8f9fa; transition:all .15s; user-select:none; }
.tag-chip:hover { background:#e9ecef; border-color:#adb5bd; }
.tag-chip.selected { background:#0d6efd; color:#fff; border-color:#0d6efd; }
.tag-chip.selected::after { content:' ✕'; font-size:.7rem; }
.tag-input-box { border:1.5px dashed #adb5bd; border-radius:8px; padding:10px; min-height:48px; background:#fff; }
.tag-input-box input { border:none; outline:none; font-size:.85rem; width:180px; }
.tag-search-results { position:absolute; z-index:99; background:#fff; border:1px solid #dee2e6; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,.1); width:280px; max-height:220px; overflow-y:auto; }
.tag-search-item { padding:8px 12px; cursor:pointer; font-size:.85rem; }
.tag-search-item:hover { background:#f0f4ff; }
.tag-search-item .badge { font-size:.7rem; }
.tag-selected-display { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:6px; }
.tag-selected-badge { display:inline-flex; align-items:center; gap:4px; background:#0d6efd; color:#fff; padding:3px 10px; border-radius:20px; font-size:.78rem; }
.tag-selected-badge .remove-tag { cursor:pointer; font-weight:bold; margin-left:2px; }
.category-label { font-size:.7rem; font-weight:700; color:#6c757d; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
.hint-box { background:#f0f7ff; border-left:3px solid #0d6efd; padding:8px 12px; border-radius:4px; font-size:.8rem; color:#495057; margin-bottom:12px; }
</style>
@endpush

@section('content')
<form action="{{ route('kunjungan.store') }}" method="POST" id="formPemeriksaan">
@csrf
<div class="row">

{{-- ═══════════════ KIRI ═══════════════ --}}
<div class="col-md-7">

    {{-- Pilih Santri --}}
    <div class="card mb-3">
        <div class="card-header"><h4 class="card-title mb-0"><i class="bi bi-person-fill-check me-2 text-primary"></i>Data Santri</h4></div>
        <div class="card-body">
            <label class="form-label">Pilih Santri <span class="text-danger">*</span></label>
            <select name="santri_id" class="form-select select2" required>
                <option value="">-- Cari Nama / NIS Santri --</option>
                @foreach($santris as $s)
                    <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama_lengkap }} ({{ $s->kelas?->nama_kelas }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Keluhan --}}
    <div class="card mb-3 border-warning">
        <div class="card-header bg-warning bg-opacity-10">
            <h4 class="card-title mb-0"><i class="bi bi-chat-square-text-fill me-2 text-warning"></i>Keluhan Santri</h4>
        </div>
        <div class="card-body">
            <div class="hint-box"><i class="bi bi-lightbulb me-1"></i>Klik tag keluhan di bawah, atau ketik untuk mencari. Tekan <strong>Enter</strong> untuk menambah keluhan baru.</div>
            <div id="keluhan-selected" class="tag-selected-display"></div>
            <div class="position-relative mb-3">
                <div class="tag-input-box d-flex align-items-center gap-2">
                    <i class="bi bi-search text-muted small"></i>
                    <input type="text" id="keluhan-search" placeholder="Cari atau tulis keluhan..." autocomplete="off">
                </div>
                <div id="keluhan-results" class="tag-search-results d-none"></div>
            </div>
            <div class="hint-box bg-light border-start border-secondary"><small class="text-muted"><i class="bi bi-hand-index me-1"></i>Klik cepat dari daftar umum:</small></div>
            <div class="tag-pool" id="keluhan-pool">
                @foreach($keluhanGroups as $cat => $items)
                    <div class="w-100"><span class="category-label">{{ $cat }}</span></div>
                    @foreach($items as $k)
                        <span class="tag-chip" data-type="keluhan" data-id="{{ $k->id }}" data-name="{{ $k->nama }}">{{ $k->nama }}</span>
                    @endforeach
                @endforeach
            </div>
            <div class="mt-3">
                <label class="form-label fw-bold">Keluhan Utama <span class="text-danger">*</span> <small class="text-muted fw-normal">(Ringkasan singkat)</small></label>
                <textarea name="keluhan_utama" class="form-control" rows="2" required placeholder="Contoh: Demam 2 hari disertai batuk dan pilek"></textarea>
            </div>
            <div class="mt-3">
                <label class="form-label">Riwayat Keluhan / Anamnesis</label>
                <textarea name="anamnesis" class="form-control" rows="2" placeholder="Detail keluhan: sejak kapan, membaik/memburuk saat apa, faktor pencetus, dll."></textarea>
            </div>
        </div>
    </div>

    {{-- Pemeriksaan Fisik --}}
    <div class="card mb-3">
        <div class="card-header"><h4 class="card-title mb-0"><i class="bi bi-heart-pulse-fill me-2 text-danger"></i>Pemeriksaan Fisik</h4></div>
        <div class="card-body">
            <div class="hint-box"><i class="bi bi-info-circle me-1"></i>Isi tanda vital sesuai hasil pemeriksaan. Kosongkan jika tidak diperiksa.</div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Suhu (°C)</label>
                    <input type="number" name="suhu" class="form-control" step="0.1" placeholder="37.0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tekanan Darah</label>
                    <input type="text" name="tekanan_darah" class="form-control" placeholder="120/80">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nadi (bpm)</label>
                    <input type="number" name="denyut_nadi" class="form-control" placeholder="80">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pernapasan (x/mnt)</label>
                    <input type="number" name="pernapasan" class="form-control" placeholder="20">
                </div>
            </div>
        </div>
    </div>

    {{-- Diagnosa --}}
    <div class="card mb-3 border-danger">
        <div class="card-header bg-danger bg-opacity-10">
            <h4 class="card-title mb-0"><i class="bi bi-clipboard2-pulse-fill me-2 text-danger"></i>Diagnosa Sementara</h4>
        </div>
        <div class="card-body">
            <div class="hint-box"><i class="bi bi-lightbulb me-1"></i>Pilih dari database diagnosa (ICD-10), atau ketik diagnosa baru dan tekan <strong>Enter</strong> — akan otomatis tersimpan.</div>
            <div id="diagnosa-selected" class="tag-selected-display"></div>
            <div class="position-relative mb-3">
                <div class="tag-input-box d-flex align-items-center gap-2">
                    <i class="bi bi-search text-muted small"></i>
                    <input type="text" id="diagnosa-search" placeholder="Cari kode ICD atau nama diagnosa..." autocomplete="off">
                </div>
                <div id="diagnosa-results" class="tag-search-results d-none"></div>
            </div>
            <div class="tag-pool" id="diagnosa-pool">
                @foreach($diagnosaGroups as $cat => $items)
                    <div class="w-100"><span class="category-label">{{ $cat }}</span></div>
                    @foreach($items as $d)
                        <span class="tag-chip" data-type="diagnosa" data-id="{{ $d->id }}" data-name="{{ $d->nama }}">
                            @if($d->kode)<small class="text-muted">[{{ $d->kode }}]</small> @endif{{ $d->nama }}
                        </span>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tindakan --}}
    <div class="card mb-3 border-info">
        <div class="card-header bg-info bg-opacity-10">
            <h4 class="card-title mb-0"><i class="bi bi-bandaid-fill me-2 text-info"></i>Tindakan Medis</h4>
        </div>
        <div class="card-body">
            <div class="hint-box"><i class="bi bi-lightbulb me-1"></i>Pilih tindakan yang dilakukan. Tekan <strong>Enter</strong> untuk menambah tindakan baru ke database.</div>
            <div id="tindakan-selected" class="tag-selected-display"></div>
            <div class="position-relative mb-3">
                <div class="tag-input-box d-flex align-items-center gap-2">
                    <i class="bi bi-search text-muted small"></i>
                    <input type="text" id="tindakan-search" placeholder="Cari atau tulis tindakan medis..." autocomplete="off">
                </div>
                <div id="tindakan-results" class="tag-search-results d-none"></div>
            </div>
            <div class="tag-pool" id="tindakan-pool">
                @foreach($tindakanGroups as $cat => $items)
                    <div class="w-100"><span class="category-label">{{ $cat }}</span></div>
                    @foreach($items as $t)
                        <span class="tag-chip" data-type="tindakan" data-id="{{ $t->id }}" data-name="{{ $t->nama }}">{{ $t->nama }}</span>
                    @endforeach
                @endforeach
            </div>
            <div class="mt-3">
                <label class="form-label">Catatan Tindakan Tambahan</label>
                <textarea name="catatan_tindakan" class="form-control" rows="2" placeholder="Catatan khusus tindakan yang tidak ada di daftar, instruksi perawat, dll."></textarea>
            </div>
        </div>
    </div>

    {{-- Tindak Lanjut --}}
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary py-2"><h5 class="card-title text-white mb-0">Rencana Tindak Lanjut</h5></div>
        <div class="card-body mt-3">
            <div class="d-flex flex-wrap gap-3 mb-3">
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

            <div id="sectionRawatInap" class="d-none border-top pt-3">
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
                        <input type="text" name="kondisi_masuk" class="form-control" placeholder="Lemas, Pucat, Sadar">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estimasi Kembali ke Pondok</label>
                        <input type="datetime-local" name="estimasi_kembali" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Alasan Rawat / Observasi</label>
                        <input type="text" name="alasan_rawat" class="form-control" placeholder="Demam tinggi >39°C, perlu pemantauan ketat">
                    </div>
                </div>
            </div>

            <div id="sectionLuar" class="d-none border-top pt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estimasi Kembali ke Pondok <small class="text-muted">(Opsional)</small></label>
                        <input type="datetime-local" name="estimasi_kembali_luar" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Alasan / Catatan Izin Keluar</label>
                        <input type="text" name="alasan_luar" class="form-control" placeholder="Dirujuk ke RSUD, Dijemput Orang Tua">
                    </div>
                    <div id="extraRSFields" class="d-none col-12">
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Nama Rumah Sakit <span class="text-danger">*</span></label>
                            <input type="text" name="nama_rs" id="input_nama_rs" class="form-control border-danger" placeholder="RSUD Balaraja">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Informasi Penting / Diagnosa RS</label>
                            <textarea name="info_rs" class="form-control border-danger" rows="2" placeholder="Detail rujukan atau hasil pemeriksaan RS"></textarea>
                        </div>
                    </div>
                    <div id="extraPulangFields" class="d-none col-12">
                        <div class="mb-3">
                            <label class="form-label text-info fw-bold">Nama Penjemput <span class="text-danger">*</span></label>
                            <input type="text" name="penjemput" id="input_penjemput" class="form-control border-info" placeholder="Ayah Kandung, Paman">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Hubungan</label>
                                <input type="text" name="hubungan_penjemput" class="form-control" placeholder="Ayah, Ibu, dll">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">No. WA/Telp</label>
                                <input type="text" name="kontak_penjemput" class="form-control" placeholder="08xxx">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2">
                <label class="form-label">Catatan Tambahan / Pesan untuk Wali</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pemantauan, instruksi makan, pantangan, dll."></textarea>
            </div>
            <div class="form-check form-switch mt-3">
                <input class="form-check-input" type="checkbox" name="notify_guardian" id="notify_guardian" value="1">
                <label class="form-check-label" for="notify_guardian">Kirim Notifikasi WhatsApp ke Wali Santri</label>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ KANAN ═══════════════ --}}
<div class="col-md-5">
    <div class="card mb-3">
        <div class="card-header"><h4 class="card-title mb-0"><i class="bi bi-capsule-pill me-2 text-success"></i>Resep / Pemberian Obat</h4></div>
        <div class="card-body">
            <p class="text-muted small">Pilih obat dari stok inventaris yang tersedia.</p>
            <div id="obatContainer">
                <div class="row mb-3 pb-3 border-bottom obat-item">
                    <div class="col-12 mb-2">
                        <select name="obats[0][id]" class="form-select obat-select">
                            <option value="">-- Pilih Obat --</option>
                            @foreach($obats as $o)
                                <option value="{{ $o->id }}">{{ $o->nama_obat }} (Sisa: {{ $o->stok }} {{ $o->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6"><input type="number" name="obats[0][jumlah]" class="form-control" placeholder="Jumlah"></div>
                    <div class="col-6"><input type="text" name="obats[0][aturan]" class="form-control" placeholder="3x1, Sesudah Makan"></div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-info w-100" id="btnAddObat"><i class="bi bi-plus"></i> Tambah Obat Lain</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check2-circle me-2"></i>Simpan Pemeriksaan</button>
                <a href="{{ route('kunjungan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </div>
    </div>
</div>

</div>{{-- /row --}}
</form>

{{-- Hidden inputs container for tag IDs --}}
<div id="tag-hidden-inputs"></div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Tag Manager ──────────────────────────────────────────────
class TagManager {
    constructor(type, searchInput, resultsBox, selectedDisplay, pool) {
        this.type = type;
        this.selected = {}; // id -> name
        this.searchEl = document.getElementById(searchInput);
        this.resultsEl = document.getElementById(resultsBox);
        this.displayEl = document.getElementById(selectedDisplay);
        this.pool = document.getElementById(pool);
        this.searchTimeout = null;

        this.searchEl.addEventListener('input', () => this.onSearch());
        this.searchEl.addEventListener('keydown', (e) => this.onKeydown(e));
        this.searchEl.addEventListener('blur', () => setTimeout(() => this.resultsEl.classList.add('d-none'), 200));

        if (this.pool) {
            this.pool.querySelectorAll('.tag-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    this.toggle(chip.dataset.id, chip.dataset.name, chip);
                });
            });
        }
    }

    toggle(id, name, chipEl) {
        if (this.selected[id]) {
            delete this.selected[id];
            if (chipEl) chipEl.classList.remove('selected');
        } else {
            this.selected[id] = name;
            if (chipEl) chipEl.classList.add('selected');
        }
        this.render();
    }

    add(id, name) {
        this.selected[id] = name;
        const chip = this.pool?.querySelector(`[data-id="${id}"]`);
        if (chip) chip.classList.add('selected');
        this.render();
        this.searchEl.value = '';
        this.resultsEl.classList.add('d-none');
    }

    render() {
        this.displayEl.innerHTML = Object.entries(this.selected).map(([id, name]) =>
            `<span class="tag-selected-badge" data-id="${id}">
                ${name} <span class="remove-tag" onclick="managers['${this.type}'].remove('${id}')">✕</span>
            </span>`
        ).join('');

        // Update hidden inputs
        document.querySelectorAll(`input[name="${this.type}_ids[]"]`).forEach(e => e.remove());
        const container = document.getElementById('tag-hidden-inputs');
        Object.keys(this.selected).forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = `${this.type}_ids[]`;
            inp.value = id;
            container.appendChild(inp);
        });
    }

    remove(id) {
        delete this.selected[id];
        const chip = this.pool?.querySelector(`[data-id="${id}"]`);
        if (chip) chip.classList.remove('selected');
        this.render();
    }

    onSearch() {
        clearTimeout(this.searchTimeout);
        const q = this.searchEl.value.trim();
        if (!q) { this.resultsEl.classList.add('d-none'); return; }
        this.searchTimeout = setTimeout(() => this.doSearch(q), 250);
    }

    async doSearch(q) {
        const res = await fetch(`/api/medical-tags/${this.type}/search?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        this.renderResults(data, q);
    }

    renderResults(items, q) {
        let html = items.map(item =>
            `<div class="tag-search-item" onclick="managers['${this.type}'].add('${item.id}','${item.nama.replace(/'/g,"\\'")}')">
                ${item.kode ? `<span class="badge bg-secondary me-1">${item.kode}</span>` : ''}
                ${item.nama}
                ${item.kategori ? `<br><small class="text-muted">${item.kategori}</small>` : ''}
            </div>`
        ).join('');

        if (!items.length) {
            html = `<div class="tag-search-item text-muted"><i class="bi bi-plus-circle me-1"></i>Tekan Enter untuk menambah "<strong>${q}</strong>"</div>`;
        }
        this.resultsEl.innerHTML = html;
        this.resultsEl.classList.remove('d-none');
    }

    async onKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.searchEl.value.trim();
            if (!q) return;
            // POST to create new tag
            const res = await fetch(`/api/medical-tags/${this.type}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ nama: q })
            });
            const data = await res.json();
            this.add(data.id, data.nama);
        }
    }
}

const managers = {
    keluhan:  new TagManager('keluhan',  'keluhan-search',  'keluhan-results',  'keluhan-selected',  'keluhan-pool'),
    diagnosa: new TagManager('diagnosa', 'diagnosa-search', 'diagnosa-results', 'diagnosa-selected', 'diagnosa-pool'),
    tindakan: new TagManager('tindakan', 'tindakan-search', 'tindakan-results', 'tindakan-selected', 'tindakan-pool'),
};

// ── Tindak Lanjut Visibility ─────────────────────────────────
document.querySelectorAll('input[name="tindak_lanjut"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('sectionRawatInap').classList.add('d-none');
        document.getElementById('sectionLuar').classList.add('d-none');
        document.getElementById('extraRSFields').classList.add('d-none');
        document.getElementById('extraPulangFields').classList.add('d-none');
        document.getElementById('input_nama_rs')?.removeAttribute('required');
        document.getElementById('input_penjemput')?.removeAttribute('required');

        if (this.value === 'rawat_inap') {
            document.getElementById('sectionRawatInap').classList.remove('d-none');
        } else if (this.value === 'rujuk_rs') {
            document.getElementById('sectionLuar').classList.remove('d-none');
            document.getElementById('extraRSFields').classList.remove('d-none');
            document.getElementById('input_nama_rs')?.setAttribute('required','required');
        } else if (this.value === 'pulang') {
            document.getElementById('sectionLuar').classList.remove('d-none');
            document.getElementById('extraPulangFields').classList.remove('d-none');
            document.getElementById('input_penjemput')?.setAttribute('required','required');
        }
    });
});

// ── Tambah Baris Obat ────────────────────────────────────────
let obatIndex = 1;
document.getElementById('btnAddObat').addEventListener('click', function() {
    const container = document.getElementById('obatContainer');
    const first = container.querySelector('.obat-item');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('input,select').forEach(el => {
        el.name = el.name?.replace(/\[\d+\]/, `[${obatIndex}]`);
        el.value = '';
    });
    container.appendChild(clone);
    obatIndex++;
});
</script>
@endpush
