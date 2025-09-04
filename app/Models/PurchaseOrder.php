<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PurchaseOrder Model
 * 
 * Corresponds to: inv.purchase_orders table
 * Orders to vendors
 */
class PurchaseOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'inv.purchase_orders';

    protected $fillable = [
        'property_id',
        'vendor_id',
        'po_number',
        'status',
        'order_date',
        'expected_delivery_date',
        'total_amount',
        'delivery_address',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'DRAFT',
        'APPROVED',
        'SENT',
        'RECEIVED',
        'CANCELLED'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id');
    }

    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class, 'po_id');
    }
}
