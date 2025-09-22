<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * RoomType Model
 * 
 * Corresponds to: core.room_types table
 * Types of rooms offered
 */
class RoomType extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'room_types';

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'capacity',
        'base_rate',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'base_rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id');
    }

    public function ratePlans()
    {
        return $this->hasMany(RatePlan::class, 'room_type_id');
    }

    public function reservationRooms()
    {
        return $this->hasMany(ReservationRoom::class, 'room_type_id');
    }
}
