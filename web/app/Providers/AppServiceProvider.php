<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        // Lấy múi giờ từ bảng settings
        $timezone = Setting::where('key', 'timezone')->first()->value ?? config('app.timezone');

        // Cập nhật múi giờ cho ứng dụng
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
    }
}
