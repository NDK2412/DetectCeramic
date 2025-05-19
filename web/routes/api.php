<?php
namespace App\Http\Middleware;
use App\Http\Controllers\PredictionControllerForADR;
use Illuminate\Http\Request;
use App\Http\Controllers\CeramicController;
use App\Http\Controllers\RechargeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\RechargePackageController;
use App\Http\Controllers\Auth\PasswordChangeController;

Route::prefix('adr')->group(function () {
    // Đăng nhập
    Route::post('/login', [AuthController::class, 'apiLogin'])
        ->name('adr.login');

    // Đăng ký
    Route::post('/register', [AuthController::class, 'apiRegister'])
        ->name('adr.register');

    // Đổi mật khẩu
    Route::post('/change-password', [PasswordChangeController::class, 'apiChange'])
        ->name('adr.change-password');

    // Dự đoán hình ảnh
    Route::post('/predict-android', [PredictionControllerForADR::class, 'predict'])
        ->name('adr.predict');

    // Kiểm tra trạng thái xác thực


    // Quản lý gói nạp
    // Route::apiResource('/recharge-packages', RechargePackageController::class);

    // Test API
    Route::get('/test', function () {
        return response()->json(['message' => 'Test API']);
    })->name('adr.test');
    //Lấy thông tin người dùng
    Route::get('/user', [AuthController::class, 'getUser']);
    //==========================================================================================================================
    //Phần nạp tiền android
    Route::get('/recharge-packages', [RechargePackageController::class, 'index']);
    Route::middleware(['auth:api'])->post('/recharge/submit', [RechargeController::class, 'submitAdr']);
    Route::middleware(['auth:api'])->get('/recharge/history', [RechargeController::class, 'getHistory']);
    // Route::get('/recharge/history', [RechargeController::class, 'getHistory']);


    // Route mới để lấy lịch sử nhận diện
    Route::get('/history', [CeramicController::class, 'getHistory'])->middleware('auth:api');

    Route::get('/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
});







