<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargePackage extends Model
{
    protected $table = 'recharge_packages';
    protected $fillable = ['amount', 'tokens', 'description', 'is_active'];
    protected $casts = [
        'is_active' => 'integer',
        'amount' => 'integer',
        'tokens' => 'integer',
    ];

}