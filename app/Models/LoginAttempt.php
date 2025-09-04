<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LoginAttempt Model
 * 
 * Corresponds to: auth.login_attempts table
 * Track login attempts for security
 */
class LoginAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'auth.login_attempts';
    
    public $timestamps = false;

    protected $fillable = [
        'username',
        'ip_address',
        'user_agent',
        'success',
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
    ];
}
