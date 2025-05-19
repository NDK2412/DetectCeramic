<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Apk;
use App\Models\Metadata;
use Illuminate\Support\Facades\Log;
use App\Models\RechargePackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Recharge;
use App\Models\RechargeRequest;
use App\Models\RechargeHistory;
use App\Models\TermsAndConditions;
use App\Http\Controllers\RecognitionHistory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Ceramic;
use Illuminate\Support\Facades\Storage; // Thêm import này ở đầu file
use App\Models\News;
use App\Models\TokenUsage;
use App\Models\Setting;
use App\Models\Classification;
use Illuminate\Support\Facades\Cache;
use App\Jobs\FetchLaravelStats;
use App\Jobs\FetchFastApiStats;
class AdminController extends Controller
{

    public function __construct()
    {
        // Đăng ký middleware trong constructor
        $this->middleware('auth'); // Middleware kiểm tra đăng nhập
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                return redirect('/dashboard');
            }
            return $next($request);
        });
    }
    public function rejectRecharge(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:recharge_requests,id',
            'reason' => 'required|string|max:500',
        ]);
        $rechargeRequest = RechargeRequest::findOrFail($request->request_id);
        $rechargeRequest->status = 'rejected';
        $rechargeRequest->save();
        // Lưu tin nhắn cho người dùng
        Message::create([
            'user_id' => $rechargeRequest->user_id,
            'admin_id' => auth()->id(),
            'message' => "Yêu cầu nạp tiền của bạn (Ngày: {$rechargeRequest->updated_at}) đã bị từ chối. Lý do: " . $request->reason,
            'created_at' => now(),
        ]);
        return redirect()->route('admin.index')->with('success', 'Đã từ chối yêu cầu và gửi tin nhắn cho người dùng.');
    }
    public function index()
    {
        $users = User::all();
        $metadata = Metadata::all();
        //Liên quan bật tắt thông tin hệ thống
        $optimizationSetting = Setting::where('key', 'system_info_optimization')->first();
        $isSystemInfoEnabled = $optimizationSetting && $optimizationSetting->value === 'enabled';
        \Log::info('isSystemInfoEnabled: ' . ($isSystemInfoEnabled ? 'true' : 'false')); // Debug log
        if ($isSystemInfoEnabled) {
            FetchFastApiStats::dispatch();
            \Illuminate\Support\Facades\Log::info('Jobs dispatched from index');
        }
        $laravelStats = $isSystemInfoEnabled ? Cache::get('laravel_stats', []) : [];
        $fastapistats = $isSystemInfoEnabled ? Cache::get('fastapi_stats', []) : [];
        // Kiểm tra ngưỡng và tạo cảnh báo
        $alerts = [];
        if ($isSystemInfoEnabled) {
            // $ramUsagePercent = $laravelStats['ram_total'] > 0 ? ($laravelStats['ram_used'] / $laravelStats['ram_total']) * 100 : 0;
            // $gpuUsagePercent = $laravelStats['gpu_total'] > 0 ? ($laravelStats['gpu_used'] / $laravelStats['gpu_total']) * 100 : 0;
            // if ($laravelStats['cpu_usage'] > 90) {
            //     $alerts[] = 'CPU (Laravel) vượt ngưỡng 90%: ' . $laravelStats['cpu_usage'] . '%';
            // }
            // if ($ramUsagePercent > 90) {
            //     $alerts[] = 'RAM (Laravel) vượt ngưỡng 90%: ' . round($ramUsagePercent, 2) . '%';
            // }
            // if ($laravelStats['gpu_usage'] > 90 || $gpuUsagePercent > 90) {
            //     $alerts[] = 'GPU (Laravel) vượt ngưỡng 90%: ' . $laravelStats['gpu_usage'] . '% (Utilization) hoặc ' . round($gpuUsagePercent, 2) . '% (Memory)';
            // }
            // if ($fastapistats['cpu_usage_percent'] > 90) {
            //     $alerts[] = 'CPU (FastAPI) vượt ngưỡng 90%: ' . $fastapistats['cpu_usage_percent'] . '%';
            // }
            // if ($fastapistats['ram_usage_percent'] > 90) {
            //     $alerts[] = 'RAM (FastAPI) vượt ngưỡng 90%: ' . $fastapistats['ram_usage_percent'] . '%';
            // }
            // if ($fastapistats['gpu_usage_percent'] > 90) {
            //     $alerts[] = 'GPU (FastAPI) vượt ngưỡng 90%: ' . $fastapistats['gpu_usage_percent'] . '%';
            // }
        }
        // Lấy giao diện hiện tại từ database
        $currentTheme = Setting::where('key', 'theme')->first()->value ?? 'index';
        $users = User::with('loginHistories')->get(); //lưu lịch sử đăng nhập
        $rechargeRequests = RechargeRequest::where('status', 'pending')->get();
        $totalRevenue = RechargeHistory::sum('amount');
        $averageRating = User::avg('rating') ?? 0;
        // Thêm số liệu mới
        $approvedRequests = RechargeRequest::where('status', 'approved')->count();
        $rejectedRequests = RechargeRequest::where('status', 'rejected')->count();
        // Thêm số liệu cho người dùng hoạt động và không hoạt động
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        // Cập nhật để tính doanh thu theo ngày
        $revenueData = RechargeHistory::select(
            DB::raw('DATE_FORMAT(approved_at, "%Y-%m-%d") as day'),
            DB::raw('SUM(amount) as total')
        )
            ->whereNotNull('approved_at')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        // Cập nhật để tính doanh thu theo tháng
        $revenueDataM = RechargeHistory::select(
            DB::raw('DATE_FORMAT(approved_at, "%Y-%m-%d") as month'),  // Đổi thành '%Y-%m' để lấy theo tháng
            DB::raw('SUM(amount) as total')
        )
            ->whereNotNull('approved_at')
            ->groupBy('month')  // Nhóm theo tháng
            ->orderBy('month')  // Sắp xếp theo tháng
            ->get();

        $revenueLabelsM = $revenueDataM->pluck('month')->toArray();
        $revenueDataM = $revenueDataM->pluck('total')->toArray();
        // Lấy múi giờ hiện tại từ bảng settings
        $currentTimezone = \App\Models\Setting::where('key', 'timezone')->first()->value ?? config('app.timezone');
        // Lấy lịch sử giao dịch (tất cả các yêu cầu nạp tiền)
        $transactionHistory = RechargeRequest::with('user')
            ->orderBy('created_at', 'desc')
            //     ->take(10) // Giới hạn 10 giao dịch gần nhất để tránh quá tải
            ->get();
        // Tính tổng doanh thu
        $totalRevenue = RechargeRequest::where('status', 'approved')->sum('amount');
        //Tính doanh thu theo từng user
        $revenueByUser = RechargeRequest::select('user_id')
            ->selectRaw('SUM(amount) as total_revenue')
            ->where('status', 'approved')
            ->groupBy('user_id')
            ->with('user') // Lấy thông tin user liên quan
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->user_id => [
                        'name' => $item->user ? $item->user->name : 'Người dùng không tồn tại',
                        'total_revenue' => $item->total_revenue,
                    ]
                ];
            });
        $revenueLabels = $revenueData->pluck('day')->toArray();

        $revenueData = $revenueData->pluck('total')->toArray();
        if (empty($revenueLabels)) {
            $revenueLabels = ['Chưa có dữ liệu'];
            $revenueData = [0];
        }

        // Lấy danh sách người dùng đã gửi tin nhắn
        $chatUsers = User::whereHas('messages', function ($query) {
            $query->whereNotNull('message');
        })->get();
        //Lịch sử giao dịch trang admin
        $classifications = \App\Models\Classification::with('user')->get();
        //dd($classifications);
        //dữ liệu từ bảng ceramics
        $ceramics = Ceramic::all();
        //Chính sách và điều khoản
        $terms = \App\Models\TermsAndConditions::first();
        // Lấy trạng thái CAPTCHA từ cột mới
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->recaptcha_enabled == 1) : false;
        //Liên hệ
        $contacts = \App\Models\Contact::latest()->get();
        //stats cho 4 tab tổng quan
        $userTrend = $this->getTrendData(User::class, 'created_at');
        $rechargeTrend = $this->getTrendData(RechargeRequest::class, 'created_at', ['status' => 'pending']);
        $revenueTrend = $this->getTrendData(RechargeRequest::class, 'created_at', ['status' => 'approved'], 'amount');
        $ratingTrend = $this->getTrendData(User::class, 'updated_at', [], 'rating');
        //cập nhật tin tức
        $news = News::orderBy('created_at', 'desc')->take(6)->get();

        // Tổng số lượt nhận diện (tokens_used) từ bảng users
        $totalTokenUsed = User::sum('tokens_used'); // Lấy tổng từ cột tokens_used trong bảng users
        // Xu hướng sử dụng token (trend data) trong 7 ngày

        $packages = RechargePackage::all();
        $llmModel = Setting::where('key', 'llm_model')->first();
        $llmApiKey = Setting::where('key', 'llm_api_key')->first();
        $availableModels = ['Gemini', 'OpenAI'];
        // Thêm cài đặt model nhận diện
        try {
            $latestApk = Apk::latest()->first();
        } catch (\Exception $e) {
            Log::error('Error fetching latest APK: ' . $e->getMessage());
            $latestApk = null;
        }
        $pendingRequests = $rechargeRequests->count();
        $recognitionModel = Setting::where('key', 'recognition_model')->first();
        $recognitionModelClass = Setting::where('key', 'recognition_model_class')->first();
        $recognitionModelPath = Setting::where('key', 'recognition_model_path')->first();
        $availableRecognitionModels = ['default', 'xception']; // Danh sách model khả dụng

        // Calculate revenue trend (e.g., daily revenue over the last 7 days)
        $days = 7; // Last 7 days
        $revenueTrendLabels = [];
        $revenueTrendValues = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenueTrendLabels[] = now()->subDays($i)->format('d/m'); // Format as "dd/mm"
            $revenue = RechargeRequest::where('status', 'approved')
                ->whereDate('created_at', $date)
                ->sum('amount'); // Sum the 'amount' for approved requests
            $revenueTrendValues[] = $revenue;
        }
        // Calculate token trend (e.g., weekly token usage over the last 7 weeks)
        $weeks = 7; // Last 7 weeks
        $tokenTrendLabels = [];
        $tokenTrendValues = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek(); // Start of the week
            $endOfWeek = now()->subWeeks($i)->endOfWeek(); // End of the week
            $weekLabel = $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m'); // Format as "dd/mm - dd/mm"
            $tokenTrendLabels[] = $weekLabel;
            $tokensUsed = User::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->sum('tokens_used'); // Sum the 'tokens_used' for users updated in this week
            $tokenTrendValues[] = $tokensUsed;
        }
        $tokenTrend = ['labels' => $tokenTrendLabels, 'values' => $tokenTrendValues];
        $revenueTrend = ['labels' => $revenueTrendLabels, 'values' => $revenueTrendValues];

        return view('admin', compact('users', 'optimizationSetting', 'revenueLabelsM', 'revenueDataM', 'pendingRequests', 'recognitionModel', 'recognitionModelPath', 'recognitionModelClass', 'recognitionModelPath', 'latestApk', 'metadata', 'isSystemInfoEnabled', 'llmModel', 'llmApiKey', 'availableModels', 'rechargeRequests', 'packages', 'totalTokenUsed', 'tokenTrend', 'fastapistats', 'totalRevenue', 'averageRating', 'revenueLabels', 'revenueData', 'chatUsers', 'transactionHistory', 'ceramics', 'currentTimezone', 'classifications', 'terms', 'recaptchaEnabled', 'revenueByUser', 'currentTheme', 'contacts', 'userTrend', 'rechargeTrend', 'revenueTrend', 'ratingTrend', 'chatUsers', 'news', 'approvedRequests', 'rejectedRequests', 'activeUsers', 'inactiveUsers', 'laravelStats', 'isSystemInfoEnabled', 'alerts', 'recognitionModel', 'availableRecognitionModels'));
    }
    public function sendChatMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:500',
        ]);
        Message::create([
            'user_id' => $request->user_id,
            'admin_id' => auth()->id(),
            'message' => $request->message,
        ]);
        return redirect()->back()->with('success', 'Đã gửi tin nhắn.');
    }
    public function getChat($userId)
    {
        $messages = Message::where('user_id', $userId)->orderBy('created_at')->get();
        return response()->json(['messages' => $messages]);
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin',
            'tokens' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive', // Validate status
        ]);
        // Cập nhật dữ liệu
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'tokens' => $request->input('tokens'),
            'status' => $request->input('status'), // Update status
        ]);
        // Xóa dòng $user->save() vì update() đã tự động lưu
        return redirect()->route('admin.index')->with('success', 'Cập nhật người dùng thành công!');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Người dùng đã được xóa thành công.');
    }


    public function approveRecharge($id)
    {
        $request = RechargeRequest::findOrFail($id);
        if ($request->status !== 'pending') {
            return redirect()->route('admin.index')->with('error', 'Yêu cầu này đã được xử lý!');
        }

        $user = User::findOrFail($request->user_id);
        $user->tokens += $request->requested_tokens;
        $user->save();

        RechargeHistory::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'tokens_added' => $request->requested_tokens,
            'approved_at' => now(),
            // 'proof_image' => $request->proof_image,
        ]);

        $request->update(['status' => 'approved']);

        return redirect()->route('admin.index')->with('success', 'Yêu cầu nạp tiền đã được xác nhận!');
    }
    // Lưu món đồ gốm mới
    public function storeCeramic(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
        ]);

        // Thêm tiền tố 'ceramics/' nếu có giá trị trong 'image'
        if (!empty($validated['image'])) {
            $validated['image'] = 'ceramics/' . ltrim($validated['image'], '/'); // Loại bỏ dấu '/' nếu có
        }

        // Lưu vào cơ sở dữ liệu
        Ceramic::create($validated);

        // Chuyển hướng về trang quản trị với thông báo thành công
        return redirect()->route('admin.index')->with('success', 'Thêm món đồ gốm thành công!');
    }

    // Cập nhật thông tin món đồ gốm
    public function updateCeramic(Request $request, $id)
    {
        $ceramic = Ceramic::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
        ]);
        $ceramic->update($validated);
        return redirect()->route('admin.index')->with('success', 'Cập nhật món đồ gốm thành công!');
    }
    // Xóa món đồ gốm
    public function deleteCeramic($id)
    {
        $ceramic = Ceramic::findOrFail($id);
        $ceramic->delete();
        return redirect()->route('admin.index')->with('success', 'Xóa món đồ gốm thành công!');
    }
    //Settings thay đổi múi giờ
    public function updateTimezone(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'timezone' => 'required|string|timezone',
        ]);
        // Lưu múi giờ vào bảng settings
        Setting::updateOrCreate(
            ['key' => 'timezone'],
            ['value' => $request->timezone]
        );
        // Cập nhật múi giờ cho ứng dụng
        config(['app.timezone' => $request->timezone]);
        date_default_timezone_set($request->timezone);
        // Chuyển hướng về trang trước đó với thông báo thành công
        return redirect()->back()->with('timezone_success', 'Múi giờ đã được cập nhật thành công!');
    }
    //Lịch sử giao dịch
    public function getRecognitionHistory(Request $request)
    {
        $query = Classification::with('user')
            ->orderBy('created_at', 'desc');
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('method') && $request->method) {
            $query->where('method', $request->method);
        }
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }
        $history = $query->paginate(15);
        return view('admin.dashboard', [
            'recognitionHistory' => $history,
            // Các biến khác...
        ]);
    }
    //Chính sách và điều khoản
    public function terms()
    {
        $terms = TermsAndConditions::first();
        return view('admin.terms', compact('terms'));
    }
    public function updateTerms(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $terms = TermsAndConditions::first();
        if ($terms) {
            $terms->update(['content' => $request->content]);
        } else {
            TermsAndConditions::create(['content' => $request->content]);
        }
        return redirect()->route('admin.index')->with('success', 'Chính sách và điều khoản đã được cập nhật.');
    }
    //Xuât file excel
    public function exportTransactionHistory(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = RechargeRequest::with('user');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $transactions = $query->orderBy('created_at', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Thiết lập tiêu đề
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tên Người Dùng');
        $sheet->setCellValue('C1', 'Số Tiền (VNĐ)');
        $sheet->setCellValue('D1', 'Tokens Yêu Cầu');
        $sheet->setCellValue('E1', 'Trạng Thái');
        $sheet->setCellValue('F1', 'Thời Gian');
        // Thêm dữ liệu
        $row = 2;
        foreach ($transactions as $transaction) {
            $sheet->setCellValue('A' . $row, $transaction->id);
            $sheet->setCellValue('B' . $row, $transaction->user->name ?? 'Người dùng không tồn tại');
            $sheet->setCellValue('C' . $row, number_format($transaction->amount));
            $sheet->setCellValue('D' . $row, $transaction->requested_tokens);
            $sheet->setCellValue('E' . $row, ucfirst($transaction->status));
            $sheet->setCellValue('F' . $row, $transaction->created_at->format('d/m/Y H:i'));
            $row++;
        }
        $writer = new Xlsx($spreadsheet);
        $fileName = 'transaction_history_' . now()->format('Ymd_His') . '.xlsx';
        // Lưu vào file tạm
        $tempFile = tempnam(sys_get_temp_dir(), 'transaction_history');
        $writer->save($tempFile);
        // Trả về file dưới dạng tải xuống
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
    //bật tắt capcha
    public function updateCaptchaSetting(Request $request)
    {
        // Kiểm tra giá trị của checkbox (1 nếu bật, 0 nếu tắt)
        $recaptchaEnabled = $request->has('recaptcha_enabled') ? '1' : '0';
        // Cập nhật hoặc tạo bản ghi trong bảng settings
        Setting::updateOrCreate(
            ['key' => 'recaptcha_enabled'],
            ['recaptcha_enabled' => $recaptchaEnabled]
        );
        // Debug: Kiểm tra giá trị sau khi lưu
        $newValue = Setting::where('key', 'recaptcha_enabled')->first()->value;
        \Log::info("CAPTCHA state updated to: " . $newValue);
        return redirect()->back()->with('captcha_success', 'Cài đặt CAPTCHA đã được cập nhật thành công!');
    }
    //Thống kê số lượt sử dụng
    public function showTokenUsage(User $user)
    {
        $tokenUsages = $user->tokenUsages()->latest()->paginate(10);
        return view('admin.token-usage', compact('user', 'tokenUsages'));
    }
    //Chọn giao diện
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:index,index2',
        ]);
        // Cập nhật hoặc tạo mới setting trong database
        Setting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => $request->theme]
        );
        return redirect()->back()->with('theme_success', 'Giao diện đã được cập nhật thành công!');
    }
    private function getTrendData($model, $dateColumn, $conditions = [], $valueColumn = null)
    {
        $query = $model::query();
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }
        $data = $query
            ->selectRaw("DATE($dateColumn) as date, " . ($valueColumn ? "AVG($valueColumn)" : 'COUNT(*)') . " as value")
            ->where($dateColumn, '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('value', 'date')
            ->toArray();
        $labels = [];
        $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($date)->format('d/m');
            $values[] = $data[$date] ?? ($valueColumn ? null : 0);
        }
        return ['labels' => $labels, 'values' => $values];
    }
    // Hàm hỗ trợ lấy thông tin hệ thống

    public function fastApiStats()
    {
        $optimizationSetting = Setting::where('key', 'system_info_optimization')->first();
        if (!$optimizationSetting || $optimizationSetting->value !== 'enabled') {
            return response()->json(['message' => 'Thông tin hệ thống bị tắt']);
        }
        // Dispatch job mỗi lần gọi API
        FetchFastApiStats::dispatch();
        return response()->json(Cache::get('fastapi_stats', []));
    }
    public function toggleOptimization(Request $request)
    {
        $optimization = $request->has('optimization') ? 'enabled' : 'disabled';
        Setting::updateOrCreate(
            ['key' => 'system_info_optimization'],
            ['value' => $optimization]
        );
        if ($optimization === 'disabled') {
            Cache::forget('laravel_stats');
            Cache::forget('fastapi_stats');
        }
        return redirect()->route('admin.index')->with('success', 'Cập nhật trạng thái tối ưu hóa thành công.');
    }
    //Sao lưu dữ liệu
    public function backup()
    {
        try {
            // Lấy thông tin cấu hình database từ file .env
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            // Ghi log cấu hình để kiểm tra
            \Log::info("Backup config: DB=$dbName, User=$dbUser, Host=$dbHost");
            // Đặt tên tệp sao lưu
            $fileName = 'database_backup_' . date('Ymd_His') . '.sql';
            $filePath = storage_path('app/backups/' . $fileName);
            // Tạo thư mục backups nếu chưa tồn tại
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            // Mở tệp để ghi
            $file = fopen($filePath, 'w');
            // Thêm tiêu đề tệp SQL
            fwrite($file, "-- Database Backup for $dbName\n");
            fwrite($file, "-- Generated on " . now()->toDateTimeString() . "\n");
            fwrite($file, "-- Host: $dbHost\n");
            fwrite($file, "-- MySQL Version: " . DB::selectOne('SELECT VERSION() as version')->version . "\n\n");
            fwrite($file, "SET FOREIGN_KEY_CHECKS = 0;\n\n");
            // Lấy danh sách bảng
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            \Log::info("Found $tableCount tables to backup");
            if ($tableCount == 0) {
                fclose($file);
                throw new \Exception("Cơ sở dữ liệu không có bảng nào để sao lưu.");
            }
            // Duyệt qua từng bảng
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0]; // Lấy tên bảng từ kết quả SHOW TABLES
                // Lấy câu lệnh CREATE TABLE
                $createTable = DB::selectOne("SHOW CREATE TABLE `$tableName`")->{'Create Table'};
                fwrite($file, "-- Table structure for `$tableName`\n");
                fwrite($file, "DROP TABLE IF EXISTS `$tableName`;\n");
                fwrite($file, "$createTable;\n\n");
                // Lấy dữ liệu từ bảng
                $rows = DB::table($tableName)->get();
                $rowCount = $rows->count();
                if ($rowCount > 0) {
                    fwrite($file, "-- Dumping data for `$tableName`\n");
                    $columns = array_keys((array) $rows->first());
                    // Chuẩn bị câu lệnh INSERT
                    $insertBase = "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES\n";
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = array_map(function ($value) {
                            return $value === null ? 'NULL' : DB::getPdo()->quote($value);
                        }, array_values((array) $row));
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    // Ghi dữ liệu vào tệp
                    fwrite($file, $insertBase);
                    fwrite($file, implode(",\n", $values) . ";\n\n");
                } else {
                    fwrite($file, "-- No data in `$tableName`\n\n");
                }
                \Log::info("Backed up table: $tableName, Rows: $rowCount");
            }
            // Thêm lệnh khôi phục kiểm tra khóa ngoại
            fwrite($file, "SET FOREIGN_KEY_CHECKS = 1;\n");
            // Đóng tệp
            fclose($file);
            // Kiểm tra kích thước tệp
            $fileSize = filesize($filePath);
            \Log::info("Backup file created: $filePath, Size: $fileSize bytes");
            if ($fileSize < 10) {
                throw new \Exception("Tệp sao lưu rỗng hoặc không chứa dữ liệu.");
            }
            // Trả về tệp tải xuống và xóa sau khi gửi
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Sao lưu dữ liệu thất bại: ' . $e->getMessage());
        }
    }

    public function updateLLMSettings(Request $request)
    {
        $request->validate([
            'llm_model' => 'required|string|in:Gemini,OpenAI',
            'llm_api_key' => 'required|string|max:500',
        ]);

        try {
            // Lưu vào cơ sở dữ liệu
            Setting::updateOrCreate(
                ['key' => 'llm_model'],
                ['value' => $request->llm_model]
            );

            Setting::updateOrCreate(
                ['key' => 'llm_api_key'],
                ['value' => $request->llm_api_key]
            );

            // Gửi yêu cầu đến FastAPI
            $apiKey = env('FASTAPI_KEY');
            $response = Http::withHeaders([
                'api-key' => $apiKey
            ])->post('http://localhost:55001/update-llm', [
                        'model' => $request->llm_model,
                        'api_key' => $request->llm_api_key
                    ]);

            if ($response->status() !== 200) {
                \Log::error('FastAPI LLM update failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect()->back()->with('llm_error', 'Cập nhật mô hình thất bại: ' . $response->body());
            }

            return redirect()->back()->with('llm_success', 'Cài đặt mô hình hệ thống đã được cập nhật thành công!');
        } catch (\Exception $e) {
            \Log::error('LLM settings update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('llm_error', 'Lỗi khi cập nhật cài đặt: ' . $e->getMessage());
        }
    }
    // Phương thức mới để cập nhật cài đặt model nhận diện
    public function updateRecognitionModel(Request $request)
    {
        $request->validate([
            'recognition_model' => 'required|string|in:default,xception',
            'model_file' => 'nullable|file|max:512000|mimes:json', // Hỗ trợ file JSON tối đa 500MB
        ]);

        try {
            // Ánh xạ giữa recognition_model và giá trị mong đợi trong request
            $modelTypeMap = [
                'default' => '66',
                'xception' => '67',
            ];
            $expectedModelType = $modelTypeMap[$request->recognition_model];

            // Gửi yêu cầu đến FastAPI để chuyển đổi mô hình
            $apiKey = env('FASTAPI_KEY');
            $switchResponse = Http::withHeaders([
                'api-key' => $apiKey,
            ])->post('http://localhost:55001/switch-model', [
                        'model_type' => $expectedModelType,
                    ]);

            if ($switchResponse->failed()) {
                Log::error('FastAPI recognition model switch failed', [
                    'status' => $switchResponse->status(),
                    'body' => $switchResponse->body(),
                ]);
                return redirect()->back()->with('recognition_model_error', 'Cập nhật mô hình nhận diện thất bại: ' . $switchResponse->body());
            }

            // Xử lý phản hồi từ FastAPI
            $switchResponseBody = $switchResponse->json();
            $status = $switchResponseBody['status'] ?? null;

            if ($status !== 'success') {
                Log::warning('FastAPI response status is not success', [
                    'status' => $status,
                    'body' => $switchResponseBody,
                ]);
                return redirect()->back()->with('recognition_model_error', 'Chuyển đổi mô hình không thành công: Trạng thái không hợp lệ.');
            }

            // Xử lý phần tải file (nếu có)
            if ($request->hasFile('model_file')) {
                $file = $request->file('model_file');
                $filePath = $file->storeAs('models', $file->getClientOriginalName(), 'public'); // Lưu file vào storage/public/models

                Log::info('Model file uploaded successfully', ['path' => $filePath]);

                // Gửi file đến FastAPI (nếu cần)
                $uploadResponse = Http::attach(
                    'model_file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->withHeaders([
                            'api-key' => $apiKey,
                        ])->post('http://localhost:55001/switch-model', [
                            'model_type' => $expectedModelType,
                        ]);

                if ($uploadResponse->failed()) {
                    Log::error('Model file upload to FastAPI failed', [
                        'status' => $uploadResponse->status(),
                        'body' => $uploadResponse->body(),
                    ]);
                    return redirect()->back()->with('recognition_model_error', 'Tải lên file mô hình thất bại: ' . $uploadResponse->body());
                }
            }

            // Lưu cài đặt mô hình nếu thành công
            Setting::updateOrCreate(
                ['key' => 'recognition_model'],
                ['value' => $request->recognition_model]
            );

            $successMessage = $switchResponseBody['message'] ?? 'Cài đặt mô hình nhận diện đã được cập nhật thành công!';
            return redirect()->back()->with('recognition_model_success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Recognition model settings update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('recognition_model_error', 'Lỗi khi cập nhật cài đặt: ' . $e->getMessage());
        }
    }
    public function uploadModel(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'model_file' => 'required|file|mimes:h5,pth,pt,onnx|max:512000', // Tối đa 500MB
            'model_class' => [
                'required',
                'string',
                'regex:/^[\w\s]+(,\s*[\w\s]+)*$/', // Định dạng: "Class1, Class2, Class3"
                function ($attribute, $value, $fail) {
                    // Tách chuỗi thành mảng các class
                    $classes = array_map('trim', explode(',', $value));
                    // Loại bỏ các phần tử rỗng
                    $classes = array_filter($classes);
                    // Kiểm tra số lượng class (ít nhất 1 class)
                    if (count($classes) < 1) {
                        $fail('Danh sách class không được rỗng.');
                    }
                    // Kiểm tra từng class
                    foreach ($classes as $class) {
                        if (strlen($class) < 1 || strlen($class) > 255) {
                            $fail('Mỗi class phải có độ dài từ 1 đến 255 ký tự.');
                        }
                    }
                },
            ],
        ]);

        try {
            // Lấy file từ request
            $file = $request->file('model_file');
            $filePath = $file->getPathname();
            $fileName = $file->getClientOriginalName();
            $modelClass = $validated['model_class'];

            // Kiểm tra file có tồn tại và đọc được không
            if (!file_exists($filePath) || !is_readable($filePath)) {
                Log::error('File không tồn tại hoặc không đọc được', ['file_path' => $filePath]);
                return redirect()->back()->with('upload_model_error', 'File không tồn tại hoặc không thể đọc.');
            }

            // Đọc nội dung file vào bộ nhớ để tránh lỗi khi gửi
            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                Log::error('Không thể đọc nội dung file', ['file_path' => $filePath]);
                return redirect()->back()->with('upload_model_error', 'Không thể đọc nội dung file.');
            }

            // Gửi yêu cầu đến FastAPI endpoint /upload-model
            $apiKey = env('FASTAPI_KEY');
            $uploadResponse = Http::timeout(600)
                ->withHeaders([
                    'api_key' => $apiKey,
                ])
                ->attach(
                    'file',
                    $fileContent,
                    $fileName
                )
                ->post('http://localhost:55001/upload-model', [
                    'model_class' => $modelClass,
                ]);

            if ($uploadResponse->failed()) {
                Log::error('FastAPI model upload failed', [
                    'status' => $uploadResponse->status(),
                    'body' => $uploadResponse->body(),
                ]);
                return redirect()->back()->with('upload_model_error', 'Upload mô hình thất bại: ' . $uploadResponse->body());
            }

            // Xử lý phản hồi từ FastAPI
            $uploadResponseBody = $uploadResponse->json();
            $status = $uploadResponseBody['status'] ?? null;

            if ($status !== 'success') {
                Log::warning('FastAPI response status is not success', [
                    'status' => $status,
                    'body' => $uploadResponseBody,
                ]);
                return redirect()->back()->with('upload_model_error', 'Upload mô hình không thành công: Trạng thái không hợp lệ.');
            }

            // Lưu thông tin vào cơ sở dữ liệu
            $modelPath = $uploadResponseBody['model_path'] ?? null;
            if (!$modelPath) {
                return redirect()->back()->with('upload_model_error', 'Không nhận được đường dẫn mô hình từ FastAPI.');
            }

            Setting::updateOrCreate(
                ['key' => 'custom_model_path'],
                ['value' => $modelPath]
            );
            Setting::updateOrCreate(
                ['key' => 'custom_model_class'],
                ['value' => $modelClass]
            );

            $successMessage = $uploadResponseBody['message'] ?? 'Upload mô hình thành công! Model đã được lưu tại: ' . $modelPath;
            return redirect()->back()->with('upload_model_success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Model upload error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('upload_model_error', 'Lỗi khi upload mô hình: ' . $e->getMessage());
        }
    }

    // public function updateRecognitionModel(Request $request)
    // {
    //     // Xác thực dữ liệu đầu vào
    //     $validated = $request->validate([
    //         'recognition_model' => 'required|string|in:default,xception,custom',
    //         'model_file' => 'required_if:recognition_model,custom|file|mimes:h5,pth,pt,onnx|max:1024000', // Tối đa 100MB
    //         'model_class' => 'required_if:recognition_model,custom|string|max:255',
    //     ]);

    //     try {
    //         // Ánh xạ giữa recognition_model và giá trị mong đợi trong request
    //         $modelTypeMap = [
    //             'default' => '66',
    //             'xception' => '67',
    //             'custom' => null, // Sẽ lấy từ trường model_class
    //         ];

    //         $modelType = $validated['recognition_model'] === 'custom'
    //             ? $validated['model_class']
    //             : $modelTypeMap[$validated['recognition_model']];

    //         // Xử lý file model nếu chọn Custom Model
    //         $modelPath = null;
    //         if ($validated['recognition_model'] === 'custom' && $request->hasFile('model_file')) {
    //             // Tạo thư mục lưu trữ nếu chưa tồn tại
    //             if (!Storage::exists('models')) {
    //                 Storage::makeDirectory('models');
    //             }

    //             // Lưu file model vào thư mục 'storage/app/models'
    //             $file = $request->file('model_file');
    //             $fileName = time() . '_' . $file->getClientOriginalName();
    //             $modelPath = $file->storeAs('models', $fileName);

    //             // Đường dẫn tuyệt đối để gửi đến FastAPI
    //             $absoluteModelPath = storage_path('app/' . $modelPath);
    //         }

    //         // Gửi yêu cầu đến FastAPI
    //         $apiKey = env('FASTAPI_KEY');
    //         $switchResponse = Http::withHeaders([
    //             'api-key' => $apiKey,
    //         ])->post('http://localhost:55001/switch-model', [
    //                     'model_type' => $modelType,
    //                     'model_path' => $modelPath ? $absoluteModelPath : null, // Gửi đường dẫn file nếu có
    //                 ]);

    //         if ($switchResponse->failed()) {
    //             Log::error('FastAPI recognition model switch failed', [
    //                 'status' => $switchResponse->status(),
    //                 'body' => $switchResponse->body(),
    //             ]);
    //             return redirect()->back()->with('recognition_model_error', 'Cập nhật mô hình nhận diện thất bại: ' . $switchResponse->body());
    //         }

    //         // Xử lý phản hồi từ FastAPI
    //         $switchResponseBody = $switchResponse->json();

    //         // Kiểm tra trường "status"
    //         $status = $switchResponseBody['status'] ?? null;
    //         if ($status !== 'success') {
    //             Log::warning('FastAPI response status is not success', [
    //                 'status' => $status,
    //                 'body' => $switchResponseBody,
    //             ]);
    //             return redirect()->back()->with('recognition_model_error', 'Chuyển đổi mô hình không thành công: Trạng thái không hợp lệ.');
    //         }

    //         // Lưu cài đặt vào cơ sở dữ liệu nếu chuyển đổi thành công
    //         Setting::updateOrCreate(
    //             ['key' => 'recognition_model'],
    //             ['value' => $validated['recognition_model']]
    //         );

    //         // Lưu class và đường dẫn file model nếu là Custom Model
    //         if ($validated['recognition_model'] === 'custom') {
    //             Setting::updateOrCreate(
    //                 ['key' => 'recognition_model_class'],
    //                 ['value' => $validated['model_class']]
    //             );
    //             Setting::updateOrCreate(
    //                 ['key' => 'recognition_model_path'],
    //                 ['value' => $modelPath]
    //             );
    //         } else {
    //             // Xóa thông tin class và path nếu không dùng Custom Model
    //             Setting::where('key', 'recognition_model_class')->delete();
    //             Setting::where('key', 'recognition_model_path')->delete();
    //         }

    //         $successMessage = $switchResponseBody['message'] ?? 'Cài đặt mô hình nhận diện đã được cập nhật thành công!';
    //         return redirect()->back()->with('recognition_model_success', $successMessage);
    //     } catch (\Exception $e) {
    //         Log::error('Recognition model settings update error', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return redirect()->back()->with('recognition_model_error', 'Lỗi khi cập nhật cài đặt: ' . $e->getMessage());
    //     }
    // }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string|max:255|unique:metadata,page',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:png,ico|max:2048',
        ]);

        // Kiểm tra và tạo thư mục 'favicons' nếu chưa tồn tại
        if (!Storage::exists('public/favicons')) {
            Storage::makeDirectory('public/favicons');
        }

        // Lưu favicon vào thư mục 'favicons'
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicons', 'public'); // Lưu vào thư mục 'favicons'
            $validated['favicon'] = $path;
        }

        Metadata::create($validated);

        return redirect()->route('admin.index')->with('success', 'Metadata đã được thêm thành công!');
    }
    public function config()
    {
        $apiUrl = Setting::where('key', 'api_url')->first();
        $apiKey = Setting::where('key', 'api_key')->first();

        if ($apiUrl && $apiKey) {
            return redirect()->route('admin.index');
        }

        return view('config');
    }
    public function updateSettings(Request $request)
    {
        // Validate input
        $request->validate([
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        // Update or create settings
        foreach ($request->settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'] ?? '']
            );
        }

        // Clear cache
        Cache::forget('settings');

        return redirect()->route('admin.index')->with('success', 'Cài đặt đã được cập nhật!');
    }
    public function saveConfig(Request $request)
    {
        $request->validate([
            'api_url' => 'required|url',
            'api_key' => 'required|string|min:8',
        ]);

        Setting::updateOrCreate(
            ['key' => 'api_url'],
            ['value' => $request->api_url]
        );
        Setting::updateOrCreate(
            ['key' => 'api_key'],
            ['value' => $request->api_key]
        );

        return redirect()->route('admin.index');
    }
}