<?php

namespace App\Http\Controllers\Concerns;

use App\Models\HospitalReferral;
use App\Models\Santri;
use App\Models\SicknessCase;
use App\Services\WhatsAppService;

trait SendsGuardianWhatsApp
{
    protected function sendSicknessCaseNotification(SicknessCase $case, WhatsAppService $whatsApp): array
    {
        $case->loadMissing(['santri', 'medicine', 'bed']);

        return $this->sendToGuardian(
            $case->santri,
            $this->buildSicknessMessage($case),
            $whatsApp
        );
    }

    protected function sendReferralNotification(HospitalReferral $referral, WhatsAppService $whatsApp): array
    {
        $referral->loadMissing('santri');

        return $this->sendToGuardian(
            $referral->santri,
            $this->buildReferralMessage($referral),
            $whatsApp
        );
    }

    private function sendToGuardian(Santri $santri, string $message, WhatsAppService $whatsApp): array
    {
        if (!$santri->guardian_phone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp wali santri belum tersedia.',
            ];
        }

        return $whatsApp->sendTextMessage($santri->guardian_phone, $message);
    }

    private function buildSicknessMessage(SicknessCase $case): string
    {
        $santri = $case->santri;
        $status = match ($case->status) {
            'observed' => 'sedang dalam observasi',
            'handled' => 'sudah ditangani',
            'recovered' => 'sudah membaik',
            'referred' => 'dirujuk untuk penanganan lanjutan',
            default => $case->status,
        };

        $lines = [
            'Assalamu\'alaikum Bapak/Ibu ' . ($santri->guardian_name ?: 'Wali Santri') . ',',
            'Kami dari Pondok Pesantren Ma\'had Dar El-Ilmi Sumatera Barat ingin menyampaikan kondisi santri:',
            'Nama: ' . $santri->name,
            'Tanggal pemeriksaan: ' . $case->visit_date->format('d-m-Y'),
            'Keluhan: ' . $case->complaint,
            'Diagnosis: ' . ($case->diagnosis ?: '-'),
            'Tindakan: ' . ($case->action_taken ?: '-'),
            'Status: ' . $status,
        ];

        if ($case->medicine) {
            $lines[] = 'Obat: ' . $case->medicine->name;
        }

        if ($case->bed) {
            $lines[] = 'Kasur UKS: ' . $case->bed->code . ' - ' . $case->bed->room_name;
        }

        if ($case->notes) {
            $lines[] = 'Catatan: ' . $case->notes;
        }

        $lines[] = 'Mohon doa dan perhatian Bapak/Ibu. Terima kasih.';

        return implode("\n", $lines);
    }

    private function buildReferralMessage(HospitalReferral $referral): string
    {
        $santri = $referral->santri;
        $status = match ($referral->status) {
            'referred' => 'dirujuk',
            'treated' => 'sedang ditangani rumah sakit',
            'returned' => 'sudah kembali',
            default => $referral->status,
        };

        $lines = [
            'Assalamu\'alaikum Bapak/Ibu ' . ($santri->guardian_name ?: 'Wali Santri') . ',',
            'Kami dari Pondok Pesantren Ma\'had Dar El-Ilmi Sumatera Barat ingin menyampaikan informasi rujukan santri:',
            'Nama: ' . $santri->name,
            'Tanggal rujukan: ' . $referral->referral_date->format('d-m-Y'),
            'Rumah sakit: ' . $referral->hospital_name,
            'Keluhan: ' . $referral->complaint,
            'Diagnosis: ' . ($referral->diagnosis ?: '-'),
            'Transportasi: ' . ($referral->transport ?: '-'),
            'Pendamping: ' . ($referral->companion_name ?: '-'),
            'Status: ' . $status,
        ];

        if ($referral->notes) {
            $lines[] = 'Catatan: ' . $referral->notes;
        }

        $lines[] = 'Mohon segera berkoordinasi jika diperlukan. Terima kasih.';

        return implode("\n", $lines);
    }
}
