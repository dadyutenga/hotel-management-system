<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StocktakeItem Model
 * 
 * Corresponds to: inv.stocktake_items table
 * Items counted in stocktake
 */
class StocktakeItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.stocktake_items';
    
    public $timestamps = false;

    protected $fillable = [
        'stocktake_id',
        'item_id',
        'system_quantity',
        'counted_quantity',
        'notes',
        'counted_at',
        'counted_by',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:4',
        'counted_quantity' => 'decimal:4',
        'counted_at' => 'datetime',
    ];

    // Computed variance attribute
    public function getVarianceAttribute()
    {
        return ($this->counted_quantity ?? 0) - $this->system_quantity;
    }

    // Relationships
    public function stocktake()
    {
        return $this->belongsTo(Stocktake::class, 'stocktake_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function countedBy()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }
}
