<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * ServiceItem Model
 * 
 * Represents individual items within a service (menu items, spa treatments, etc.).
 * Used for services that have multiple options or components.
 * 
 * Table: service_items
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Service (service)
 * - hasMany: ServiceOrderItem (orderItems)
 */
class ServiceItem extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price',
        'cost',
        'category',
        'sku',
        'is_available',
        'preparation_time',
        'ingredients',
        'allergens',
        'nutritional_info',
        'image',
        'sort_order',
        'tags',
    ];

    protected $casts = [
        'id' => 'string',
        'service_id' => 'string',
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_available' => 'boolean',
        'preparation_time' => 'integer',
        'ingredients' => 'array',
        'allergens' => 'array',
        'nutritional_info' => 'array',
        'sort_order' => 'integer',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    /**
     * Get the service this item belongs to
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get order items for this service item
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->cost > 0) {
            return (($this->price - $this->cost) / $this->price) * 100;
        }
        return 0;
    }

    /**
     * Get profit amount
     */
    public function getProfitAttribute(): float
    {
        return $this->price - $this->cost;
    }

    /**
     * Check if item has allergens
     */
    public function hasAllergens(): bool
    {
        return $this->allergens && count($this->allergens) > 0;
    }

    /**
     * Get preparation time display
     */
    public function getPreparationTimeDisplayAttribute(): string
    {
        if (!$this->preparation_time) {
            return 'Not specified';
        }

        $hours = intval($this->preparation_time / 60);
        $minutes = $this->preparation_time % 60;

        if ($hours > 0) {
            $timeStr = $hours . 'h';
            if ($minutes > 0) {
                $timeStr .= ' ' . $minutes . 'm';
            }
            return $timeStr;
        }

        return $minutes . ' minutes';
    }

    /**
     * Scope for available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for items by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for items with tags
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get allergens display
     */
    public function getAllergensDisplayAttribute(): string
    {
        if (!$this->allergens || count($this->allergens) === 0) {
            return 'None';
        }

        return implode(', ', $this->allergens);
    }

    /**
     * Get tags display
     */
    public function getTagsDisplayAttribute(): string
    {
        if (!$this->tags || count($this->tags) === 0) {
            return 'None';
        }

        return implode(', ', $this->tags);
    }
}