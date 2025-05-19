<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'cache';
    protected $primaryKey = 'key'; // Khóa chính là cột key
    public $incrementing = false; // Không tự tăng
    protected $keyType = 'string'; // Kiểu chuỗi
    protected $fillable = ['key', 'value', 'expiration'];
    public $timestamps = false; // Không dùng timestamps
}