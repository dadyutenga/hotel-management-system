<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PurchaseOrderItem Model
 * 
 * Corresponds to: inv.purchase_order_items table
 * Items in purchase orders
 */
class PurchaseOrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.purchase_order_items';
    
    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'item_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'tax_percent',
        'line_total',
        'received_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'line_total' => 'decimal:4',
        'received_quantity' => 'decimal:4',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function goodsReceiptItems()
    {
        return $this->hasMany(GoodsReceiptItem::class, 'po_item_id');
    }
}
