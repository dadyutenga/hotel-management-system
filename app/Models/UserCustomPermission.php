<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * UserCustomPermission Model
 * 
 * Corresponds to: auth.user_custom_permissions table
 * Override or add permissions for specific users
 */
class UserCustomPermission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'auth.user_custom_permissions';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'permission_id',
        'is_granted',
        'granted_by',
    ];

    protected $casts = [
        'is_granted' => 'boolean',
        'granted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
