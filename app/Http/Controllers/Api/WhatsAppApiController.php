<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HospitalReferral;
use App\Models\SicknessCase;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class WhatsAppApiController extends Controller
{
    public function notifySicknessCase($id, WhatsAppService $whatsApp)
    {
        $case = SicknessCase::with('santri')->findOrFail($id);
        $santri = $case->santri;

        if (!$santri?->guardian_phone) {
            return response()->json(['success' => false, 'message' => 'Nomor wali tidak tersedia.'], 422);
        }

        $statusLabel = match ($case->status) {
            'observed'  => 'Sedang Diobservasi',
            'handled'   => 'Sedang Ditangani',
            'recovered' => 'Alhamdulillah, Telah Sembuh',
            'referred'  => 'Dirujuk ke RS',
            default     => ucfirst($case->status),
        };

        $message = "Assalamualaikum Bapak/Ibu *{$santri->guardian_name}*,\n\n"
            . "Kami dari UKS Pondok Pesantren memberitahukan informasi terkini mengenai putra/putri Anda:\n\n"
            . "👤 *Santri:* {$santri->name}\n"
            . "📋 *Keluhan:* {$case->complaint}\n"
            . ($case->diagnosis ? "🩺 *Diagnosa:* {$case->diagnosis}\n" : "")
            . "🏥 *Status:* {$statusLabel}\n"
            . "📅 *Tanggal Kunjungan:* " . Carbon::parse($case->visit_date)->format('d M Y') . "\n\n"
            . "Jika ada pertanyaan, silakan hubungi petugas UKS kami.\n"
            . "Jazakumullahu khairan.";

        $result = $whatsApp->sendTextMessage($santri->guardian_phone, $message);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 500);
    }

    public function notifyReferral($id, WhatsAppService $whatsApp)
    {
        $referral = HospitalReferral::with('santri')->findOrFail($id);
        $santri = $referral->santri;

        if (!$santri?->guardian_phone) {
            return response()->json(['success' => false, 'message' => 'Nomor wali tidak tersedia.'], 422);
        }

        $message = "Assalamualaikum Bapak/Ibu *{$santri->guardian_name}*,\n\n"
            . "Kami memberitahukan bahwa santri *{$santri->name}* telah mendapat rujukan medis:\n\n"
            . "🏥 *Rumah Sakit:* {$referral->hospital_name}\n"
            . "📋 *Keluhan:* {$referral->complaint}\n"
            . ($referral->diagnosis ? "🩺 *Diagnosa:* {$referral->diagnosis}\n" : "")
            . "📅 *Tanggal Rujukan:* " . Carbon::parse($referral->referral_date)->format('d M Y') . "\n"
            . ($referral->companion_name ? "👤 *Pendamping:* {$referral->companion_name}\n" : "")
            . ($referral->transport ? "🚗 *Kendaraan:* {$referral->transport}\n" : "")
            . "\nMohon doanya semoga diberikan kesembuhan yang segera.\n"
            . "Jazakumullahu khairan.";

        $result = $whatsApp->sendTextMessage($santri->guardian_phone, $message);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 500);
    }
}
