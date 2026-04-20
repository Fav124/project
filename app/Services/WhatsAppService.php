<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function isConfigured(): bool
    {
        return filled(config('services.whatsapp.token'))
            && filled(config('services.whatsapp.phone_number_id'));
    }

    public function sendTextMessage(string $phone, string $message): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi WhatsApp API belum lengkap.',
            ];
        }

        $response = Http::withToken(config('services.whatsapp.token'))
            ->post($this->endpoint(), [
                'messaging_product' => 'whatsapp',
                'to' => $this->normalizePhone($phone),
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message,
                ],
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Notifikasi WhatsApp berhasil dikirim.',
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'message' => data_get($response->json(), 'error.message', 'Gagal mengirim notifikasi WhatsApp.'),
            'data' => $response->json(),
        ];
    }

    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . ltrim($phone, '0');
        }

        return $phone;
    }

    private function endpoint(): string
    {
        return sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            config('services.whatsapp.version', 'v23.0'),
            config('services.whatsapp.phone_number_id')
        );
    }
}
