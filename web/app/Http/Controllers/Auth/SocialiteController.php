<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user, true);
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'email_verified_at' => now(), // Google verifies email
                    'password' => Hash::make(uniqid()), // Random password since Google won't provide one
                    'role' => 'user', // Default role, adjust as needed
                    'tokens' => 3, // Default value
                    'tokens_used' => 0, // Default value
                    'rating' => null, // Default value
                    'feedback' => null, // Default value
                ]);
                Auth::login($user, true);
            }

            return redirect()->intended('/dashboard'); // Redirect to your desired page
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.']);
        }
    }
}