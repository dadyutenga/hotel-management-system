<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RoomInspection Model
 * 
 * Corresponds to: ops.room_inspections table
 * Room inspection records
 */
class RoomInspection extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'room_inspections';
    
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'inspector_id',
        'inspection_date',
        'cleanliness_score',
        'maintenance_score',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'cleanliness_score' => 'integer',
        'maintenance_score' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
