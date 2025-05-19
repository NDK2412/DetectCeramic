<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckApiConfig
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem FASTAPI_URL và FASTAPI_KEY có tồn tại trong bảng settings
        $fastapiUrl = Setting::where('key', 'fastapi_url')->first();
        $fastapiKey = Setting::where('key', 'fastapi_key')->first();

        // Nếu một trong hai giá trị chưa được thiết lập, chuyển hướng đến trang cấu hình
        if (!$fastapiUrl || !$fastapiKey) {
            return redirect()->route('config.show');
        }

        return $next($request);
    }
}