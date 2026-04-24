<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Kesehatan - DeisaHealth</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #1a202c;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        .container {
            max-width: 100%;
        }
        
        /* Kop Surat (Letterhead) */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
        }
        .kop-surat::after {
            content: '';
            display: block;
            width: 100%;
            height: 1px;
            background: #000;
            position: absolute;
            bottom: -5px;
        }
        .logo-placeholder {
            width: 80px;
            height: 80px;
            background: #064e3b;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .kop-info {
            flex-grow: 1;
            text-align: center;
        }
        .kop-info h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-info h2 {
            margin: 2px 0;
            font-size: 18px;
            font-weight: normal;
        }
        .kop-info p {
            margin: 0;
            font-size: 12px;
            font-style: italic;
            color: #4a5568;
        }

        .report-title {
            text-align: center;
            text-decoration: underline;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        .meta-table td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .section-header {
            background: #f7fafc;
            padding: 8px 12px;
            border-left: 5px solid #064e3b;
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0 15px;
            font-size: 14px;
            color: #064e3b;
        }

        /* Summary Stats Cards for PDF */
        .summary-stats {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
        }
        .stat-card {
            display: table-cell;
            border: 1px solid #cbd5e0;
            padding: 12px;
            text-align: center;
            width: 16.66%;
            background: #fff;
        }
        .stat-card .value {
            display: block;
            font-size: 20px;
            font-weight: bold;
            color: #2d3748;
        }
        .stat-card .label {
            display: block;
            font-size: 10px;
            color: #718096;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Formal Tables */
        .formal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
        }
        .formal-table th {
            background: #edf2f7;
            border: 1px solid #a0aec0;
            padding: 8px;
            text-align: center;
            text-transform: uppercase;
        }
        .formal-table td {
            border: 1px solid #a0aec0;
            padding: 8px;
            vertical-align: top;
        }
        .formal-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Signature Area */
        .signature-container {
            margin-top: 50px;
            width: 100%;
            display: table;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
        }
        .signature-box.right {
            text-align: center;
            padding-left: 100px;
        }
        .date-location {
            margin-bottom: 10px;
        }
        .sign-line {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        .sign-title {
            font-size: 12px;
        }

        /* Print Controls */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            border: 1px solid #e2e8f0;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-family: sans-serif;
            transition: all 0.2s;
        }
        .btn-primary { background: #064e3b; color: white; }
        .btn-secondary { background: #edf2f7; color: #2d3748; margin-left: 8px; }

        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
            .kop-surat { -webkit-print-color-adjust: exact; }
            .stat-card { -webkit-print-color-adjust: exact; border: 1px solid #000; }
            .formal-table th { -webkit-print-color-adjust: exact; border: 1px solid #000; }
            .formal-table td { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">
            Cetak Laporan / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Tutup
        </button>
    </div>

    <div class="container">
        <!-- Letterhead -->
        <div class="kop-surat">
            <div class="logo-placeholder">DH</div>
            <div class="kop-info">
                <h1>Unit Kesehatan Santri (UKS)</h1>
                <h2>Pondok Pesantren Deisa Al-Ikhlas</h2>
                <p>Jl. Pendidikan No. 45, Bangkalan, Jawa Timur | Telp: (031) 1234567 | Email: uks@deisa.id</p>
            </div>
        </div>

        <div class="report-title">Laporan Bulanan Kesehatan Santri</div>

        <table class="meta-table">
            <tr>
                <td>Periode Laporan</td>
                <td>: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Petugas Pelapor</td>
                <td>: {{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td>Status Laporan</td>
                <td>: Dokumen Resmi Digital</td>
            </tr>
        </table>

        <div class="section-header">I. Ringkasan Statistik Kesehatan</div>
        <div class="summary-stats">
            <div class="stat-card">
                <span class="value">{{ $summary['total_santri'] }}</span>
                <span class="label">Populasi Santri</span>
            </div>
            <div class="stat-card">
                <span class="value">{{ $summary['santri_sakit'] }}</span>
                <span class="label">Kasus Sakit</span>
            </div>

            <div class="stat-card">
                <span class="value">{{ $summary['rujukan_rs'] }}</span>
                <span class="label">Rujukan RS</span>
            </div>
            <div class="stat-card">
                <span class="value">{{ $summary['obat_menipis'] }}</span>
                <span class="label">Stok Kritis</span>
            </div>
            <div class="stat-card">
                <span class="value">{{ $summary['kasur_tersedia'] }}</span>
                <span class="label">Fasilitas UKS</span>
            </div>
        </div>

        <div class="section-header">II. Rincian Penanganan Kasus Sakit</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="20%">Nama Santri</th>
                    <th width="25%">Keluhan</th>
                    <th width="20%">Diagnosis</th>
                    <th width="15%">Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allCases as $index => $case)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $case->visit_date->format('d/m/Y') }}</td>
                        <td><span class="font-bold">{{ $case->santri->name }}</span></td>
                        <td>{{ $case->complaint }}</td>
                        <td>{{ $case->diagnosis ?: '-' }}</td>
                        <td class="text-center">
                            @php
                                $statusLabel = match($case->status) {
                                    'observed' => 'Observasi',
                                    'handled' => 'Ditangani',
                                    'recovered' => 'Sembuh',
                                    'referred' => 'Dirujuk',
                                    default => ucfirst($case->status)
                                };
                            @endphp
                            {{ $statusLabel }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Data tidak ditemukan untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-header">III. Daftar Rujukan Rumah Sakit Eksternal</div>
        <table class="formal-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tgl Rujukan</th>
                    <th width="20%">Nama Santri</th>
                    <th width="25%">Instansi Tujuan</th>
                    <th width="35%">Indikasi Medis / Alasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $index => $ref)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $ref->referral_date->format('d/m/Y') }}</td>
                        <td><span class="font-bold">{{ $ref->santri->name }}</span></td>
                        <td>{{ $ref->hospital_name }}</td>
                        <td>{{ $ref->reason }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Tidak ada rujukan keluar pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="page-break-inside: avoid;">
            <div class="section-header">IV. Lembar Pengesahan</div>
            <div class="signature-container">
                <div class="signature-box">
                    <p>Mengetahui,</p>
                    <p style="margin-top: 5px;">Pimpinan Pondok Pesantren</p>
                    <div class="sign-line" style="margin-top: 80px;">__________________________</div>
                    <div class="sign-title">K.H. Ahmad Dahlan, M.Pd.I</div>
                </div>
                <div class="signature-box right">
                    <div class="date-location">Bangkalan, {{ now()->translatedFormat('d F Y') }}</div>
                    <p>Dibuat oleh,</p>
                    <p style="margin-top: 5px;">Kepala Unit Kesehatan</p>
                    <div class="sign-line">{{ auth()->user()->name }}</div>
                    <div class="sign-title">NIP. {{ rand(19900000, 20230000) . rand(1000, 9999) }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
