<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

/**
 * Tenant Model
 * 
 * Represents a tenant in the multi-tenant hotel management system.
 * Each tenant represents a hotel chain, individual hotel, or hotel management company.
 * 
 * Table: tenants
 * Primary Key: UUID
 * 
 * Relationships:
 * - hasMany: Hotel (hotels)
 * - hasMany: User (users)
 * - hasMany: TenantSetting (settings)
 */
class Tenant extends SpatieTenant
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'domain',
        'database',
        'company_name',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'timezone',
        'currency',
        'settings',
        'is_active',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'id' => 'string',
        'settings' => 'array',
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'subscription_expires_at',
        'deleted_at',
    ];

    /**
     * Get all hotels belonging to this tenant
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    /**
     * Get all users belonging to this tenant
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get tenant settings
     */
    public function tenantSettings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    /**
     * Scope for active tenants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get tenant's main currency
     */
    public function getCurrencyAttribute(): string
    {
        return $this->attributes['currency'] ?? 'USD';
    }

    /**
     * Get tenant's timezone
     */
    public function getTimezoneAttribute(): string
    {
        return $this->attributes['timezone'] ?? 'UTC';
    }
}