<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PosOrderItem Model
 * 
 * Corresponds to: pos.order_items table
 * Items in orders
 */
class PosOrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.order_items';

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'item_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'PREPARING',
        'SERVED',
        'CANCELLED'
    ];

    // Relationships
    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
