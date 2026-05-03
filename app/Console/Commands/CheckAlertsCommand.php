<?php

namespace App\Console\Commands;

use App\Models\Obat;
use App\Models\RawatInap;
use App\Models\User;
use App\Notifications\InpatientAlertNotification;
use App\Notifications\StockAlertNotification;
use Illuminate\Console\Command;

class CheckAlertsCommand extends Command
{
    protected $signature   = 'deihealth:check-alerts';
    protected $description = 'DEI Health – Periksa kondisi stok dan rawat inap, kirim notifikasi jika ditemukan masalah';

    public function handle(): void
    {
        $this->info('[DEI Health] Memeriksa kondisi stok obat…');

        // Penerima: semua petugas dan admin
        $recipients = User::whereIn('role', ['super_admin', 'admin', 'petugas_kesehatan'])->get();

        // ── 1. Stok menipis ──────────────────────────────────────────────
        $stokMenipis = Obat::stokMenipis()
            ->get(['id', 'kode_obat', 'nama_obat', 'stok', 'stok_minimum'])
            ->toArray();

        if (count($stokMenipis) > 0) {
            $recipients->each(fn($u) => $u->notify(new StockAlertNotification('stok_menipis', $stokMenipis)));
            $this->warn('  → ' . count($stokMenipis) . ' obat stok menipis – notifikasi dikirim.');
        }

        // ── 2. Hampir kadaluarsa ─────────────────────────────────────────
        $batas = (int) \App\Models\Setting::get('batas_hampir_kadaluarsa_hari', 90);
        $hampirKadaluarsa = Obat::hampirKadaluarsa($batas)
            ->get(['id', 'kode_obat', 'nama_obat', 'tanggal_kadaluarsa'])
            ->toArray();

        if (count($hampirKadaluarsa) > 0) {
            $recipients->each(fn($u) => $u->notify(new StockAlertNotification('hampir_kadaluarsa', $hampirKadaluarsa)));
            $this->warn('  → ' . count($hampirKadaluarsa) . ' obat hampir kadaluarsa – notifikasi dikirim.');
        }

        // ── 3. Sudah kadaluarsa ──────────────────────────────────────────
        $kadaluarsa = Obat::kadaluarsa()
            ->get(['id', 'kode_obat', 'nama_obat', 'tanggal_kadaluarsa'])
            ->toArray();

        if (count($kadaluarsa) > 0) {
            $admins = User::whereIn('role', ['super_admin', 'admin'])->get();
            $admins->each(fn($u) => $u->notify(new StockAlertNotification('kadaluarsa', $kadaluarsa)));
            $this->error('  → ' . count($kadaluarsa) . ' obat kadaluarsa – notifikasi kritis dikirim.');
        }

        // ── 4. Rawat inap terlalu lama (> X hari sesuai setting) ─────────
        $batasHariRawat = (int) \App\Models\Setting::get('rawat_inap_batas_hari', 7);
        $lama = RawatInap::where('status_rawat', 'aktif')
            ->where('tanggal_masuk', '<=', now()->subDays($batasHariRawat))
            ->with('santri:id,nama_lengkap,nis')
            ->get();

        if ($lama->count() > 0) {
            $data = $lama->map(fn($r) => [
                'rawat_inap_id' => $r->id,
                'santri'        => $r->santri?->nama_lengkap,
                'nis'           => $r->santri?->nis,
                'hari_ke'       => $r->tanggal_masuk->diffInDays(now()),
            ])->toArray();

            $recipients->each(fn($u) => $u->notify(new InpatientAlertNotification($data)));
            $this->warn('  → ' . count($data) . ' santri rawat inap > ' . $batasHariRawat . ' hari.');
        }

        $this->info('[DEI Health] Pemeriksaan selesai.');
    }
}
