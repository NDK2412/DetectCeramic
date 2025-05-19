<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchFastApiStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('Starting FetchFastApiStats job');
        $stats = [
            'cpu_usage_percent' => 0,
            'ram_total_mb' => 0,
            'ram_used_mb' => 0,
            'ram_usage_percent' => 0,
            'gpu_usage_percent' => 0,
            'gpu_total_mb' => 0,
            'gpu_used_mb' => 0,
            'error' => null
        ];

        try {
            $maxRetries = 3;
            $retryDelay = 1000; // 1 giây
            $attempt = 0;

            while ($attempt < $maxRetries) {
                try {
                    $response = Http::withHeaders(['api-key' => env('FASTAPI_KEY')])
                        ->timeout(5)
                        ->get('http://localhost:55001/system-stats');

                    if ($response->successful()) {
                        $stats = $response->json();
                        Log::info('FastAPI stats fetched successfully', ['stats' => $stats]);
                        break;
                    } else {
                        $stats['error'] = 'FastAPI request failed: ' . $response->status();
                        Log::warning('FastAPI request failed', [
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);
                        $attempt++;
                        if ($attempt < $maxRetries) {
                            usleep($retryDelay * 1000); // Delay trước khi thử lại
                        }
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $stats['error'] = 'FastAPI connection error: ' . $e->getMessage();
                    Log::warning('FastAPI connection error', [
                        'attempt' => $attempt + 1,
                        'error' => $e->getMessage()
                    ]);
                    $attempt++;
                    if ($attempt < $maxRetries) {
                        usleep($retryDelay * 1000);
                    }
                }
            }

            if ($attempt >= $maxRetries) {
                Log::error('FastAPI stats fetch failed after max retries', ['stats' => $stats]);
            }
        } catch (\Exception $e) {
            $stats['error'] = 'Unexpected error: ' . $e->getMessage();
            Log::error('Unexpected error in FetchFastApiStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Kiểm tra ngưỡng 90%
        if ($stats['cpu_usage_percent'] > 90) {
            Log::emergency('CẢNH BÁO KHẨN CẤP: CPU (FastAPI) vượt ngưỡng 90% - Hiện tại: ' . $stats['cpu_usage_percent'] . '%');
        }
        if ($stats['ram_usage_percent'] > 90) { // Sửa từ 10 thành 90
            Log::emergency('CẢNH BÁO KHẨN CẤP: RAM (FastAPI) vượt ngưỡng 90% - Hiện tại: ' . $stats['ram_usage_percent'] . '%');
        }
        if ($stats['gpu_usage_percent'] > 90) {
            Log::emergency('CẢNH BÁO KHẨN CẤP: GPU (FastAPI) vượt ngưỡng 90% - Hiện tại: ' . $stats['gpu_usage_percent'] . '%');
        }

        Cache::put('fastapi_stats', $stats, 60);
        Log::info('FetchFastApiStats completed', ['stats' => $stats]);
    }
}