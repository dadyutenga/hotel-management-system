<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PasswordResetToken Model
 * 
 * Corresponds to: auth.password_reset_tokens table
 * Store password reset tokens
 */
class PasswordResetToken extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'auth.password_reset_tokens';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
