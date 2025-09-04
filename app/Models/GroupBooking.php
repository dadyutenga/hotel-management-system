<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * GroupBooking Model
 * 
 * Corresponds to: res.group_bookings table
 * Group reservations
 */
class GroupBooking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'res.group_bookings';

    protected $fillable = [
        'property_id',
        'name',
        'leader_guest_id',
        'corporate_account_id',
        'total_rooms',
        'arrival_date',
        'departure_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_rooms' => 'integer',
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'CONFIRMED',
        'CANCELLED',
        'COMPLETED'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function leaderGuest()
    {
        return $this->belongsTo(Guest::class, 'leader_guest_id');
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

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'group_booking_id');
    }
}
