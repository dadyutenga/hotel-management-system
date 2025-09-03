<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * ServiceOrderItem Model
 * 
 * Represents individual items within a service order.
 * Links service orders to specific service items with quantities and pricing.
 * 
 * Table: service_order_items
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: ServiceOrder (serviceOrder)
 * - belongsTo: ServiceItem (serviceItem)
 */
class ServiceOrderItem extends Model implements Auditable
{
    use HasFactory, HasUuids, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'service_order_id',
        'service_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
        'status',
    ];

    protected $casts = [
        'id' => 'string',
        'service_order_id' => 'string',
        'service_item_id' => 'string',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Item statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_SERVED = 'served';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the service order this item belongs to
     */
    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    /**
     * Get the service item
     */
    public function serviceItem(): BelongsTo
    {
        return $this->belongsTo(ServiceItem::class);
    }

    /**
     * Calculate total price based on quantity and unit price
     */
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Scope for items by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending items
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for preparing items
     */
    public function scopePreparing($query)
    {
        return $query->where('status', self::STATUS_PREPARING);
    }

    /**
     * Scope for ready items
     */
    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PREPARING => 'Preparing',
            self::STATUS_READY => 'Ready',
            self::STATUS_SERVED => 'Served',
            self::STATUS_CANCELLED => 'Cancelled',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Boot method to calculate total price
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->calculateTotalPrice();
        });
    }
}