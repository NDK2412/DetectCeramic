<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PredictionControllerForADR extends Controller
{
    public function predict(Request $request)
    {
        Log::info('predict endpoint called', [
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);

        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            Log::warning('No token provided for predict', [
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng cung cấp api_token.'
            ], 401);
        }

        $user = User::where('api_token', $apiToken)->first();
        if (!$user) {
            Log::warning('Invalid api_token for predict', [
                'token' => substr($apiToken, 0, 10) . '...',
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'api_token không hợp lệ. Vui lòng đăng nhập lại.'
            ], 401);
        }

        if ($user->status !== 'active') {
            Log::warning('Inactive user attempted predict', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Tài khoản của bạn đã bị khóa.'
            ], 403);
        }

        Log::info('predict authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $this->handlePrediction($request, $user);
    }

    public function predictForAndroid(Request $request)
    {
        Log::info('predictForAndroid endpoint called', [
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);

        $apiToken = $request->bearerToken();
        if (!$apiToken) {
            Log::warning('No token provided for Android predict', [
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng cung cấp api_token.'
            ], 401);
        }

        $user = User::where('api_token', $apiToken)->first();
        if (!$user) {
            Log::warning('Invalid api_token for Android predict', [
                'token' => substr($apiToken, 0, 10) . '...',
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'api_token không hợp lệ. Vui lòng đăng nhập lại.'
            ], 401);
        }

        if ($user->status !== 'active') {
            Log::warning('Inactive user attempted predict', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Tài khoản của bạn đã bị khóa.'
            ], 403);
        }

        Log::info('Android predict authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $this->handlePrediction($request, $user);
    }

    protected function handlePrediction(Request $request, User $user)
    {
        if (!$request->hasFile('file')) {
            Log::warning('No file uploaded', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'error' => 'Vui lòng upload ảnh trước!'
            ], 400);
        }

        if ($user->tokens <= 0) {
            Log::warning('User out of tokens', [
                'user_id' => $user->id,
                'tokens' => $user->tokens,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Bạn đã hết lượt dự đoán! Vui lòng nạp thêm.'
            ], 403);
        }

        $file = $request->file('file');
        $formData = new \Illuminate\Http\UploadedFile($file->path(), $file->getClientOriginalName());

        try {
            $apiKey = env('FASTAPI_KEY');
            Log::info('Sending request to FastAPI', [
                'url' => 'http://localhost:55001/predict',
                'api_key' => $apiKey,
                'file_name' => $formData->getClientOriginalName(),
                'file_path' => $formData->path(),
                'user_id' => $user->id,
            ]);

            $response = Http::withHeaders([
                'api-key' => $apiKey
            ])
                ->attach('file', file_get_contents($formData->path()), $formData->getClientOriginalName())
                ->post('http://localhost:55001/predict');

            Log::info('FastAPI Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'user_id' => $user->id,
            ]);

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
}