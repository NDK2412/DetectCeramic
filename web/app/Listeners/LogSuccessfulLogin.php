<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use App\Models\LoginHistory;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        $user = $event->user;
        LoginHistory::create([
            'user_id' => $user->id,
            'login_time' => now(),
            'ip_address' => Request::ip(),
            'device_info' => Request::header('User-Agent'),
        ]);
    }
}