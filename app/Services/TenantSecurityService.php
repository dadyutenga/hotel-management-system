<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class TenantSecurityService
{
    /**
     * Validate that a resource belongs to the current user's tenant
     *
     * @param mixed $resource
     * @param string $tenantField
     * @return bool
     */
    public static function validateTenantOwnership($resource, string $tenantField = 'tenant_id'): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->tenant_id) {
            return false;
        }

        // Handle direct tenant_id comparison
        if (is_object($resource) && property_exists($resource, $tenantField)) {
            return $resource->{$tenantField} === $user->tenant_id;
        }

        // Handle array access
        if (is_array($resource) && isset($resource[$tenantField])) {
            return $resource[$tenantField] === $user->tenant_id;
        }

        return false;
    }

    /**
     * Validate that a resource belongs to the current user's property and tenant
     *
     * @param mixed $resource
     * @return bool
     */
    public static function validatePropertyOwnership($resource): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        if (!$user->tenant_id || !$user->property_id) {
            return false;
        }

        // Validate both property and tenant ownership
        if (is_object($resource)) {
            $validProperty = property_exists($resource, 'property_id') && 
                           $resource->property_id === $user->property_id;
            
            $validTenant = property_exists($resource, 'tenant_id') && 
                          $resource->tenant_id === $user->tenant_id;
            
            return $validProperty && $validTenant;
        }

        return false;
    }

    /**
     * Abort with 403 if resource doesn't belong to current tenant
     *
     * @param mixed $resource
     * @param string $tenantField
     * @return void
     */
    public static function abortUnlessOwner($resource, string $tenantField = 'tenant_id'): void
    {
        if (!self::validateTenantOwnership($resource, $tenantField)) {
            abort(403, 'Unauthorized access to resource.');
        }
    }

    /**
     * Get the current authenticated user's tenant ID
     *
     * @return string|null
     */
    public static function getCurrentTenantId(): ?string
    {
        return Auth::check() ? Auth::user()->tenant_id : null;
    }

    /**
     * Check if current user has access to a specific tenant
     *
     * @param string $tenantId
     * @return bool
     */
    public static function hasAccessToTenant(string $tenantId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->tenant_id === $tenantId;
    }

    /**
     * Get tenant-scoped query constraint
     *
     * @return array
     */
    public static function getTenantConstraint(): array
    {
        $tenantId = self::getCurrentTenantId();
        
        return $tenantId ? ['tenant_id' => $tenantId] : [];
    }

    /**
     * Validate that all resources in a collection belong to current tenant
     *
     * @param iterable $resources
     * @param string $tenantField
     * @return bool
     */
    public static function validateCollectionTenantOwnership(iterable $resources, string $tenantField = 'tenant_id'): bool
    {
        foreach ($resources as $resource) {
            if (!self::validateTenantOwnership($resource, $tenantField)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Log security violation
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logSecurityViolation(string $message, array $context = []): void
    {
        \Log::warning('Security Violation: ' . $message, array_merge([
            'user_id' => Auth::id(),
            'tenant_id' => self::getCurrentTenantId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ], $context));
    }
}