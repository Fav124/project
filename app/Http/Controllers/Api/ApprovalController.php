<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\ApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(protected ApprovalService $approvalService) {}

    public function index(Request $request): JsonResponse
    {
        // For Android compatibility, we map ApprovalRequests to a user-like structure
        $query = ApprovalRequest::with(['requester'])
            ->where('action', 'register_user')
            ->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $approvals = $query->paginate(15);
        
        $mapped = $approvals->getCollection()->map(function ($app) {
            return [
                'id' => $app->id,
                'name' => $app->payload['name'] ?? 'Unknown',
                'email' => $app->payload['email'] ?? '-',
                'role' => $app->payload['role'] ?? 'petugas_kesehatan',
                'role_label' => ($app->payload['role'] ?? '') === 'admin' ? 'Admin' : 'Petugas',
                'status' => $app->status,
                'status_label' => ucfirst($app->status),
                'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                'notes' => $app->notes,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar permintaan persetujuan berhasil dimuat.',
            'data' => $mapped,
            'meta' => [
                'current_page' => $approvals->currentPage(),
                'last_page' => $approvals->lastPage(),
                'total' => $approvals->total(),
            ]
        ]);
    }

    public function show(ApprovalRequest $approval): JsonResponse
    {
        $approval->load(['requester:id,name', 'approver:id,name', 'approvable']);
        return response()->json($approval);
    }

    public function approve(Request $request, ApprovalRequest $approval): JsonResponse
    {
        $request->validate(['notes' => 'nullable|string']);

        if (!$approval->isPending()) {
            return response()->json(['message' => 'Hanya approval pending yang bisa disetujui.'], 400);
        }

        $result = $this->approvalService->approve($approval, $request->notes);

        return response()->json(['message' => 'Permintaan berhasil disetujui.', 'data' => $result]);
    }

    public function reject(Request $request, ApprovalRequest $approval): JsonResponse
    {
        $request->validate(['notes' => 'required|string']);

        if (!$approval->isPending()) {
            return response()->json(['message' => 'Hanya approval pending yang bisa ditolak.'], 400);
        }

        $result = $this->approvalService->reject($approval, $request->notes);

        return response()->json(['message' => 'Permintaan berhasil ditolak.', 'data' => $result]);
    }
}
