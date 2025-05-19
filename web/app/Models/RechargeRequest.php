<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeRequest extends Model
{
    protected $table = 'recharge_requests'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'user_id',
        'amount',
        'requested_tokens',
        'status',
        'proof_image',
    ];

    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}