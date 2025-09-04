<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * TenantStaffLimit Model
 * 
 * Corresponds to: core.tenant_staff_limits table
 * Defines the maximum number of staff allowed per role
 */
class TenantStaffLimit extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core.tenant_staff_limits';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'role',
        'max_count',
    ];

    protected $casts = [
        'max_count' => 'integer',
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
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
