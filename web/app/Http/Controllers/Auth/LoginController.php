<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Setting;
class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm()
    {
        $recaptchaEnabled = \App\Models\Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->value == '1') : false;

        return view('login', compact('recaptchaEnabled'));
    }

    public function login(Request $request)
    {
       
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->value == '1') : false;
        // Tìm người dùng dựa trên email trước khi xác thực
       
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        if ($recaptchaEnabled) {
            if ($request->has('g-recaptcha-response') && !empty($request->input('g-recaptcha-response'))) {
                // Kiểm tra tính hợp lệ của CAPTCHA
                $validator = validator(['g-recaptcha-response' => $request->input('g-recaptcha-response')], [
                    'g-recaptcha-response' => 'required|captcha'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'CAPTCHA không hợp lệ. Vui lòng thử lại.',
                    ])->onlyInput('email');
                }
            } else {
                // Nếu không có g-recaptcha-response hoặc rỗng
                return back()->withErrors([
                    'g-recaptcha-response' => 'Vui lòng tích vào CAPTCHA để xác nhận.',
                ])->onlyInput('email');
            }
        }
        $request->validate($rules);
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        // Nếu tài khoản không tồn tại hoặc không hoạt động, trả về lỗi ngay lập tức
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

        // Nếu tài khoản hoạt động, tiến hành xác thực
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Nếu xác thực thất bại (mật khẩu sai, v.v.)
        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
    

}