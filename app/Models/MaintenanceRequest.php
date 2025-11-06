<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MaintenanceRequest Model
 * 
 * Corresponds to: ops.maintenance_requests table
 * Maintenance tickets
 */
class MaintenanceRequest extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'maintenance_requests';

    protected $fillable = [
        'property_id',
        'room_id',
        'location_details',
        'issue_type',
        'description',
        'reported_by',
        'assigned_to',
        'status',
        'priority',
        'reported_at',
        'assigned_at',
        'started_at',
        'completed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'OPEN',
        'ASSIGNED',
        'IN_PROGRESS',
        'ON_HOLD',
        'COMPLETED',
        'CANCELLED'
    ];

    // Priority constants
    const PRIORITIES = [
        'LOW',
        'MEDIUM',
        'HIGH',
        'URGENT'
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

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Many-to-many: Multiple staff can be assigned to a maintenance request
     */
    public function assignedStaff()
    {
        return $this->belongsToMany(User::class, 'maintenance_request_staff', 'maintenance_request_id', 'user_id')
            ->using(MaintenanceRequestStaff::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }
}
