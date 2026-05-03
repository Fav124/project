<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected \App\Services\ApprovalService $approvalService;

    public function __construct(\App\Services\ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index()
    {
        $approvals = ApprovalRequest::with('requester')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('approvals.index', compact('approvals'));
    }

    public function approve(ApprovalRequest $approval)
    {
        $this->approvalService->approve($approval);
        return back()->with('success', 'Permintaan berhasil disetujui.');
    }

    public function reject(Request $request, ApprovalRequest $approval)
    {
        $this->approvalService->reject($approval, $request->notes);
        return back()->with('error', 'Permintaan telah ditolak.');
    }
}
