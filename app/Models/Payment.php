<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Payment Model
 * 
 * Corresponds to: fin.payments table
 * Payments received
 */
class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'fin.payments';

    protected $fillable = [
        'folio_id',
        'invoice_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'payment_date',
        'payment_details',
        'currency',
        'base_amount',
        'fx_rate',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'base_amount' => 'decimal:4',
        'fx_rate' => 'decimal:6',
        'payment_date' => 'datetime',
        'payment_details' => 'json',
        'deleted_at' => 'datetime',
    ];

    // Method constants
    const METHODS = [
        'CASH',
        'BANK',
        'MOBILE',
        'CARD'
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'COMPLETED',
        'FAILED',
        'REFUNDED'
    ];

    // Relationships
    public function folio()
    {
        return $this->belongsTo(Folio::class, 'folio_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'payment_id');
    }
}
