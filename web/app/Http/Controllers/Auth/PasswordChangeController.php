<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;

class PasswordChangeController extends Controller
{
    public function showChangeForm()
    {
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->value == '1') : false;

        return view('change-password', compact('recaptchaEnabled'));
    }

    public function change(Request $request)
    {
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->value == '1') : false;

        // Xác thực dữ liệu đầu vào
        $rules = [
            'email' => 'required|email|exists:users,email',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ];

        if ($recaptchaEnabled) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules);

        // Tìm user dựa trên email
        $user = User::where('email', $request->email)->first();

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu cũ không chính xác.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được thay đổi thành công! Vui lòng đăng nhập lại.');
    }
    
}