<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Superadmin Model
 * 
 * Corresponds to: superadmins table
 * Superadmin users who manage the entire system
 */
class Superadmin extends Authenticatable
{
    use HasFactory, HasUuids;

    protected $table = 'superadmins';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Map password_hash to password for Laravel authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
