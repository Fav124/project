<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan UKS - {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/main/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main/app-dark.css') }}">
    <style>
        @media print {
            @page { 
                margin: 0; /* This helps remove browser headers/footers */
            }
            body { 
                margin: 1.5cm; /* Content margin */
                background-color: white !important; 
                color: black !important; 
                -webkit-print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            .card { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
            .container { width: 100% !important; max-width: none !important; padding: 0 !important; margin: 0 !important; }
        }
        body { font-family: 'Times New Roman', Times, serif; }
        .report-header { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .school-logo { width: 80px; height: auto; }
        .report-title { text-align: center; margin-bottom: 30px; }
        .signature-row { margin-top: 50px; }
        .signature-box { width: 250px; text-align: center; }
        .page-break { page-break-after: always; }
        table { font-size: 11px !important; width: 100% !important; border-collapse: collapse; }
        th { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
        .border-primary { border-color: #435ebe !important; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.parent.closePrintIframe(); }">
    <div class="container py-2">

        <div class="card p-5 shadow-sm bg-white border-0">
            {{-- Kop Surat --}}
            <div class="report-header d-flex align-items-center">
                @php $logo = \App\Models\Setting::get('institution_logo'); @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" class="school-logo me-4" alt="Logo">
                @else
                    <img src="{{ asset('assets/images/logo/dei.png') }}" class="school-logo me-4" alt="Logo">
                @endif
                <div class="text-center flex-grow-1">
                    <h2 class="mb-0 fw-bold text-uppercase">{{ \App\Models\Setting::get('institution_name', 'PONDOK PESANTREN DAR EL-ILMI') }}</h2>
                    <p class="mb-0">Unit Kesehatan Sekolah (UKS) & Layanan Medis Santri</p>
                    <p class="small mb-0">{{ \App\Models\Setting::get('institution_address', 'Jl. Pembangunan No. 123, Kota Bandung, Jawa Barat') }}</p>
                </div>
            </div>

            <div class="report-title">
                <h4 class="text-uppercase fw-bold">Laporan Operasional UKS</h4>
                <p>Periode: <strong>{{ $startDate->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $endDate->translatedFormat('d F Y') }}</strong></p>
            </div>

            {{-- 1. Rekap Kunjungan --}}
            @if(in_array('kunjungan', $includeSections))
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-primary ps-2 mb-3">I. Rekapitulasi Kunjungan Pasien</h5>
                <p class="text-muted small">Total kunjungan tercatat: {{ $rekapKunjungan->sum('total') }} pasien.</p>
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th width="50px">No</th>
                            <th>Periode (Tanggal/Bulan)</th>
                            <th class="text-center">Jumlah Kunjungan</th>
                            <th class="text-center">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = $rekapKunjungan->sum('total'); @endphp
                        @foreach($rekapKunjungan as $index => $kunjungan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kunjungan->periode }}</td>
                            <td class="text-center">{{ $kunjungan->total }}</td>
                            <td class="text-center">{{ $total > 0 ? number_format(($kunjungan->total / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- DETAIL PASIEN --}}
            @if(in_array('detail_pasien', $includeSections) && $detailPasien)
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-success ps-2 mb-3">II. Daftar Detail Pasien & Penanganan</h5>
                <table class="table table-bordered table-sm" style="font-size: 10px !important;">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Tgl Kunjungan</th>
                            <th>Nama Santri (NIS)</th>
                            <th>Kelas</th>
                            <th>Keluhan</th>
                            <th>Diagnosa</th>
                            <th>Penanganan / Obat</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailPasien as $p)
                        <tr>
                            <td class="text-center">{{ $p->tanggal_kunjungan->format('d/m/y H:i') }}</td>
                            <td><strong>{{ $p->santri->nama_lengkap }}</strong><br><small class="text-muted">{{ $p->santri->nis }}</small></td>
                            <td class="text-center">{{ $p->santri->kelas->nama_kelas }}</td>
                            <td>{{ $p->keluhan_utama }}</td>
                            <td>{{ $p->diagnosa_sementara }}</td>
                            <td>
                                <div><strong>Tindakan:</strong> {{ $p->tindakan ?? '-' }}</div>
                                <div class="mt-1">
                                    <strong>Obat:</strong>
                                    @forelse($p->pemberianObats as $po)
                                        <span class="badge bg-light text-dark border">{{ $po->obat->nama_obat }} ({{ $po->jumlah }})</span>
                                    @empty
                                        -
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-center">{{ $p->petugas->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- 2. Penggunaan Obat --}}
            @if(in_array('obat', $includeSections))
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-primary ps-2 mb-3">III. Statistik Penggunaan Obat Terbanyak</h5>
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th width="50px">No</th>
                            <th>Nama Obat</th>
                            <th class="text-center">Frekuensi Diberikan</th>
                            <th class="text-center">Total Volume Keluar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($obatTerlaris as $index => $obat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $obat->obat->nama_obat }}</td>
                            <td class="text-center">{{ $obat->frekuensi_diberikan }} kali</td>
                            <td class="text-center fw-bold">{{ (int)$obat->total_keluar }} {{ $obat->obat->satuan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- DETAIL OBAT KADALUARSA --}}
            @if(in_array('detail_obat_kadaluarsa', $includeSections) && $detailObatKadaluarsa->count() > 0)
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-danger ps-2 mb-3 text-danger">IV. Daftar Obat KADALUARSA (KRITIS)</h5>
                <table class="table table-bordered table-sm">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Tgl Kadaluarsa</th>
                            <th>Stok Sisa</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailObatKadaluarsa as $o)
                        <tr class="table-danger">
                            <td>{{ $o->kode_obat }}</td>
                            <td><strong>{{ $o->nama_obat }}</strong></td>
                            <td>{{ $o->tanggal_kadaluarsa->format('d/m/Y') }}</td>
                            <td>{{ $o->stok }}</td>
                            <td>{{ $o->lokasi_penyimpanan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- DETAIL OBAT HAMPIR --}}
            @if(in_array('detail_obat_hampir', $includeSections) && $detailObatHampir->count() > 0)
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-warning ps-2 mb-3">V. Daftar Obat Hampir Kadaluarsa</h5>
                <table class="table table-bordered table-sm">
                    <thead class="bg-warning">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Obat</th>
                            <th>Tgl Kadaluarsa</th>
                            <th>Stok Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailObatHampir as $o)
                        <tr>
                            <td>{{ $o->kode_obat }}</td>
                            <td>{{ $o->nama_obat }}</td>
                            <td>{{ $o->tanggal_kadaluarsa->format('d/m/Y') }}</td>
                            <td>{{ $o->stok }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- DETAIL STOK MENIPIS --}}
            @if(in_array('detail_obat_stok', $includeSections) && $detailObatStok->count() > 0)
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-dark ps-2 mb-3">VI. Daftar Obat Stok Menipis / Habis</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nama Obat</th>
                            <th>Stok Saat Ini</th>
                            <th>Min. Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailObatStok as $o)
                        <tr>
                            <td>{{ $o->nama_obat }}</td>
                            <td class="fw-bold {{ $o->stok <= 0 ? 'text-danger' : 'text-warning' }}">{{ $o->stok }}</td>
                            <td>{{ $o->stok_minimum }}</td>
                            <td>{{ $o->stok <= 0 ? 'HABIS' : 'MENIPIS' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- 3. Inventaris Ringkasan --}}
            @if(in_array('inventaris', $includeSections))
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-primary ps-2 mb-3">VII. Ringkasan Statistik Inventaris</h5>
                <div class="row">
                    <div class="col-4">
                        <div class="p-3 border rounded text-center">
                            <h6 class="text-muted small">Kadaluarsa</h6>
                            <h4 class="text-danger mb-0">{{ $statusObat['kadaluarsa'] }}</h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded text-center">
                            <h6 class="text-muted small">Stok Menipis</h6>
                            <h4 class="text-warning mb-0">{{ $statusObat['stok_menipis'] }}</h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 border rounded text-center">
                            <h6 class="text-muted small">Stok Habis</h6>
                            <h4 class="text-dark mb-0">{{ $statusObat['stok_habis'] }}</h4>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- 4. Rawat Inap --}}
            @if(in_array('rawat_inap', $includeSections))
            <section class="mb-5">
                <h5 class="fw-bold border-start border-4 border-primary ps-2 mb-3">IV. Ringkasan Rawat Inap & Observasi</h5>
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Status Terakhir</th>
                            <th class="text-center">Jumlah Pasien</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rawatInap as $ri)
                        <tr>
                            <td class="text-capitalize">{{ $ri->status_rawat }}</td>
                            <td class="text-center fw-bold">{{ $ri->total }}</td>
                            <td>
                                @if($ri->status_rawat == 'aktif')
                                    Pasien masih dalam observasi di UKS
                                @else
                                    Pasien telah kembali ke asrama / pulang
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
            @endif

            {{-- Tanda Tangan --}}
            <div class="signature-row d-flex justify-content-between">
                <div class="signature-box">
                    <p>Mengetahui,</p>
                    <p class="fw-bold mb-5">Kepala UKS / Penanggung Jawab</p>
                    <br>
                    <p class="mb-0 underline">__________________________</p>
                    <p class="small text-muted">Petugas UKS</p>
                </div>
                <div class="signature-box">
                    <p>Bandung, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="fw-bold mb-5">Kepala Sekolah / Mudir</p>
                    <br>
                    <p class="mb-0 fw-bold">{{ $kepalaSekolah }}</p>
                    <p class="small text-muted">NIP. {{ $nipKepalaSekolah }}</p>
                </div>
            </div>

            <div class="mt-5 text-center small text-muted border-top pt-2">
                Laporan ini dibuat secara otomatis oleh Sistem Manajemen DEI Health pada {{ now()->format('d/m/Y H:i') }}.
            </div>
        </div>
    </div>
</body>
</html>
