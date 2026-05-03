<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupLog::with('user')->latest()->get();
        return view('backups.index', compact('backups'));
    }

    public function store(Request $request)
    {
        try {
            Artisan::call('deihealth:backup-db', [
                'user_id' => auth()->id()
            ]);
            return back()->with('success', 'Database berhasil di-backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $backup = BackupLog::findOrFail($id);
        $path = storage_path('app/' . $backup->path);

        if (!file_exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan di storage.');
        }

        return response()->download($path);
    }

    public function restore($id)
    {
        $backup = BackupLog::findOrFail($id);
        $path = storage_path('app/' . $backup->path);

        if (!file_exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        try {
            $dbConfig = config('database.connections.' . config('database.default'));
            $host     = escapeshellarg($dbConfig['host']);
            $port     = escapeshellarg($dbConfig['port'] ?? '3306');
            $user     = escapeshellarg($dbConfig['username']);
            $password = escapeshellarg($dbConfig['password']);
            $database = escapeshellarg($dbConfig['database']);
            $src      = escapeshellarg($path);

            // Command untuk restore
            $command = "mysql -h {$host} -P {$port} -u {$user} -p{$password} {$database} < {$src} 2>&1";
            
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("Restore gagal: " . implode("\n", $output));
            }

            return back()->with('success', 'Database berhasil di-restore ke titik: ' . $backup->filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan restore: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt'
        ]);

        try {
            $file = $request->file('backup_file');
            $filename = 'imported_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('backups', $filename);

            BackupLog::create([
                'filename' => $filename,
                'disk' => 'local',
                'path' => $path,
                'size_bytes' => $file->getSize(),
                'status' => 'success',
                'notes' => 'Imported manually',
                'initiated_by' => auth()->id(),
            ]);

            return back()->with('success', 'File backup berhasil di-import. Anda sekarang bisa melakukan Restore jika diperlukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal meng-import file: ' . $e->getMessage());
        }
    }
}
