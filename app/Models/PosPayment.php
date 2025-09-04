<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PosPayment Model
 * 
 * Corresponds to: pos.payments table
 * POS payments
 */
class PosPayment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.payments';
    
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'transaction_reference',
        'payment_details',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'payment_details' => 'json',
        'created_at' => 'datetime',
    ];

    // Payment method constants
    const PAYMENT_METHODS = [
        'CASH',
        'CARD',
        'MOBILE',
        'ROOM_CHARGE'
    ];

    // Relationships
    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
