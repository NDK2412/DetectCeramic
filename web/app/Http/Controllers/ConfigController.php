<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    public function show(Request $request)
    {
        // Kiểm tra xem FASTAPI_URL và FASTAPI_KEY đã được thiết lập
        $fastapiUrl = Setting::where('key', 'fastapi_url')->first();
        $fastapiKey = Setting::where('key', 'fastapi_key')->first();

        // Nếu cả hai đã được thiết lập, chuyển hướng đến trang chính
        if ($fastapiUrl && $fastapiKey) {
            return redirect()->route('news.index');
        }

        // Lấy giá trị hiện tại (nếu có) để hiển thị trong form
        $fastapiUrlValue = $fastapiUrl ? $fastapiUrl->value : '';
        $fastapiKeyValue = $fastapiKey ? $fastapiKey->value : '';

        return view('config', [
            'fastapiUrl' => $fastapiUrlValue,
            'fastapiKey' => $fastapiKeyValue
        ]);
    }

    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'fastapi_url' => 'required|url',
            'fastapi_key' => 'required|string',
        ]);

        // Lưu hoặc cập nhật FASTAPI_URL
        Setting::updateOrCreate(
            ['key' => 'fastapi_url'],
            ['value' => $request->fastapi_url]
        );

        // Lưu hoặc cập nhật FASTAPI_KEY
        Setting::updateOrCreate(
            ['key' => 'fastapi_key'],
            ['value' => $request->fastapi_key]
        );

        // Xóa cache
        Cache::forget('settings');

        // Chuyển hướng đến trang chính
        return redirect()->route('news.index')->with('success', 'Cấu hình API đã được lưu!');
    }
}