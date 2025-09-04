<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission Model
 * 
 * Corresponds to: auth.permissions table
 * Available permissions in the system
 */
class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'auth.permissions';
    
    public $timestamps = false;

    protected $fillable = [
        'code',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'auth.role_permissions', 'permission_id', 'role_id');
    }

    public function userCustomPermissions()
    {
        return $this->hasMany(UserCustomPermission::class, 'permission_id');
    }
}
