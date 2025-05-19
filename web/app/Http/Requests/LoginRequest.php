<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;
class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép tất cả người dùng sử dụng request này
    }

    public function rules()
    {
        // Lấy trạng thái CAPTCHA từ bảng settings, sử dụng cột recaptchaEnable
        $recaptchaEnabled = Setting::where('key', 'recaptcha_enabled')->first();
        $recaptchaEnabled = $recaptchaEnabled ? ($recaptchaEnabled->recaptcha_enabled == 1) : false;

        // Debug: Kiểm tra giá trị recaptchaEnabled
        \Log::info("recaptchaEnabled in LoginRequest: " . ($recaptchaEnabled ? 'true' : 'false'));

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];

        // Chỉ yêu cầu xác minh reCAPTCHA nếu được bật
        if ($recaptchaEnabled) {
            $rules['g-recaptcha-response'] = ['required', function ($attribute, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                $result = $response->json();

                if (!$result['success']) {
                    $fail('Xác minh CAPTCHA không thành công. Vui lòng thử lại.');
                }
            }];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'g-recaptcha-response.required' => 'Vui lòng xác minh CAPTCHA.',
        ];
    }
}