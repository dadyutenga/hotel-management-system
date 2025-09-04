<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReservationRoom Model
 * 
 * Corresponds to: res.reservation_rooms table
 * Rooms assigned to reservations
 */
class ReservationRoom extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'res.reservation_rooms';
    
    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'room_type_id',
        'rate_plan_id',
        'status',
        'guest_name',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'RESERVED',
        'ASSIGNED',
        'OCCUPIED',
        'COMPLETED'
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class, 'rate_plan_id');
    }

    public function roomRates()
    {
        return $this->hasMany(ReservationRoomRate::class, 'reservation_room_id');
    }
}
