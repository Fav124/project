<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    protected LaporanService $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    /**
     * Export Rekap Kunjungan ke CSV
     */
    public function exportKunjunganCsv(Request $request): StreamedResponse
    {
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth();
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfMonth();

        $data = $this->laporanService->kunjunganPerPeriode($start, $end, 'date');

        $fileName = 'rekap_kunjungan_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Periode Tanggal', 'Total Kunjungan']);

            foreach ($data as $row) {
                fputcsv($file, [$row->periode, $row->total]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    // Untuk Export PDF, kita mock return JSON yang menginformasikan Endpoint siap diintegrasi dengan library seperti DomPDF
    public function exportKunjunganPdf(Request $request)
    {
        return response()->json([
            'message' => 'Endpoint siap. Silakan install barryvdh/laravel-dompdf untuk merender HTML ini ke PDF.',
            'view_data' => 'Data dari LaporanService->kunjunganPerPeriode() dilempar ke Blade view lalu diekspor.'
        ]);
    }
}
