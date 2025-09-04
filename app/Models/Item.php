<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Item Model
 * 
 * Corresponds to: inv.items table
 * Inventory items
 */
class Item extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inv.items';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'description',
        'category_id',
        'uom_id',
        'cost_price',
        'selling_price',
        'is_stockable',
        'is_sellable',
        'is_active',
        'low_stock_threshold',
        'barcode',
    ];

    protected $casts = [
        'cost_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'low_stock_threshold' => 'decimal:2',
        'is_stockable' => 'boolean',
        'is_sellable' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'item_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'item_id');
    }

    public function goodsReceiptItems()
    {
        return $this->hasMany(GoodsReceiptItem::class, 'item_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function stocktakeItems()
    {
        return $this->hasMany(StocktakeItem::class, 'item_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'item_id');
    }

    public function posOrderItems()
    {
        return $this->hasMany(PosOrderItem::class, 'item_id');
    }
}
