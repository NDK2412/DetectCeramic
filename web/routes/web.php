<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ImagePredictionController;
use App\Http\Controllers\Cache;
use App\Http\Controllers\CeramicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RechargeController;
use App\Models\TermsAndConditions;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MetadataController;
// use App\Http\Controllers\ImageController;
// use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\CustomForgotPasswordController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Models\News;
use App\Models\Setting;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\RechargePackageController;

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::post('/predict-image', [PredictionController::class, 'predict'])->name('predict.image');
Route::get('/gallery', [CeramicController::class, 'gallery'])->name('gallery');
Route::get('/ceramics/{id}', [CeramicController::class, 'show'])->name('ceramics.show');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return view('ceramic.index');
})->middleware('auth')->name('dashboard');
Route::get('/', [NewsController::class, 'index'])->name('index');

Route::get('/news', [NewsController::class, 'news'])->name('news');

// Hiển thị form quên mật khẩu
Route::get('/forgot-password', [CustomForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');

// Xử lý đặt lại mật khẩu trực tiếp
Route::post('/reset-password', [CustomForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::post('/predict', [PredictionController::class, 'predict']);

Route::get('/check-auth', function () {
    return response()->json(['authenticated' => Auth::check()]);
});
Route::post('/logout', function () {
    Auth::logout();
    return response()->json(['message' => 'Logged out successfully']);
});
Route::middleware('auth:sanctum')->get('/check-auth', function (Request $request) {
    return response()->json(['authenticated' => true, 'user' => $request->user()]);
});

// Hiển thị trang login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Hiển thị trang đăng ký
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/', [CeramicController::class, 'index'])->name('home');
Route::post('/predict', [ImagePredictionController::class, 'predict'])->name('predict');

Route::get('/', function () {
    return view('index'); // Hoặc bất kỳ view nào có sẵn
});

Route::post('/use-token', [AuthController::class, 'useToken'])->middleware('auth'); // Route giảm token
Route::get('/recharge', function () {
    return view('recharge'); // Trang nạp token (chưa triển khai)
})->middleware('auth')->name('recharge');

Route::get('/admin', function () {
    return view('admin');
})->middleware('auth')->name('admin');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.update');

    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
    Route::post('/admin/recharge/approve/{id}', [AdminController::class, 'approveRecharge'])->name('admin.recharge.approve');
    Route::post('/admin/recharge/reject', [AdminController::class, 'rejectRecharge'])->name('admin.recharge.reject');
});

// Route cho admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
});

//Xử lý đánh giá người dùng
Route::post('/submit-rating', [App\Http\Controllers\UserController::class, 'submitRating'])->name('submit.rating');

Route::get('/recharge', [App\Http\Controllers\RechargeController::class, 'index'])->name('recharge.index');
Route::post('/recharge', [App\Http\Controllers\RechargeController::class, 'submit'])->name('recharge.submit');
Route::post('/admin/recharge/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveRecharge'])->name('admin.recharge.approve');

Route::get('/recharge/export/{id}', [RechargeController::class, 'exportReceipt'])->name('recharge.export');

Route::post('/recharge/message', [RechargeController::class, 'sendMessage'])->name('recharge.message');
Route::post('/admin/recharge/reject', [AdminController::class, 'rejectRecharge'])->name('admin.recharge.reject');

//Route setting
Route::post('/admin/settings/timezone', [App\Http\Controllers\AdminController::class, 'updateTimezone'])->name('admin.settings.timezone');

// Routes cho quản lý thư viện đồ gốm
Route::post('/admin/ceramics', [AdminController::class, 'storeCeramic'])->name('admin.ceramics.store');
Route::put('/admin/ceramics/{id}', [AdminController::class, 'updateCeramic'])->name('admin.ceramics.update');
Route::delete('/admin/ceramics/{id}', [AdminController::class, 'deleteCeramic'])->name('admin.ceramics.delete');

Route::post('/classify', [App\Http\Controllers\CeramicController::class, 'classify'])->name('classify');

//Quản lý điều khoản
Route::get('/admin/terms', function () {
    $terms = TermsAndConditions::first();
    return view('admin.terms', compact('terms'));
})->name('admin.terms');

