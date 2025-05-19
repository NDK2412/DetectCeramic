<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RechargeRequest;
use App\Models\RechargeHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Models\RechargePackage;
use Illuminate\Support\Facades\Log;

class RechargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rechargeRequests = RechargeRequest::all();
        $packages = RechargePackage::where('is_active', true)->get();
        $rechargeHistory = RechargeHistory::where('user_id', Auth::id())
            ->orderBy('approved_at', 'desc')
            ->get();
        $requests = RechargeRequest::where('user_id', Auth::id())->get();
        $messages = Message::where('user_id', Auth::id())->orderBy('created_at')->get();

        $pendingRequestsCount = $requests->where('status', 'pending')->count();
        $approvedRequestsCount = $requests->where('status', 'approved')->count();
        $totalAmount = $rechargeHistory->sum('amount');
        $totalTokens = $rechargeHistory->sum('tokens_added');

        return view('recharge', compact(
            'requests',
            'messages',
            'rechargeHistory',
            'pendingRequestsCount',
            'approvedRequestsCount',
            'totalAmount',
            'totalTokens',
            'packages',
            'rechargeRequests'
        ));
    }

    public function submit(Request $request)
    {
        try {
            $package = RechargePackage::findOrFail($request->package_id);
            $amount = $package->amount;
            $tokens = $package->tokens;

            $request->validate([
                'package_id' => 'required|exists:recharge_packages,id',
                'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Kiểm tra file hợp lệ
            $file = $request->file('proof_image');
            if (!$file->isValid()) {
                Log::error('File ảnh không hợp lệ: ' . $file->getClientOriginalName());
                return redirect()->back()->with('error', 'File ảnh không hợp lệ, vui lòng thử lại!');
            }

            // Tạo tên file duy nhất
            $fileName = 'proof_' . time() . '_' . $file->hashName();
            $proofPath = 'proof_images/' . $fileName;

            // Lưu trực tiếp vào proof_images
            $fileContent = file_get_contents($file->getRealPath());
            Storage::disk('public')->put($proofPath, $fileContent);
            Log::info('Ảnh chứng minh được lưu tại: ' . $proofPath);

            // Kiểm tra file đã lưu thành công
            if (!Storage::disk('public')->exists($proofPath)) {
                Log::error('Lỗi khi lưu file ảnh: ' . $proofPath);
                return redirect()->back()->with('error', 'Lỗi khi lưu ảnh chứng minh, vui lòng thử lại!');
            }

            RechargeRequest::create([
                'user_id' => Auth::id(),
                'amount' => $amount,
                'requested_tokens' => $tokens,
                'proof_image' => $proofPath,
            ]);

            return redirect()->route('recharge.index')->with('success', 'Yêu cầu nạp tiền đã được gửi, chờ admin xác nhận!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý upload ảnh: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi gửi yêu cầu nạp tiền: ' . $e->getMessage());
        }
    }
    public function submitAdr(Request $request)
    {
        try {
            // Lấy token từ header Authorization
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu token xác thực'
                ], 401);
            }

            // Tìm user dựa trên api_token
            $user = \App\Models\User::where('api_token', $token)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không hợp lệ'
                ], 401);
            }

            // Validate dữ liệu
            $validated = $request->validate([
                'package_id' => 'required|exists:recharge_packages,id',
                'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Lấy gói nạp
            $package = \App\Models\RechargePackage::findOrFail($validated['package_id']);
            $amount = $package->amount;
            $tokens = $package->tokens;

            // Kiểm tra và lưu ảnh
            $file = $request->file('proof_image');
            if (!$file->isValid()) {
                \Log::error('File ảnh không hợp lệ: ' . $file->getClientOriginalName());
                return response()->json([
                    'success' => false,
                    'message' => 'File ảnh không hợp lệ'
                ], 422);
            }

            $fileName = 'proof_' . time() . '_' . $file->hashName();
            $proofPath = 'proof_images/' . $fileName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($proofPath, file_get_contents($file->getRealPath()));

            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($proofPath)) {
                \Log::error('Lỗi lưu ảnh: ' . $proofPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi lưu ảnh chứng minh'
                ], 500);
            }

            // Lưu yêu cầu nạp
            \App\Models\RechargeRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'requested_tokens' => $tokens,
                'status' => 'pending',
                'proof_image' => $proofPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu nạp đã được gửi, chờ xác nhận!'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Lỗi nạp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportReceipt($id)
    {
        try {
            $record = RechargeHistory::findOrFail($id);
            $proofImagePath = $record->proof_image ? public_path('storage/' . $record->proof_image) : null;
            // Log để debug
            Log::info('exportReceipt: proof_image từ database: ' . $record->proof_image);
            Log::info('exportReceipt: proofImagePath: ' . $proofImagePath);

            // Tạm bỏ kiểm tra exists để test hiển thị ảnh
            /*
            if ($proofImagePath && !Storage::disk('public')->exists($record->proof_image)) {
                Log::warning('Ảnh chứng minh không tồn tại tại: ' . $record->proof_image);
                $proofImagePath = null;
            }
            */

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipt', [
                'record' => $record,
                'proof_image' => $proofImagePath,
            ]);

            return $pdf->download('HoaDon_NapTien_' . $id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xuất PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xuất hóa đơn: ' . $e->getMessage());
        }
    }

    public function verify(Request $request)
    {
        $password = $request->input('password');
        $user = Auth::user();

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu không chính xác!'
            ], 401);
        }

        $recaptchaResponse = $request->input('g-recaptcha-response');
        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip()
            ]
        ]);

        $recaptchaData = json_decode($response->getBody(), true);

        if (!$recaptchaData['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Xác nhận CAPTCHA không thành công!'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận thành công!'
        ]);
    }
    //Phần Dành cho android
    //<=======================================================================================================================
    public function getPackages(Request $request)
    {
        try {

            $packages = RechargePackage::where('is_active', true)->get();
            return response()->json([
                'success' => true,
                'packages' => $packages->map(function ($package) {
                    return [
                        'id' => $package->id,
                        'amount' => $package->amount,
                        'tokens' => $package->tokens,
                        'description' => $package->description,
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách gói nạp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách gói nạp: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        try {
            $apiToken = $request->bearerToken();
            if (!$apiToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp api_token.'
                ], 401);
            }

            $user = \App\Models\User::where('api_token', $apiToken)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'api_token không hợp lệ.'
                ], 401);
            }

            $history = \App\Models\RechargeHistory::where('user_id', $user->id)
                ->orderBy('approved_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history->map(function ($record) {
                    // Kiểm tra approved_at trước khi format
                    $approvedAt = null;
                    if ($record->approved_at) {
                        try {
                            $date = new \DateTime($record->approved_at);
                            $approvedAt = $date->format('d/m/Y H:i');
                        } catch (\Exception $e) {
                            \Log::warning('Invalid approved_at format: ' . $record->approved_at);
                        }
                    }
                    return [
                        'id' => $record->id,
                        'amount' => $record->amount,
                        'tokens_added' => $record->tokens_added,
                        'approved_at' => $approvedAt,
                    ];
                })->toArray(),
                'message' => $history->isEmpty() ? 'Chưa có lịch sử nạp' : 'Lấy lịch sử nạp thành công'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi lấy lịch sử nạp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }
}