<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Classification;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PredictionController extends Controller
{
    public function predict(Request $request)
    {
        if (!Auth::check()) {
            Log::warning('Unauthenticated predict attempt', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return response()->json(['success' => false, 'error' => 'Bạn cần đăng nhập để sử dụng tính năng này.'], 401);
        }

        $user = Auth::user();

        if (!$request->hasFile('file')) {
            Log::warning('No file uploaded', ['user_id' => $user->id]);
            return response()->json(['success' => false, 'error' => 'Vui lòng upload ảnh trước!'], 400);
        }

        if ($user->tokens <= 0) {
            Log::warning('User out of tokens', ['user_id' => $user->id, 'tokens' => $user->tokens]);
            return response()->json(['success' => false, 'error' => 'Bạn đã hết lượt dự đoán! Vui lòng nạp thêm.'], 403);
        }

        $file = $request->file('file');
        $formData = new \Illuminate\Http\UploadedFile($file->path(), $file->getClientOriginalName());

        try {
            // Lấy fastapi_url và fastapi_key từ database
            $fastapiUrl = Setting::where('key', 'fastapi_url')->first()?->value;
            $fastapiKey = Setting::where('key', 'fastapi_key')->first()?->value;

            if (!$fastapiUrl || !$fastapiKey) {
                Log::error('Missing fastapi_url or fastapi_key in settings', [
                    'fastapi_url' => $fastapiUrl,
                    'fastapi_key' => $fastapiKey,
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Cấu hình FastAPI không đầy đủ. Liên hệ admin.'
                ], 500);
            }

            $predictEndpoint = rtrim($fastapiUrl, '/') . '/predict';
            Log::info('Sending request to FastAPI', [
                'url' => $predictEndpoint,
                'api_key' => substr($fastapiKey, 0, 10) . '...',
                'file_name' => $formData->getClientOriginalName(),
                'file_path' => $formData->path(),
                'user_id' => $user->id,
            ]);

            // Thêm retry logic và tăng timeout
            $response = null;
            $maxRetries = 3;
            $timeout = 60; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $response = Http::timeout($timeout)
                        ->withHeaders(['api-key' => $fastapiKey])
                        ->attach('file', file_get_contents($formData->path()), $formData->getClientOriginalName())
                        ->post($predictEndpoint);

                    break; // Thoát vòng lặp nếu thành công
                } catch (\Exception $e) {
                    Log::warning("FastAPI request failed, attempt $attempt/$maxRetries", [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    if ($attempt === $maxRetries) {
                        throw $e; // Ném lỗi nếu hết số lần thử
                    }
                    sleep(1); // Chờ 1 giây trước khi thử lại
                }
            }

            Log::info('FastAPI Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'user_id' => $user->id,
            ]);

            // Kiểm tra nếu phản hồi là HTML
            $body = $response->body();
            if (stripos($body, '<!DOCTYPE') !== false || stripos($body, '<html') !== false) {
                Log::error('FastAPI returned HTML instead of JSON', [
                    'body' => substr($body, 0, 200),
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'FastAPI trả về HTML thay vì JSON: ' . substr($body, 0, 100)
                ], 500);
            }

            if ($response->status() !== 200) {
                Log::error('FastAPI returned non-200 status', [
                    'status' => $response->status(),
                    'body' => $body,
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'FastAPI trả về lỗi: ' . $body
                ], $response->status());
            }

            $predictData = $response->json();

            if (!$predictData || !is_array($predictData)) {
                Log::error('Invalid FastAPI response', [
                    'response' => $predictData,
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Phản hồi từ FastAPI không hợp lệ'
                ], 500);
            }

            if (isset($predictData['error'])) {
                Log::error('FastAPI returned error', [
                    'error' => $predictData['error'],
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => $predictData['error']
                ], 500);
            }

            if (!isset($predictData['predicted_class']) || !isset($predictData['llm_response'])) {
                Log::error('Missing keys in FastAPI response', [
                    'response' => $predictData,
                    'user_id' => $user->id,
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Dữ liệu từ FastAPI thiếu predicted_class hoặc llm_response'
                ], 500);
            }

            $user->tokens -= 1;
            $user->tokens_used += 1;
            $user->save();
            Log::info('User tokens updated', [
                'user_id' => $user->id,
                'tokens' => $user->tokens,
                'tokens_used' => $user->tokens_used,
            ]);

            $classification = new Classification();
            $imagePath = $file->store('images', 'public');
            $classification->user_id = $user->id;
            $classification->image_path = '/storage/' . $imagePath;
            $classification->result = $predictData['predicted_class'];
            $classification->llm_response = $predictData['llm_response'];
            $classification->save();
            Log::info('Classification saved', [
                'classification_id' => $classification->id,
                'user_id' => $user->id,
                'image_path' => $classification->image_path,
            ]);

            return response()->json([
                'success' => true,
                'predicted_class' => $predictData['predicted_class'],
                'llm_response' => $predictData['llm_response'],
                'tokens' => $user->tokens,
            ]);

        } catch (\Exception $e) {
            Log::error('Prediction error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Lỗi khi kết nối với server dự đoán: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getInfo($id)
    {
        $classification = Classification::findOrFail($id);
        $llm_response = $classification->llm_response;

        return view('classification_info', compact('llm_response'));
    }
}