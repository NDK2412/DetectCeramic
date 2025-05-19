<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = ['user_id', 'login_time', 'ip_address', 'device_info'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}