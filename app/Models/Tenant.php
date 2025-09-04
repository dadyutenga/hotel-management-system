<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * Tenant Model
 * 
 * Corresponds to: core.tenants table
 * Represents businesses using the system
 */
class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes, CentralConnection;

    protected $table = 'core.tenants';

    protected $fillable = [
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'certification_proof',
        'business_type',
        'subscription_level',
        'subscription_expires_at',
        'base_currency',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Business type constants
    const BUSINESS_TYPES = [
        'HOTEL',
        'LODGE',
        'RESTAURANT',
        'BAR',
        'PUB'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(Superadmin::class, 'created_by');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'tenant_id');
    }

    public function staffLimits()
    {
        return $this->hasMany(TenantStaffLimit::class, 'tenant_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function roomFeatures()
    {
        return $this->hasMany(RoomFeature::class, 'tenant_id');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'tenant_id');
    }

    public function corporateAccounts()
    {
        return $this->hasMany(CorporateAccount::class, 'tenant_id');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'tenant_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'tenant_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'tenant_id');
    }

    public function uoms()
    {
        return $this->hasMany(Uom::class, 'tenant_id');
    }

    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class, 'tenant_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'tenant_id');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'tenant_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'tenant_id');
    }

    public function systemSettings()
    {
        return $this->hasMany(SystemSetting::class, 'tenant_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'tenant_id');
    }
}
