<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * AuditableScope Trait
 * 
 * Provides common scopes for auditable models.
 * Includes scopes for filtering by creation, modification, and user actions.
 */
trait AuditableScope
{
    /**
     * Scope for records created today
     */
    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for records created this week
     */
    public function scopeCreatedThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for records created this month
     */
    public function scopeCreatedThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for records updated today
     */
    public function scopeUpdatedToday(Builder $query): Builder
    {
        return $query->whereDate('updated_at', today());
    }

    /**
     * Scope for records updated this week
     */
    public function scopeUpdatedThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('updated_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for recently created records
     */
    public function scopeRecentlyCreated(Builder $query, $hours = 24): Builder
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for recently updated records
     */
    public function scopeRecentlyUpdated(Builder $query, $hours = 24): Builder
    {
        return $query->where('updated_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for records created between dates
     */
    public function scopeCreatedBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for records updated between dates
     */
    public function scopeUpdatedBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('updated_at', [$startDate, $endDate]);
    }

    /**
     * Scope for ordering by creation date (newest first)
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for ordering by creation date (oldest first)
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }
}