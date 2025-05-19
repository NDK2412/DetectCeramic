<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apk extends Model
{
    protected $fillable = ['version', 'file_name', 'file_path'];
}