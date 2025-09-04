<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * HousekeepingTask Model
 * 
 * Corresponds to: ops.housekeeping_tasks table
 * Housekeeping assignments
 */
class HousekeepingTask extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.housekeeping_tasks';

    protected $fillable = [
        'property_id',
        'room_id',
        'assigned_to',
        'task_type',
        'status',
        'priority',
        'notes',
        'scheduled_date',
        'scheduled_time',
        'started_at',
        'completed_at',
        'verified_at',
        'verified_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'time',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Task type constants
    const TASK_TYPES = [
        'DAILY_CLEAN',
        'DEEP_CLEAN',
        'TURNDOWN',
        'INSPECTION',
        'OTHER'
    ];

    // Status constants
    const STATUSES = [
        'PENDING',
        'IN_PROGRESS',
        'COMPLETED',
        'VERIFIED',
        'CANCELLED'
    ];

    // Priority constants
    const PRIORITIES = [
        'LOW',
        'MEDIUM',
        'HIGH'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
