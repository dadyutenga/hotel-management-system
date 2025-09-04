<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StockMovement Model
 * 
 * Corresponds to: inv.stock_movements table
 * Track all inventory movements
 */
class StockMovement extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.stock_movements';
    
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'item_id',
        'warehouse_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'reference_type',
        'reference_id',
        'notes',
        'movement_date',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'movement_date' => 'datetime',
    ];

    // Movement type constants
    const MOVEMENT_TYPES = [
        'INCOMING',
        'OUTGOING',
        'TRANSFER',
        'ADJUSTMENT'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
