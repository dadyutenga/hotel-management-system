<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;
use App\Traits\AuditableScope;
use App\Traits\ReportingHelpers;

/**
 * Reservation Model
 * 
 * Represents a hotel reservation/booking made by a guest.
 * Contains all booking details, dates, pricing, and guest information.
 * 
 * Table: reservations
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Hotel (hotel)
 * - belongsTo: Room (room)
 * - belongsTo: RoomType (roomType)
 * - belongsTo: User (guest)
 * - belongsTo: User (handledBy - staff member)
 * - hasMany: Payment (payments)
 * - hasMany: ServiceOrder (serviceOrders)
 * - hasMany: ReservationGuest (additionalGuests)
 */
class Reservation extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable, AuditableScope, ReportingHelpers;

    protected $fillable = [
        'hotel_id',
        'room_id',
        'room_type_id',
        'guest_id',
        'handled_by',
        'reservation_number',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'room_rate',
        'total_amount',
        'paid_amount',
        'status',
        'booking_source',
        'special_requests',
        'guest_notes',
        'internal_notes',
        'cancellation_reason',
        'cancelled_at',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'payment_status',
        'is_group_booking',
        'group_name',
        'promotional_code',
        'discount_amount',
    ];

    protected $casts = [
        'id' => 'string',
        'hotel_id' => 'string',
        'room_id' => 'string',
        'room_type_id' => 'string',
        'guest_id' => 'string',
        'handled_by' => 'string',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'room_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'special_requests' => 'array',
        'is_group_booking' => 'boolean',
        'discount_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'check_in_date',
        'check_out_date',
        'cancelled_at',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'deleted_at',
    ];

    // Reservation statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    // Payment statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    // Booking sources
    const SOURCE_DIRECT = 'direct';
    const SOURCE_BOOKING_COM = 'booking_com';
    const SOURCE_EXPEDIA = 'expedia';
    const SOURCE_AGODA = 'agoda';
    const SOURCE_PHONE = 'phone';
    const SOURCE_WALK_IN = 'walk_in';

    /**
     * Get the hotel for this reservation
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room for this reservation
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the room type for this reservation
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the guest who made this reservation
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    /**
     * Get the staff member who handled this reservation
     */
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get all payments for this reservation
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all service orders for this reservation
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Get additional guests for this reservation
     */
    public function additionalGuests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    /**
     * Get number of nights
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Get total guests count
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->adults + $this->children;
    }

    /**
     * Check if reservation is active (checked in but not checked out)
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    /**
     * Check if reservation can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]) 
               && $this->check_in_date->isFuture();
    }

    /**
     * Check if guest can check in
     */
    public function canCheckIn(): bool
    {
        return $this->status === self::STATUS_CONFIRMED 
               && $this->check_in_date->isToday();
    }

    /**
     * Check if guest can check out
     */
    public function canCheckOut(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN;
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN);
    }

    /**
     * Scope for upcoming reservations
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED)
                    ->where('check_in_date', '>', now());
    }

    /**
     * Scope for today's arrivals
     */
    public function scopeArrivalsToday($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED)
                    ->whereDate('check_in_date', today());
    }

    /**
     * Scope for today's departures
     */
    public function scopeDeparturesToday($query)
    {
        return $query->where('status', self::STATUS_CHECKED_IN)
                    ->whereDate('check_out_date', today());
    }

    /**
     * Scope for cancelled reservations
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CHECKED_IN => 'Checked In',
            self::STATUS_CHECKED_OUT => 'Checked Out',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_NO_SHOW => 'No Show',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayAttribute(): string
    {
        $statuses = [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PARTIAL => 'Partially Paid',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_REFUNDED => 'Refunded',
        ];

        return $statuses[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Generate unique reservation number
     */
    public static function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $timestamp = now()->format('ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Boot method to set reservation number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = self::generateReservationNumber();
            }
        });
    }
}