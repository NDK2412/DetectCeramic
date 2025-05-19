<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'image',
        'source_url',
        'created_at',
        'updated_at',
    ];
}