<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReservationRoomRate Model
 * 
 * Corresponds to: res.reservation_room_rates table
 * Daily rates for reserved rooms
 */
class ReservationRoomRate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'res.reservation_room_rates';

    protected $fillable = [
        'reservation_room_id',
        'date',
        'rate',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function reservationRoom()
    {
        return $this->belongsTo(ReservationRoom::class, 'reservation_room_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
