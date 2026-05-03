<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'institution_logo']);
        
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('institution_logo')) {
            $path = $request->file('institution_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'institution_logo'], ['value' => $path]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
