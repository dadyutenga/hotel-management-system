<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * RatePlan Model
 * 
 * Corresponds to: core.rate_plans table
 * Different rate plans for room types
 */
class RatePlan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'core.rate_plans';

    protected $fillable = [
        'room_type_id',
        'name',
        'description',
        'rate',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function seasonalRates()
    {
        return $this->hasMany(SeasonalRate::class, 'rate_plan_id');
    }

    public function reservationRooms()
    {
        return $this->hasMany(ReservationRoom::class, 'rate_plan_id');
    }
}
