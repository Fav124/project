<?php

namespace App\Console\Commands;

use App\Models\BackupLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseCommand extends Command
{
    protected $signature   = 'deihealth:backup-db {user_id?}';
    protected $description = 'DEI Health – Backup database ke storage/app/backups';

    public function handle(): void
    {
        $userId = $this->argument('user_id');
        $this->info('[DEI Health] Memulai proses backup database…');

        $dbConfig = config('database.connections.' . config('database.default'));
        $filename = 'deihealth_backup_' . now()->format('Y_m_d_His') . '.sql';
        $storagePath = storage_path('app/backups');
        $fullPath    = "{$storagePath}/{$filename}";

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $host     = escapeshellarg($dbConfig['host']);
        $port     = escapeshellarg($dbConfig['port'] ?? '3306');
        $user     = escapeshellarg($dbConfig['username']);
        $password = escapeshellarg($dbConfig['password']);
        $database = escapeshellarg($dbConfig['database']);
        $dest     = escapeshellarg($fullPath);

        $command = "mysqldump -h {$host} -P {$port} -u {$user} -p{$password} {$database} > {$dest} 2>&1";

        exec($command, $output, $returnCode);

        $status = $returnCode === 0 ? 'success' : 'failed';
        $size   = file_exists($fullPath) ? filesize($fullPath) : 0;
        $notes  = $returnCode !== 0 ? implode("\n", $output) : null;

        BackupLog::create([
            'filename'     => $filename,
            'disk'         => 'local',
            'path'         => "backups/{$filename}",
            'size_bytes'   => $size,
            'status'       => $status,
            'notes'        => $notes,
            'initiated_by' => $userId,
        ]);

        if ($status === 'success') {
            $this->info("[DEI Health] Backup berhasil: {$filename} (" . number_format($size / 1024, 2) . " KB)");
        } else {
            $this->error("[DEI Health] Backup gagal! Lihat backup_logs untuk detail.");
        }
    }
}
