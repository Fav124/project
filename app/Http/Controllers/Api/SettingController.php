<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return response()->json([
            'success' => true,
            'data'    => $settings
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $settings = $request->all();
        
        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        // Clear cache if needed
        Cache::forget('app_settings');

        return response()->json([
            'success' => true,
            'message' => 'Semua pengaturan berhasil diperbarui.'
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key'         => 'required|string|unique:settings,key',
            'value'       => 'nullable',
            'type'        => 'required|in:string,boolean,integer,json',
            'group'       => 'nullable|string',
            'label'       => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $setting = Setting::create($request->all());

        return response()->json([
            'message' => 'Setting baru berhasil ditambahkan.',
            'data'    => $setting,
        ], 201);
    }
}