Route::post('/admin/terms/update', function (Request $request) {
    $request->validate([
        'content' => 'required|string',
    ]);

    $terms = TermsAndConditions::first();
    if ($terms) {
        $terms->update(['content' => $request->content]);
    } else {
        TermsAndConditions::create(['content' => $request->content]);
    }

    return redirect()->route('admin.terms')->with('success', 'Chính sách và điều khoản đã được cập nhật.');
})->name('admin.terms.update');

Route::get('/terms-and-conditions', function () {
    $terms = TermsAndConditions::first();
    return response()->json(['content' => $terms ? $terms->content : 'Chưa có chính sách và điều khoản.']);
})->name('terms.show');

Route::get('/admin/terms', [AdminController::class, 'terms'])->name('admin.terms');
Route::post('/admin/terms/update', [AdminController::class, 'updateTerms'])->name('admin.terms.update');

Route::get('/guide', function () {
    return view('guide');
});

//Lưu file excel
Route::get('/admin/export-transaction-history', [AdminController::class, 'exportTransactionHistory'])->name('admin.export.transaction.history');

//Bật tắt capcha
Route::post('/admin/settings/captcha', [AdminController::class, 'updateCaptchaSetting'])->name('admin.settings.captcha');

// Thống kê số lượt sử dụng
Route::get('/admin/users/{user}/token-usage', [AdminController::class, 'showTokenUsage'])->name('admin.users.token-usage');

Route::get('/classification/{id}/info', [CeramicController::class, 'getClassificationInfo'])->middleware('auth')->name('classification.info');
Route::get('/dashboard', [CeramicController::class, 'dashboard'])->middleware('auth')->name('dashboard');

//Kiểm tra mật khẩu capcha khi nạp tiền
Route::post('/recharge/verify', [App\Http\Controllers\RechargeController::class, 'verify'])->middleware('auth')->name('recharge.verify');

//Settings theme
Route::post('/admin/settings/theme', [AdminController::class, 'updateTheme'])->name('admin.settings.theme');

// Route trang chủ
Route::get('/', function () {
    $theme = \App\Models\Setting::where('key', 'theme')->first()->value ?? 'index';
    return view($theme);
})->name('home');

//Phần liên hệ người dùng
Route::get('/admin', [ContactController::class, 'admin'])->name('admin.index');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/admin/contact/{id}', [ContactController::class, 'show'])->name('admin.contact.show');
Route::post('/admin/contact/{id}/mark-read', [ContactController::class, 'markAsRead'])->name('admin.contact.markRead');

//Phần tin tức
Route::middleware('auth')->group(function () {
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.delete');
});


Route::get('/news/{id}', function ($id) {
    $article = App\Models\News::findOrFail($id);
    return view('newsdetail', compact('article'));
})->name('news.detail');

//đổi tên
Route::post('/change-name', [AuthController::class, 'changeName'])->middleware('auth');

//Đổi mật khẩu
Route::get('/password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.form');
Route::post('/password/change', [PasswordChangeController::class, 'change'])->name('password.change');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    
Route::get('/admin/system/fastapi-stats', function () {
    $url = Setting::where('key', 'fastapi_url')->first()?->value . '/admin/system/fastapi-stats';
    $response = Http::get($url);
    return response()->json($response->json());
})->name('admin.system.fastapi_stats');

    Route::post('/admin/toggle-optimization', [AdminController::class, 'toggleOptimization'])->name('admin.toggle-optimization');
});

Route::get('/admin/system/laravel-stats', [AdminController::class, 'laravelStats'])->name('admin.system.laravel_stats');

//Sao lưu dữ liệu
Route::post('/admin/backup', [AdminController::class, 'backup'])->name('admin.backup');

//Quản lý gói nạp tiền
Route::post('/recharge-packages', [RechargePackageController::class, 'store'])->name('recharge-packages.store');
Route::put('/recharge-packages/{id}', [RechargePackageController::class, 'update'])->name('recharge-packages.update');
Route::delete('/recharge-packages/{id}', [RechargePackageController::class, 'destroy'])->name('recharge-packages.destroy');

//Cập nhật hiển thị gói nạp cho trang user
Route::get('/recharge', [App\Http\Controllers\RechargeController::class, 'index'])->name('recharge.index');
Route::post('/recharge/submit', [App\Http\Controllers\RechargeController::class, 'submit'])->name('recharge.submit');

