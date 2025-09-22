<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Reservation Model
 * 
 * Corresponds to: res.reservations table
 * Individual reservations
 */
class Reservation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'reservations';

    protected $fillable = [
        'property_id',
        'guest_id',
        'group_booking_id',
        'corporate_account_id',
        'status',
        'arrival_date',
        'departure_date',
        'adults',
        'children',
        'total_amount',
        'discount_amount',
        'discount_reason',
        'special_requests',
        'notes',
        'source',
        'external_reference',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'total_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'CONFIRMED',
        'CHECKED_IN',
        'CHECKED_OUT',
        'CANCELLED',
        'NO_SHOW',
        'HOLD'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function groupBooking()
    {
        return $this->belongsTo(GroupBooking::class, 'group_booking_id');
    }

    public function corporateAccount()
    {
        return $this->belongsTo(CorporateAccount::class, 'corporate_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reservationRooms()
    {
        return $this->hasMany(ReservationRoom::class, 'reservation_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(ReservationStatusHistory::class, 'reservation_id');
    }

    public function folio()
    {
        return $this->hasOne(Folio::class, 'reservation_id');
    }
}
