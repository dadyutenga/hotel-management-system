<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * RoomMaintenance Model
 * 
 * Represents maintenance activities and records for hotel rooms.
 * Tracks scheduled and completed maintenance tasks.
 * 
 * Table: room_maintenance
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Room (room)
 * - belongsTo: User (reportedBy)
 * - belongsTo: User (assignedTo)
 * - belongsTo: User (completedBy)
 */
class RoomMaintenance extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $table = 'room_maintenance';

    protected $fillable = [
        'room_id',
        'reported_by',
        'assigned_to',
        'completed_by',
        'type',
        'priority',
        'title',
        'description',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'estimated_duration',
        'actual_duration',
        'cost',
        'parts_used',
        'notes',
        'images',
    ];

    protected $casts = [
        'id' => 'string',
        'room_id' => 'string',
        'reported_by' => 'string',
        'assigned_to' => 'string',
        'completed_by' => 'string',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_duration' => 'integer',
        'actual_duration' => 'integer',
        'cost' => 'decimal:2',
        'parts_used' => 'array',
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'scheduled_at',
        'started_at',
        'completed_at',
        'deleted_at',
    ];

    // Maintenance types
    const TYPE_PREVENTIVE = 'preventive';
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_CLEANING = 'cleaning';
    const TYPE_INSPECTION = 'inspection';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Maintenance statuses
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    /**
     * Get the room this maintenance belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who reported this maintenance
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user assigned to this maintenance
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who completed this maintenance
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Check if maintenance is overdue
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_at 
               && $this->scheduled_at->isPast() 
               && !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Get duration difference (actual vs estimated)
     */
    public function getDurationVarianceAttribute(): ?int
    {
        if ($this->actual_duration && $this->estimated_duration) {
            return $this->actual_duration - $this->estimated_duration;
        }
        return null;
    }

    /**
     * Scope for maintenance by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for scheduled maintenance
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope for in-progress maintenance
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for completed maintenance
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for overdue maintenance
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope for maintenance by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for maintenance by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        $types = [
            self::TYPE_PREVENTIVE => 'Preventive',
            self::TYPE_CORRECTIVE => 'Corrective',
            self::TYPE_EMERGENCY => 'Emergency',
            self::TYPE_CLEANING => 'Deep Cleaning',
            self::TYPE_INSPECTION => 'Inspection',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get priority display name
     */
    public function getPriorityDisplayAttribute(): string
    {
        $priorities = [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];

        return $priorities[$this->priority] ?? $this->priority;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ON_HOLD => 'On Hold',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get estimated duration display
     */
    public function getEstimatedDurationDisplayAttribute(): string
    {
        if (!$this->estimated_duration) {
            return 'Not specified';
        }

        $hours = intval($this->estimated_duration / 60);
        $minutes = $this->estimated_duration % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Get actual duration display
     */
    public function getActualDurationDisplayAttribute(): string
    {
        if (!$this->actual_duration) {
            return 'Not recorded';
        }

        $hours = intval($this->actual_duration / 60);
        $minutes = $this->actual_duration % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }
}