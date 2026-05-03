<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ApprovalPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approval;

    public function __construct($approval)
    {
        $this->approval = $approval;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'      => '[DEI Health] Ada permintaan persetujuan baru',
            'action'     => $this->approval->action,
            'model'      => class_basename($this->approval->approvable_type),
            'model_id'   => $this->approval->approvable_id,
            'status'     => $this->approval->status,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
