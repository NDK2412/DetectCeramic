<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'role', 'tokens', 'tokens_used', 'rating', 
        'feedback', 'status', 'api_token', 'phone', 'address', 'id_number', 'passport'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    // Check if user is active
    public function isActive()
    {
        return $this->status === 'active';
    }
}