<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Room Model
 * 
 * Corresponds to: core.rooms table
 * Individual rooms
 */
class Room extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'core.rooms';

    protected $fillable = [
        'property_id',
        'floor_id',
        'room_type_id',
        'room_number',
        'status',
        'current_rate',
        'notes',
    ];

    protected $casts = [
        'current_rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'OCCUPIED',
        'VACANT',
        'DIRTY',
        'CLEAN',
        'OUT_OF_ORDER',
        'CLEANING_IN_PROGRESS',
        'INSPECTED'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function features()
    {
        return $this->belongsToMany(RoomFeature::class, 'core.room_features_map', 'room_id', 'feature_id');
    }

    public function reservationRooms()
    {
        return $this->hasMany(ReservationRoom::class, 'room_id');
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'room_id');
    }

    public function roomInspections()
    {
        return $this->hasMany(RoomInspection::class, 'room_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'room_id');
    }

    public function lostFoundItems()
    {
        return $this->hasMany(LostFound::class, 'room_id');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'room_id');
    }
}
