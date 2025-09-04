<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * GoodsReceiptItem Model
 * 
 * Corresponds to: inv.goods_receipt_items table
 * Items received
 */
class GoodsReceiptItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.goods_receipt_items';
    
    public $timestamps = false;

    protected $fillable = [
        'goods_receipt_id',
        'po_item_id',
        'item_id',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
