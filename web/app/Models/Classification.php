<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = ['user_id', 'image_path', 'result', 'created_at', 'llm_response'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
