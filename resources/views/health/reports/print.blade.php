<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kesehatan - DeisaHealth</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; color: #1e293b; padding: 40px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 28px; color: #064e3b; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 14px; }
        
        .meta-info { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 14px; }
        .meta-info div b { display: block; color: #064e3b; margin-bottom: 4px; }

        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .summary-item { border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; text-align: center; }
        .summary-item .val { display: block; font-size: 24px; font-weight: 700; color: #064e3b; }
        .summary-item .lbl { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; }

        h2 { font-size: 18px; border-left: 4px solid #064e3b; padding-left: 10px; margin: 30px 0 15px; color: #064e3b; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8fafc; text-align: left; font-size: 12px; text-transform: uppercase; color: #64748b; padding: 12px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }

        .footer { margin-top: 50px; text-align: right; font-size: 14px; }
        .signature { margin-top: 60px; display: inline-block; border-top: 1px solid #1e293b; padding-top: 10px; min-width: 200px; text-align: center; }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .summary-item { break-inside: avoid; }
            table { break-inside: auto; }
            tr { break-inside: avoid; break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #064e3b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 700;">
            Cetak Laporan / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #e2e8f0; color: #1e293b; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>Laporan Kesehatan UKS</h1>
        <p>Aplikasi DeisaHealth - Sistem Manajemen Kesehatan Terpadu</p>
    </div>

    <div class="meta-info">
        <div>
            <b>Periode Laporan:</b>
            {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}
        </div>
        <div>
            <b>Dicetak Oleh:</b>
            {{ auth()->user()->name }}
        </div>
        <div style="text-align: right;">
            <b>Tanggal Cetak:</b>
            {{ now()->translatedFormat('d F Y H:i') }}
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-item">
            <span class="val">{{ $summary['total_santri'] }}</span>
            <span class="lbl">Total Santri</span>
        </div>
        <div class="summary-item">
            <span class="val">{{ $summary['santri_sakit'] }}</span>
            <span class="lbl">Kasus Sakit</span>
        </div>
        <div class="summary-item">
            <span class="val">{{ $summary['rekam_kesehatan'] }}</span>
            <span class="lbl">Rekam Medis</span>
        </div>
        <div class="summary-item">
            <span class="val">{{ $summary['rujukan_rs'] }}</span>
            <span class="lbl">Rujukan RS</span>
        </div>
        <div class="summary-item">
            <span class="val">{{ $summary['obat_menipis'] }}</span>
            <span class="lbl">Obat Kritis</span>
        </div>
        <div class="summary-item">
            <span class="val">{{ $summary['kasur_tersedia'] }}</span>
            <span class="lbl">Kasur Kosong</span>
        </div>
    </div>

    <h2>Rincian Kasus Sakit</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Santri</th>
                <th>Keluhan</th>
                <th>Diagnosis</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allCases as $case)
                <tr>
                    <td>{{ $case->visit_date->format('d/m/Y') }}</td>
                    <td><b>{{ $case->santri->name }}</b></td>
                    <td>{{ $case->complaint }}</td>
                    <td>{{ $case->diagnosis ?: '-' }}</td>
                    <td>{{ ucfirst($case->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">Tidak ada data kasus pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Daftar Rujukan Rumah Sakit</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Santri</th>
                <th>Rumah Sakit</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrals as $ref)
                <tr>
                    <td>{{ $ref->referral_date->format('d/m/Y') }}</td>
                    <td><b>{{ $ref->santri->name }}</b></td>
                    <td>{{ $ref->hospital_name }}</td>
                    <td>{{ $ref->reason }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">Tidak ada data rujukan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis melalui sistem DeisaHealth</p>
        <div class="signature">
            Petugas Kesehatan UKS
        </div>
    </div>

    <script>
        // Trigger print dialog automatically if needed
        // window.onload = () => window.print();
    </script>
</body>
</html>
