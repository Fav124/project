<?php

namespace App\Services;

use App\Models\KasusSakit;
use App\Models\RiwayatPerawatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MedicalCaseService
{
    /**
     * Start a new medical case for a student.
     */
    public function startCase(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the main case
            $case = KasusSakit::create([
                'santri_id' => $data['santri_id'],
                'kunjungan_id' => $data['kunjungan_id'] ?? null,
                'status_kasus' => $data['status_kasus'] ?? 'aktif',
                'diagnosa_terakhir' => $data['diagnosa'] ?? null,
                'tanggal_mulai' => now(),
            ]);

            // 2. Create the first history record
            $this->createHistory($case->id, [
                'lokasi_perawatan' => $data['lokasi'],
                'nama_rs' => $data['nama_rs'] ?? null,
                'info_rs' => $data['info_rs'] ?? null,
                'penjemput' => $data['penjemput'] ?? null,
                'kontak_penjemput' => $data['kontak_penjemput'] ?? null,
                'hubungan_penjemput' => $data['hubungan_penjemput'] ?? null,
                'kondisi_masuk' => $data['kondisi'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            return $case;
        });
    }

    /**
     * Transition a case to a new location.
     */
    public function transition(int $caseId, array $data)
    {
        return DB::transaction(function () use ($caseId, $data) {
            $case = KasusSakit::findOrFail($caseId);
            $oldHistory = $case->riwayatAktif;

            // 1. Close old history
            if ($oldHistory) {
                $oldHistory->update([
                    'tanggal_keluar' => now(),
                    'kondisi_keluar' => $data['kondisi_keluar'] ?? null,
                    'alasan_pindah' => $data['alasan_pindah'] ?? null,
                ]);

                // Release the bed if it was occupied
                if ($oldHistory->kasur_id) {
                    $oldHistory->kasur->update(['status' => 'tersedia']);
                }
            }

            // 2. Update case if status changes
            if (isset($data['status_kasus'])) {
                $case->update(['status_kasus' => $data['status_kasus']]);
                if ($data['status_kasus'] !== 'aktif') {
                    $case->update(['tanggal_selesai' => now()]);
                    return $case; // No new history if case is finished
                }
            }

            // 3. Create new history
            $this->createHistory($case->id, [
                'lokasi_perawatan' => $data['lokasi'],
                'nama_rs' => $data['nama_rs'] ?? null,
                'info_rs' => $data['info_rs'] ?? null,
                'penjemput' => $data['penjemput'] ?? null,
                'kontak_penjemput' => $data['kontak_penjemput'] ?? null,
                'hubungan_penjemput' => $data['hubungan_penjemput'] ?? null,
                'kondisi_masuk' => $data['kondisi_masuk'] ?? ($data['kondisi_keluar'] ?? null),
                'catatan' => $data['catatan'] ?? null,
            ]);

            return $case;
        });
    }

    /**
     * Helper to create history and handle bed status.
     */
    private function createHistory(int $caseId, array $data)
    {
        $history = RiwayatPerawatan::create([
            'kasus_sakit_id' => $caseId,
            'lokasi_perawatan' => $data['lokasi_perawatan'],
            'nama_rs' => $data['nama_rs'] ?? null,
            'info_rs' => $data['info_rs'] ?? null,
            'penjemput' => $data['penjemput'] ?? null,
            'kontak_penjemput' => $data['kontak_penjemput'] ?? null,
            'hubungan_penjemput' => $data['hubungan_penjemput'] ?? null,
            'kasur_id' => $data['kasur_id'] ?? null,
            'tanggal_masuk' => now(),
            'kondisi_masuk' => $data['kondisi_masuk'],
            'petugas_id' => Auth::id(),
            'catatan' => $data['catatan'],
        ]);

        // If a bed is assigned, mark it as occupied
        if ($history->kasur_id) {
            $history->kasur->update(['status' => 'terisi']);
        }

        return $history;
    }

    /**
     * Update an existing history record.
     */
    public function updateHistory(int $historyId, array $data)
    {
        $history = RiwayatPerawatan::findOrFail($historyId);
        
        return DB::transaction(function () use ($history, $data) {

            $history->update($data);
            return $history;
        });
    }
}
