<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KasusSakit;
use App\Services\MedicalCaseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Controller untuk Monitoring Santri Sakit (KasusSakit workflow).
 * Ini adalah padanan dari Web\RawatInapController yang menggunakan MedicalCaseService.
 */
class MonitoringController extends Controller
{
    protected MedicalCaseService $medicalService;

    public function __construct(MedicalCaseService $medicalService)
    {
        $this->medicalService = $medicalService;
    }

    /**
     * List semua kasus sakit aktif.
     */
    public function index(Request $request): JsonResponse
    {
        $query = KasusSakit::with(['santri.kelas', 'kunjungan', 'riwayatAktif.petugas']);

        if ($request->has('status_kasus')) {
            $query->where('status_kasus', $request->status_kasus);
        } else {
            $query->where('status_kasus', 'aktif');
        }

        $cases = $query->orderByDesc('tanggal_mulai')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $cases
        ]);
    }

    /**
     * Detail kasus sakit tertentu.
     */
    public function show(int $id): JsonResponse
    {
        $case = KasusSakit::where('id', $id)
            ->orWhere('kunjungan_id', $id)
            ->with(['santri.kelas', 'kunjungan', 'riwayats.petugas', 'riwayatAktif'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $case
        ]);
    }

    /**
     * Pindahkan santri ke lokasi baru (transisi perawatan).
     */
    public function pindah(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'lokasi' => 'required|in:uks,rumah_sakit,rumah,pondok',
            'kondisi_terakhir' => 'nullable|string',
            'alasan_pindah' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            $case = KasusSakit::where('id', $id)
                ->orWhere('kunjungan_id', $id)
                ->firstOrFail();

            $case = $this->medicalService->transition($case->id, [
                'lokasi' => $request->lokasi,
                'kondisi_keluar' => $request->kondisi_terakhir,
                'alasan_pindah' => $request->alasan_pindah,
                'catatan' => $request->catatan,
            ]);

            return response()->json([
                'message' => 'Lokasi perawatan santri berhasil diperbarui.',
                'data' => $case
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memindahkan santri.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Selesaikan kasus (santri dinyatakan sembuh).
     */
    public function selesai(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'kondisi_keluar' => 'nullable|string',
            'catatan_keluar' => 'nullable|string',
        ]);

        try {
            $case = KasusSakit::where('id', $id)
                ->orWhere('kunjungan_id', $id)
                ->first();

            if (!$case) {
                return response()->json([
                    'message' => 'Gagal menyelesaikan perawatan.',
                    'error' => "Kasus Sakit tidak ditemukan untuk ID: $id"
                ], 404);
            }

            $case = $this->medicalService->transition($case->id, [
                'status_kasus' => 'sembuh',
                'kondisi_keluar' => $request->kondisi_keluar ?: 'Sudah Sehat',
                'catatan' => $request->catatan_keluar,
            ]);

            return response()->json([
                'message' => 'Santri dinyatakan sehat dan kasus ditutup.',
                'data' => $case
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menyelesaikan perawatan.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update riwayat perawatan aktif (kondisi terakhir, catatan).
     */
    public function updateRiwayat(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'kondisi_terakhir' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        try {
            $case = KasusSakit::findOrFail($id);
            $riwayat = $case->riwayatAktif;

            if (!$riwayat) {
                return response()->json([
                    'message' => 'Tidak ada riwayat perawatan aktif untuk kasus ini.'
                ], 404);
            }

            $this->medicalService->updateHistory($riwayat->id, [
                'kondisi_masuk' => $request->kondisi_terakhir,
                'catatan' => $request->catatan,
            ]);

            return response()->json([
                'message' => 'Data perawatan berhasil diperbarui.',
                'data' => $riwayat->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data.',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
