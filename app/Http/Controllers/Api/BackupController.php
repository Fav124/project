<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    /**
     * Jalankan backup secara manual via API (hanya super_admin).
     */
    public function store(): JsonResponse
    {
        Artisan::call('deihealth:backup');

        $latestLog = BackupLog::latest()->first();

        return response()->json([
            'message' => 'Backup berhasil dijalankan.',
            'data'    => $latestLog,
        ]);
    }

    /**
     * Lihat riwayat backup.
     */
    public function index(): JsonResponse
    {
        $logs = BackupLog::with('initiator:id,name')->latest()->paginate(15);
        return response()->json($logs);
    }
}
