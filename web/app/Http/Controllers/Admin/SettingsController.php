<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy tất cả cài đặt từ bảng settings
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update a setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate input
        $request->validate([
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        // Cập nhật hoặc tạo mới các cài đặt
        foreach ($request->settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'] ?? '']
            );
        }

        // Xóa cache nếu có
        Cache::forget('settings');

        return redirect()->route('admin.settings')->with('success', 'Cài đặt đã được cập nhật!');
    }
}