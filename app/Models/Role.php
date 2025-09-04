<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 * 
 * Corresponds to: auth.roles table
 * Predefined system roles
 */
class Role extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'auth.roles';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Role constants
    const ROLES = [
        'DIRECTOR',
        'MANAGER',
        'SUPERVISOR',
        'ACCOUNTANT',
        'BAR_TENDER',
        'RECEPTIONIST',
        'HOUSEKEEPER'
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'auth.role_permissions', 'role_id', 'permission_id');
    }
}
