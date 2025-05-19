<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeHistory extends Model
{
    protected $table = 'recharge_history'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'user_id',
        'amount',
        'tokens_added',
        'approved_at',
        'proof_image',
    ];
    public $timestamps = false;
    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}