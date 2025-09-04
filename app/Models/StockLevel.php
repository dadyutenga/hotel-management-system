<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StockLevel Model
 * 
 * Corresponds to: inv.stock_levels table
 * Current stock levels by warehouse
 */
class StockLevel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.stock_levels';
    
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'warehouse_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
