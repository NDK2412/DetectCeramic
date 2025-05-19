<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $fillable = ['name', 'phone', 'email', 'message', 'is_read'];
    public $timestamps = true;
}