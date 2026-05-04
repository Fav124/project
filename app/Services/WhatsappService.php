<?php

namespace App\Services;

use App\Models\Kunjungan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Send a medical report to the santri's primary guardians.
     */
    public function sendMedicalReport(Kunjungan $kunjungan)
    {
        $santri = $kunjungan->santri;
        $walis = $santri->waliSantris;

        if ($walis->isEmpty()) {
            Log::warning("No wali found for santri: {$santri->nama_lengkap}. WA report skipped.");
            return;
        }

        $message = $this->formatReportMessage($kunjungan);

        foreach ($walis as $wali) {
            if ($wali->no_hp) {
                $this->sendMessage($wali->no_hp, $message);
            }
        }
    }

    /**
     * Format the report to be readable and professional for parents.
     */
    private function formatReportMessage(Kunjungan $kunjungan)
    {
        $santri = $kunjungan->santri;
        $tanggal = $kunjungan->tanggal_kunjungan->format('d/m/Y H:i');
        
        $statusMap = [
            'sembuh' => '✅ Sembuh / Kembali ke Kamar',
            'rawat_inap' => '🏥 Rawat Inap di UKS',
            'dirujuk' => '🚑 Dirujuk ke Rumah Sakit',
            'pulang' => '🏠 Izin Pulang / Dijemput Wali',
        ];

        $status = $statusMap[$kunjungan->status_kunjungan] ?? $kunjungan->status_kunjungan;

        $message = "📢 *LAPORAN KESEHATAN SANTRI*\n";
        $message .= "Pondok Pesantren DEI Health\n";
        $message .= "──────────────────\n\n";
        $message .= "Nama: *{$santri->nama_lengkap}*\n";
        $message .= "Kelas: {$santri->kelas->nama_kelas}\n";
        $message .= "Waktu: {$tanggal}\n\n";
        
        $message .= "*Keluhan Utama:*\n{$kunjungan->keluhan_utama}\n\n";
        
        if ($kunjungan->diagnosa_sementara) {
            $message .= "*Diagnosa Sementara:*\n{$kunjungan->diagnosa_sementara}\n\n";
        }

        $message .= "*Tindakan:*\n{$kunjungan->tindakan}\n\n";

        if ($kunjungan->pemberianObats->isNotEmpty()) {
            $message .= "*Obat yang Diberikan:*\n";
            foreach ($kunjungan->pemberianObats as $pemberian) {
                $message .= "- {$pemberian->obat->nama_obat} ({$pemberian->aturan_pakai})\n";
            }
            $message .= "\n";
        }

        $message .= "*Status Akhir:*\n{$status}\n\n";
        
        $message .= "──────────────────\n";
        $message .= "_Laporan ini dibuat otomatis oleh Sistem Kesehatan DEI Health. Mohon doa untuk kesembuhan putra/putri Bapak/Ibu._";

        return $message;
    }

    /**
     * Core logic to send the message via WA API Provider.
     * Replace the URL and API Key as per your provider (e.g., Fonnte, Wablas, etc.)
     */
    private function sendMessage($phone, $message)
    {
        // Example integration with a generic provider
        // $apiUrl = config('services.whatsapp.url');
        // $apiKey = config('services.whatsapp.key');

        Log::info("Sending WA to {$phone}: \n{$message}");

        /*
        try {
            Http::withHeaders([
                'Authorization' => $apiKey
            ])->post($apiUrl, [
                'target' => $phone,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error("WA API Error: " . $e->getMessage());
        }
        */
    }
}
