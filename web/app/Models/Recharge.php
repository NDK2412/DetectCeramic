<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $fillable = ['user_id', 'amount', 'requested_tokens', 'status', 'proof_image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}