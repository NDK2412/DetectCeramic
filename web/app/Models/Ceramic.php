<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ceramic extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'category',
        'origin',
    ];
}