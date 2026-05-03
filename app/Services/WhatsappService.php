<?php

namespace App\Services;

use App\Models\Setting;

class WhatsappService
{
    /**
     * Generate a WhatsApp click-to-chat link.
     */
    public function generateLink($phoneNumber, $message)
    {
        // Format phone number (remove non-digits, replace leading 0 with 62)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        return "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
    }

    /**
     * Send a WhatsApp message via API (Placeholder for integration like Fonnte/Wablas).
     */
    public function sendMessage($phoneNumber, $message)
    {
        $apiKey = Setting::where('key', 'whatsapp_api_key')->value('value');
        
        if (!$apiKey) {
            // Fallback to log or just ignore if no API key
            \Log::info("WhatsApp Message (API Key missing): To {$phoneNumber} - {$message}");
            return false;
        }

        // Example implementation for Fonnte
        /*
        $response = \Http::withHeaders([
            'Authorization' => $apiKey,
        ])->post('https://api.fonnte.com/send', [
            'target' => $phoneNumber,
            'message' => $message,
        ]);
        return $response->successful();
        */

        return true;
    }

    /**
     * Get Admin WhatsApp number from settings.
     */
    public function getAdminNumber()
    {
        return Setting::where('key', 'admin_whatsapp')->value('value') ?? '628123456789';
    }
}
