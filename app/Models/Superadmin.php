<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Superadmin Model
 * 
 * Corresponds to: auth.superadmins table
 * Superadmin users who manage the entire system
 */
class Superadmin extends Authenticatable
{
    use HasFactory, HasUuids;

    protected $table = 'auth.superadmins';

    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function createdTenants()
    {
        return $this->hasMany(Tenant::class, 'created_by');
    }
}
