<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name',      'value' => 'DEI Health',          'type' => 'string',  'group' => 'general', 'label' => 'Nama Aplikasi'],
            ['key' => 'pondok_name',   'value' => 'Dar El-Ilmi',         'type' => 'string',  'group' => 'general', 'label' => 'Nama Pondok Pesantren'],
            ['key' => 'date_format',   'value' => 'Y-m-d',               'type' => 'string',  'group' => 'general', 'label' => 'Format Tanggal'],

            // Obat / Inventory
            ['key' => 'stok_minimum_default',       'value' => '10',  'type' => 'integer', 'group' => 'inventory', 'label' => 'Batas Stok Minimum Default'],
            ['key' => 'batas_hampir_kadaluarsa_hari','value' => '90',  'type' => 'integer', 'group' => 'inventory', 'label' => 'Peringatan Kadaluarsa (hari sebelum)'],

            // Notifikasi
            ['key' => 'notif_stok_menipis',       'value' => 'true', 'type' => 'boolean', 'group' => 'notification', 'label' => 'Aktifkan Notif Stok Menipis'],
            ['key' => 'notif_hampir_kadaluarsa',   'value' => 'true', 'type' => 'boolean', 'group' => 'notification', 'label' => 'Aktifkan Notif Hampir Kadaluarsa'],
            ['key' => 'notif_rawat_inap_lama',     'value' => 'true', 'type' => 'boolean', 'group' => 'notification', 'label' => 'Aktifkan Notif Rawat Inap Lama'],
            ['key' => 'rawat_inap_batas_hari',     'value' => '7',    'type' => 'integer', 'group' => 'notification', 'label' => 'Batas Hari Rawat Inap sebelum Alert'],

            // Approval
            ['key' => 'approval_user_baru',       'value' => 'true', 'type' => 'boolean', 'group' => 'approval', 'label' => 'Wajib Approval untuk User Baru'],
            ['key' => 'approval_hapus_santri',     'value' => 'true', 'type' => 'boolean', 'group' => 'approval', 'label' => 'Wajib Approval untuk Hapus Data Santri'],

            // Laporan
            ['key' => 'laporan_logo_path',        'value' => null,   'type' => 'string',  'group' => 'report', 'label' => 'Path Logo untuk Header Laporan'],
            ['key' => 'laporan_footer_text',      'value' => 'DEI Health – Dar El-Ilmi', 'type' => 'string', 'group' => 'report', 'label' => 'Footer Laporan'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
