<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Refund Model
 * 
 * Corresponds to: fin.refunds table
 * Track refunds issued
 */
class Refund extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fin.refunds';
    
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'amount',
        'reason',
        'refund_date',
        'status',
        'refund_transaction_id',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'refund_date' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'PROCESSED',
        'FAILED'
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
