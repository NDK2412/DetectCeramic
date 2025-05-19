<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    protected $table = 'metadata';
    protected $fillable = ['page', 'title', 'description', 'keywords', 'favicon'];
}