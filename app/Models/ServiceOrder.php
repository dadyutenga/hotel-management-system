<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * ServiceOrder Model
 * 
 * Represents orders for hotel services (room service, spa bookings, etc.).
 * Links guests to services with order details and status tracking.
 * 
 * Table: service_orders
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Reservation (reservation)
 * - belongsTo: Room (room)
 * - belongsTo: Service (service)
 * - belongsTo: User (user - guest)
 * - belongsTo: User (assignedTo - staff member)
 * - hasMany: ServiceOrderItem (items)
 */
class ServiceOrder extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'reservation_id',
        'room_id',
        'service_id',
        'user_id',
        'assigned_to',
        'order_number',
        'status',
        'priority',
        'total_amount',
        'quantity',
        'duration',
        'scheduled_for',
        'completed_at',
        'cancelled_at',
        'special_instructions',
        'guest_notes',
        'staff_notes',
        'cancellation_reason',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'id' => 'string',
        'reservation_id' => 'string',
        'room_id' => 'string',
        'service_id' => 'string',
        'user_id' => 'string',
        'assigned_to' => 'string',
        'total_amount' => 'decimal:2',
        'quantity' => 'integer',
        'duration' => 'integer',
        'scheduled_for' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'scheduled_for',
        'completed_at',
        'cancelled_at',
        'deleted_at',
    ];

    // Order statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the reservation this order belongs to
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the room this order is for
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the service being ordered
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user (guest) who placed this order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the staff member assigned to this order
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all items in this service order
     */
    public function items(): HasMany
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])
               && ($this->scheduled_for === null || $this->scheduled_for->isFuture());
    }

    /**
     * Check if order can be completed
     */
    public function canBeCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_for 
               && $this->scheduled_for->isPast() 
               && !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Get estimated completion time
     */
    public function getEstimatedCompletionAttribute()
    {
        if ($this->scheduled_for && $this->duration) {
            return $this->scheduled_for->addMinutes($this->duration);
        }
        return null;
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for in-progress orders
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope for orders by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for overdue orders
     */
    public function scopeOverdue($query)
    {
        return $query->where('scheduled_for', '<', now())
                    ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope for today's orders
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_for', today());
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];

        return $statuses[$this->status] ?? $this->status;
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
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        $colors = [
            self::PRIORITY_LOW => 'green',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'SO';
        $timestamp = now()->format('ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Boot method to set order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }
}