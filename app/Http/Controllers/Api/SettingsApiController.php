<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsApiController extends BaseApiController
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return $this->success($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        return $this->success([], 'Pengaturan berhasil disimpan.');
    }
}
