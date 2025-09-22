<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot the trait and add global scope for tenant isolation
     */
    protected static function bootBelongsToTenant()
    {
        // Add global scope for tenant isolation
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $builder->where('tenant_id', Auth::user()->tenant_id);
            }
        });

        // Automatically set tenant_id when creating new records
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id && !$model->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    /**
     * Define relationship to tenant
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }

    /**
     * Scope query to specific tenant
     */
    public function scopeForTenant(Builder $query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope query to current authenticated user's tenant
     */
    public function scopeForCurrentTenant(Builder $query)
    {
        if (Auth::check() && Auth::user()->tenant_id) {
            return $query->where('tenant_id', Auth::user()->tenant_id);
        }
        
        return $query;
    }

    /**
     * Remove tenant scope temporarily
     */
    public function withoutTenantScope()
    {
        return $this->withoutGlobalScope('tenant');
    }

    /**
     * Check if model belongs to the current user's tenant
     */
    public function belongsToCurrentTenant(): bool
    {
        if (!Auth::check() || !Auth::user()->tenant_id) {
            return false;
        }

        return $this->tenant_id === Auth::user()->tenant_id;
    }

    /**
     * Validate tenant ownership before operations
     */
    public function validateTenantOwnership(): void
    {
        if (!$this->belongsToCurrentTenant()) {
            abort(403, 'Unauthorized access to resource.');
        }
    }
}