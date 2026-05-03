<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\User;
use App\Notifications\ApprovalPendingNotification;
use Illuminate\Support\Facades\Auth;

class ApprovalService
{
    /**
     * Submit data to approval queue.
     */
    public function submit(string $approvableType, int $approvableId, string $action, ?array $payload = null, ?string $notes = null): ApprovalRequest
    {
        $approval = ApprovalRequest::create([
            'approvable_type' => $approvableType,
            'approvable_id'   => $approvableId,
            'action'          => $action,
            'payload'         => $payload,
            'status'          => 'pending',
            'requested_by'    => Auth::id(),
            'notes'           => $notes,
        ]);

        // Notify all admins & super_admins
        User::whereIn('role', ['admin', 'super_admin'])
            ->cursor()
            ->each(fn($u) => $u->notify(new ApprovalPendingNotification($approval)));

        return $approval;
    }

    /**
     * Approve a request.
     */
    public function approve(ApprovalRequest $approval, ?string $notes = null): ApprovalRequest
    {
        $approval->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'notes'       => $notes ?: $approval->notes,
            'decided_at'  => now(),
        ]);

        // Execute action based on action type
        if ($approval->action === 'register_user') {
            $user = User::find($approval->approvable_id);
            if ($user) {
                $role = $approval->payload['role'] ?? 'petugas_kesehatan';
                $user->update([
                    'is_approved' => true,
                    'role' => $role
                ]);
            }
        } elseif ($approval->action === 'update' && $approval->payload) {
            $model = $approval->approvable;
            if ($model) {
                $model->update($approval->payload);
            }
        }

        return $approval;
    }

    /**
     * Reject a request.
     */
    public function reject(ApprovalRequest $approval, ?string $notes = null): ApprovalRequest
    {
        $approval->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'notes'       => $notes,
            'decided_at'  => now(),
        ]);

        return $approval;
    }
}
