<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenUsage extends Model
{
    protected $fillable = ['user_id', 'tokens_used', 'description'];

public function user()
{
    return $this->belongsTo(User::class);
}
}
