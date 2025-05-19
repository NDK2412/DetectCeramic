<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    protected $fillable = ['queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'];
    public $timestamps = false; // Không dùng cột timestamps mặc định
}