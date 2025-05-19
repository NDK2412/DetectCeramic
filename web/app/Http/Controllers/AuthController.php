<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;
use App\Models\LoginHistory;
use App\Models\Apk;
class AuthController extends Controller
{
    public function index()
    {
      
        $latestApk = Apk::latest()->first();
        return view('index', compact('latestApk'));
    }

    public function showLoginForm()
    {
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->recaptcha_enabled == '1') : false;
        \Log::info("recaptchaEnabled in showLoginForm: " . ($recaptchaEnabled ? 'true' : 'false'));

        return view('login', compact('recaptchaEnabled'));
    }

    public function login(Request $request)
    {
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->value == '1') : false;
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if ($recaptchaEnabled && empty($request->input('g-recaptcha-response'))) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Vui lòng tích vào CAPTCHA.',
            ])->onlyInput('email');
        }

        if (!$user) {
            return back()->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ])->onlyInput('email');
        }

        if (!$user->isActive()) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'passport' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'id_number' => $request->id_number,
            'passport' => $request->passport,
            'tokens' => 3,
            'status' => 'active',
        ]);

        return redirect()->route('login')->with('success', 'Tài khoản đã được tạo! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function useToken(Request $request)
    {
        $user = Auth::user();
        if ($user->tokens > 0) {
            $user->tokens -= 1;
            $user->tokens_used += 1;
            $user->save();
            return response()->json(['success' => true, 'tokens' => $user->tokens]);
        }
        return response()->json(['success' => false, 'message' => 'Hết lượt dự đoán']);
    }

    public function changeName(Request $request)
    {
        $request->validate([
            'userId' => 'required',
            'name' => 'required|string|min:3|max:255',
        ]);

        $user = User::find($request->userId);

        if (!$user || $user->id !== Auth::id()) {
            return response()->json(['error' => 'Không có quyền thay đổi tên!'], 403);
        }

        $user->name = $request->name;
        $user->save();

        return response()->json(['success' => true]);
    }



    public function apiLogin(Request $request)
    {
        try {
            if (!$request->isJson()) {
                Log::warning('Login attempt with non-JSON content', [
                    'headers' => $request->headers->all(),
                    'body' => $request->all(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Yêu cầu phải có Content-Type: application/json'
                ], 400);
            }

            $credentials = $request->only('email', 'password');

            Log::info('Login attempt', ['email' => $credentials['email']]);

            if (empty($credentials['email']) || empty($credentials['password'])) {
                Log::warning('Missing credentials', ['body' => $credentials]);
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp email và mật khẩu'
                ], 400);
            }

            $user = User::where('email', $credentials['email'])->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                if ($user->status !== 'active') {
                    Log::warning('Inactive user login attempt', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản của bạn đã bị khóa.'
                    ], 403);
                }

                $token = Str::random(60);
                $user->api_token = $token;
                $user->save();
                
                LoginHistory::create([
                    'user_id' => $user->id,
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                    'device_info' => $request->header('User-Agent') ?? 'Unknown'
                ]);
                Log::info('Login successful', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'token' => substr($token, 0, 10) . '...',
                ]);

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'id_number' => $user->id_number,
                        'passport' => $user->passport,
                        'tokens' => $user->tokens,
                        'role' => $user->role
                    ]
                ], 200);
            }

            Log::warning('Invalid login credentials', ['email' => $credentials['email']]);
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không đúng.'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),


                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server khi đăng nhập: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiRegister(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'phone' => 'nullable|string|max:15',
                'address' => 'nullable|string|max:255',
                'id_number' => 'nullable|string|max:20',
                'passport' => 'nullable|string|max:20',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'id_number' => $data['id_number'],
                'passport' => $data['passport'],
                'tokens' => 3,
                'status' => 'active',
                'role' => 'user',
                'api_token' => bin2hex(random_bytes(32)),
            ]);

            return response()->json([
                'success' => true,
                'token' => $user->api_token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'id_number' => $user->id_number,
                    'passport' => $user->passport,
                    'tokens' => $user->tokens,
                    'role' => $user->role,
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('apiRegister error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUser(Request $request)
    {
        try {
            $apiToken = $request->bearerToken();
            if (!$apiToken) {
                Log::warning('No token provided for getUser');
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp api_token.'
                ], 401);
            }

            $user = User::where('api_token', $apiToken)->first();
            if ($user) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'id_number' => $user->id_number,
                        'passport' => $user->passport,
                        'tokens' => $user->tokens,
                        'role' => $user->role
                    ]
                ], 200);
            }

            Log::warning('Invalid token for getUser', [
                'token' => substr($apiToken, 0, 10) . '...',
            ]);
            return response()->json([
                'success' => false,
                'message' => 'api_token không hợp lệ.'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Get user error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)),
                    'tokens' => 3,
                    'status' => 'active',
                    'role' => 'user',
                    'api_token' => bin2hex(random_bytes(32)),
                ]);
            } else {
                $user->api_token = bin2hex(random_bytes(32));
                $user->save();
            }

            Log::info('Google login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->away("ceramicprediction://auth?token={$user->api_token}");
        } catch (\Exception $e) {
            Log::error('handleGoogleCallback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đăng nhập Google: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            return response()->json([
                'success' => true,
                'url' => $url,
            ], 200);
        } catch (\Exception $e) {
            Log::error('redirectToGoogle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi chuyển hướng Google: ' . $e->getMessage(),
            ], 500);
        }
    }
}