<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $type;
    protected array $obats;

    public function __construct(string $type, array $obats)
    {
        $this->type  = $type;  // 'stok_menipis' | 'hampir_kadaluarsa' | 'kadaluarsa'
        $this->obats = $obats;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $judul = match ($this->type) {
            'stok_menipis'      => '[DEI Health] Peringatan: Stok obat menipis',
            'hampir_kadaluarsa' => '[DEI Health] Peringatan: Obat hampir kadaluarsa',
            'kadaluarsa'        => '[DEI Health] Kritis: Ada obat yang sudah kadaluarsa!',
            default             => '[DEI Health] Alert stok obat',
        };

        return [
            'title'      => $judul,
            'type'       => $this->type,
            'total'      => count($this->obats),
            'obats'      => $this->obats,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
