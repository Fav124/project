<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─────────────────────────────────────────────
// DEI Health – Scheduled Tasks
// ─────────────────────────────────────────────

// Periksa kondisi stok & rawat inap setiap pagi pukul 07:00
Schedule::command('deihealth:check-alerts')->dailyAt('07:00');

// Backup database setiap malam pukul 02:00
Schedule::command('deihealth:backup')->dailyAt('02:00');

