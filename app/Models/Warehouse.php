<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Warehouse Model
 * 
 * Corresponds to: inv.warehouses table
 * Inventory storage locations
 */
class Warehouse extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.warehouses';

    protected $fillable = [
        'property_id',
        'name',
        'location',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'warehouse_id');
    }

    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class, 'warehouse_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'warehouse_id');
    }

    public function stocktakes()
    {
        return $this->hasMany(Stocktake::class, 'warehouse_id');
    }
}
