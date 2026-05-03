<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $rekapKunjungan = $this->laporanService->kunjunganPerPeriode($startDate, $endDate);
        $obatTerlaris = $this->laporanService->obatPalingSeringDigunakan($startDate, $endDate, 10); // Up to 10
        $statusObat = $this->laporanService->statusStokObat();
        $rawatInap = $this->laporanService->rawatInapPerPeriode($startDate, $endDate);

        $action = $request->input('action');

        if ($action === 'print') {
            $kepalaSekolah = $request->input('kepala_sekolah', \App\Models\Setting::get('kepala_sekolah', 'Nama Kepala Sekolah'));
            $nipKepalaSekolah = $request->input('nip_kepala_sekolah', \App\Models\Setting::get('nip_kepala_sekolah', '-'));
            $includeSections = $request->input('include_sections', ['kunjungan', 'obat', 'inventaris', 'rawat_inap']);
            
            $detailPasien = in_array('detail_pasien', $includeSections) ? $this->laporanService->detailPasienSakit($startDate, $endDate) : null;
            $detailObatKadaluarsa = in_array('detail_obat_kadaluarsa', $includeSections) ? $this->laporanService->detailObatBermasalah('kadaluarsa') : null;
            $detailObatHampir = in_array('detail_obat_hampir', $includeSections) ? $this->laporanService->detailObatBermasalah('hampir_kadaluarsa') : null;
            $detailObatStok = in_array('detail_obat_stok', $includeSections) ? $this->laporanService->detailObatBermasalah('stok_menipis') : null;

            return view('laporan.print', compact(
                'rekapKunjungan', 'obatTerlaris', 'statusObat', 'rawatInap', 
                'startDate', 'endDate', 'kepalaSekolah', 'nipKepalaSekolah', 'includeSections',
                'detailPasien', 'detailObatKadaluarsa', 'detailObatHampir', 'detailObatStok'
            ));
        }

        return view('laporan.index', compact('rekapKunjungan', 'obatTerlaris', 'statusObat', 'rawatInap', 'startDate', 'endDate'));
    }
}
