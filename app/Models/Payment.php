<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Payment Model
 * 
 * Represents payments made for hotel reservations and services.
 * Tracks payment methods, amounts, and transaction details.
 * 
 * Table: payments
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Reservation (reservation)
 * - belongsTo: User (user - guest or staff who processed)
 * - belongsTo: User (processedBy - staff member)
 */
class Payment extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'processed_by',
        'payment_method',
        'payment_type',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'transaction_id',
        'reference_number',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'paid_at',
        'refunded_at',
        'refund_amount',
        'refund_reason',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'id' => 'string',
        'reservation_id' => 'string',
        'user_id' => 'string',
        'processed_by' => 'string',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'refunded_at',
        'deleted_at',
    ];

    // Payment methods
    const METHOD_CASH = 'cash';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_STRIPE = 'stripe';
    const METHOD_CHECK = 'check';
    const METHOD_GIFT_CARD = 'gift_card';

    // Payment types
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_FULL_PAYMENT = 'full_payment';
    const TYPE_BALANCE = 'balance';
    const TYPE_REFUND = 'refund';
    const TYPE_EXTRA_CHARGES = 'extra_charges';

    // Payment statuses
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Payment gateways
    const GATEWAY_STRIPE = 'stripe';
    const GATEWAY_PAYPAL = 'paypal';
    const GATEWAY_SQUARE = 'square';
    const GATEWAY_MANUAL = 'manual';

    /**
     * Get the reservation this payment belongs to
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the user who made this payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff member who processed this payment
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if payment is refunded
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    /**
     * Check if payment can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this->status === self::STATUS_COMPLETED 
               && $this->refund_amount === null;
    }

    /**
     * Get net amount (amount minus refunds)
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - ($this->refund_amount ?? 0);
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for refunded payments
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * Scope for payments by method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for payments by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        $methods = [
            self::METHOD_CASH => 'Cash',
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_PAYPAL => 'PayPal',
            self::METHOD_STRIPE => 'Stripe',
            self::METHOD_CHECK => 'Check',
            self::METHOD_GIFT_CARD => 'Gift Card',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get payment type display name
     */
    public function getPaymentTypeDisplayAttribute(): string
    {
        $types = [
            self::TYPE_DEPOSIT => 'Deposit',
            self::TYPE_FULL_PAYMENT => 'Full Payment',
            self::TYPE_BALANCE => 'Balance Payment',
            self::TYPE_REFUND => 'Refund',
            self::TYPE_EXTRA_CHARGES => 'Extra Charges',
        ];

        return $types[$this->payment_type] ?? $this->payment_type;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $timestamp = now()->format('ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Boot method to set receipt number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = self::generateReceiptNumber();
            }
        });
    }
}