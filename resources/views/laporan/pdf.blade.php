<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan UKS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0 0;
        }
        .section-title {
            background-color: #f2f2f2;
            padding: 5px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        .signature {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN OPERASIONAL KESEHATAN (UKS)</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Rekapitulasi Kunjungan</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-center">Total Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapKunjungan as $kunjungan)
            <tr>
                <td>{{ \Carbon\Carbon::parse($kunjungan->periode)->format('d F Y') }}</td>
                <td class="text-center">{{ $kunjungan->total }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">Tidak ada data kunjungan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Obat Paling Sering Digunakan (Top 5)</div>
    <table>
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th class="text-center">Frekuensi Pemberian</th>
                <th class="text-center">Total Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($obatTerlaris as $obat)
            <tr>
                <td>{{ $obat->obat->nama_obat }}</td>
                <td class="text-center">{{ $obat->frekuensi_diberikan }} kali</td>
                <td class="text-center">{{ (int)$obat->total_keluar }} {{ $obat->obat->satuan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data pengeluaran obat pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Status Inventaris Obat</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-center">Jumlah Item</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kadaluarsa</td>
                <td class="text-center">{{ $statusObat['kadaluarsa'] }}</td>
            </tr>
            <tr>
                <td>Stok Menipis</td>
                <td class="text-center">{{ $statusObat['stok_menipis'] }}</td>
            </tr>
            <tr>
                <td>Stok Habis</td>
                <td class="text-center">{{ $statusObat['stok_habis'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Rekapitulasi Rawat Inap</div>
    <table>
        <thead>
            <tr>
                <th>Status Rawat Inap</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rawatInap as $ri)
            <tr>
                <td>{{ strtoupper($ri->status_rawat) }}</td>
                <td class="text-center">{{ $ri->total }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">Tidak ada data rawat inap pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <p>Tangerang, {{ now()->format('d F Y') }}</p>
        <p>Kepala Sekolah</p>
        <div class="signature-space"></div>
        <p style="font-weight: bold; text-decoration: underline;">{{ $kepalaSekolah }}</p>
        <p>NIP. {{ $nipKepalaSekolah }}</p>
    </div>
</body>
</html>
