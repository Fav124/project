<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InpatientAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $rawatInaps;

    public function __construct(array $rawatInaps)
    {
        $this->rawatInaps = $rawatInaps;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'      => '[DEI Health] Peringatan: Ada santri yang sudah lama dirawat inap',
            'total'      => count($this->rawatInaps),
            'santri_ids' => $this->rawatInaps,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
