<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * GoodsReceipt Model
 * 
 * Corresponds to: inv.goods_receipts table
 * Received goods
 */
class GoodsReceipt extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.goods_receipts';
    
    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'receipt_number',
        'receipt_date',
        'warehouse_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function goodsReceiptItems()
    {
        return $this->hasMany(GoodsReceiptItem::class, 'goods_receipt_id');
    }
}