//Hiển thị bình luận của người dùng trên dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

//Cài đặt llm
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/llm-settings', [AdminController::class, 'updateLLMSettings'])->name('admin.updateLLMSettings');
});

//Chỉnh sửa thông tin user
Route::post('/update-user-info', [App\Http\Controllers\DashboardController::class, 'updateUserInfo'])->name('update.user.info');

//Xem thông tin chi tiết nhận diện
Route::get('/classification/{id}/info', [PredictionController::class, 'getInfo'])->name('classification.info');

use App\Http\Controllers\ApkController;

// routes/web.php
Route::post('/admin/apk/upload', [ApkController::class, 'upload'])->name('admin.apk.upload')->middleware('web'); // Phải có middleware 'web' để xử lý session

//Chỉnh metadata
Route::get('/metadata', [MetadataController::class, 'index'])->name('admin.metadata.index');
Route::post('/admin', [AdminController::class, 'store'])->name('admin.metadata.store');
Route::get('/metadata/{id}/edit', [MetadataController::class, 'edit'])->name('admin.metadata.edit');
Route::put('/metadata/{id}', [MetadataController::class, 'update'])->name('admin.metadata.update');
Route::post('/admin/recognition-model/update', [AdminController::class, 'updateRecognitionModel'])->name('admin.recognition-model.update');
Route::post('/admin/model/upload', [AdminController::class, 'uploadModel'])->name('admin.model.upload');

// Route cho Sửa, Cập nhật và Xóa tin tức
Route::get('/admin/news/{id}/edit', [AdminController::class, 'editNews'])->name('admin.news.edit');
Route::put('/admin/news/{id}', [AdminController::class, 'updateNews'])->name('admin.news.update');
Route::delete('/admin/news/{id}', [AdminController::class, 'deleteNews'])->name('admin.news.delete');
// Route::post('/admin/news/fetch', function () {
//     try {
//         \Artisan::call('news:fetch');
//         return response()->json(['success' => true, 'message' => 'Cập nhật tin tức thành công!']);
//     } catch (\Exception $e) {
//         return response()->json(['success' => false, 'message' => 'Lỗi khi cập nhật tin tức: ' . $e->getMessage()], 500);
//     }
// })->middleware('auth')->name('admin.news.fetch');

Route::post('/admin/news/fetch', function () {
    try {
        // Gọi Artisan Command
        Artisan::call('news:fetch');
        return Response::json([
            'success' => true,
            'message' => 'Cập nhật tin tức thành công!'
        ]);
    } catch (\Exception $e) {
        return Response::json([
            'success' => false,
            'message' => 'Lỗi khi cập nhật tin tức: ' . $e->getMessage()
        ], 500);
    }
})->middleware('auth')->name('admin.news.fetch');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.update');
Route::delete('/admin/users/{id}', [AdminController::class, 'delete'])->name('admin.delete');

// Config Routes (Thêm để hỗ trợ cấu hình API URL và API Key)
Route::get('/config', [AdminController::class, 'config'])->name('config.index');
Route::post('/config/save', [AdminController::class, 'saveConfig'])->name('config.save');

// Cập nhật route /admin để thêm middleware check.config
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});


use App\Http\Controllers\Admin\SettingsController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
});
use App\Http\Controllers\ConfigController;
// Route gốc kiểm tra cấu hình
Route::get('/', [ConfigController::class, 'show'])->name('config.show');
Route::post('/config', [ConfigController::class, 'store'])->name('config.store');

// Routes admin yêu cầu đăng nhập
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});
// Route cho trang chính
Route::get('/', function () {
    $news = News::all();
    $currentTheme = Setting::where('key', 'theme')->first()->value ?? 'index'; // Mặc định là 'index'
    return view($currentTheme, compact('news'));
})->name('home');

//      ->name('admin.settings.captcha')
//      ->middleware('auth'); // Đảm bảo phải đăng nhập
/*use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/upload', function () {
    return view('upload');
});

Route::post('/upload', function (Request $request) {
    // Validate input
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Lưu ảnh vào thư mục public/images
    $imageName = time() . '.' . $request->image->extension();
    $request->image->move(public_path('images'), $imageName);

    return back()->with('success', 'Ảnh đã được tải lên thành công!')->with('image', $imageName);
});
*/
?>