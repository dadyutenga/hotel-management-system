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
 * Room Model
 * 
 * Represents individual rooms within a hotel.
 * Each room belongs to a room type and can have multiple reservations over time.
 * 
 * Table: rooms
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Hotel (hotel)
 * - belongsTo: RoomType (roomType)
 * - hasMany: Reservation (reservations)
 * - hasMany: RoomMaintenance (maintenanceRecords)
 * - hasMany: ServiceOrder (serviceOrders)
 */
class Room extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'special_features',
        'last_cleaned_at',
        'last_maintenance_at',
        'notes',
        'is_accessible',
        'view_type',
        'balcony',
        'smoking_allowed',
    ];

    protected $casts = [
        'id' => 'string',
        'hotel_id' => 'string',
        'room_type_id' => 'string',
        'floor' => 'integer',
        'special_features' => 'array',
        'last_cleaned_at' => 'datetime',
        'last_maintenance_at' => 'datetime',
        'is_accessible' => 'boolean',
        'balcony' => 'boolean',
        'smoking_allowed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_cleaned_at',
        'last_maintenance_at',
        'deleted_at',
    ];

    // Room statuses
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_OUT_OF_ORDER = 'out_of_order';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CLEANING = 'cleaning';
    const STATUS_DIRTY = 'dirty';

    // View types
    const VIEW_CITY = 'city';
    const VIEW_OCEAN = 'ocean';
    const VIEW_MOUNTAIN = 'mountain';
    const VIEW_GARDEN = 'garden';
    const VIEW_COURTYARD = 'courtyard';
    const VIEW_POOL = 'pool';

    /**
     * Get the hotel that owns this room
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room type
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get all reservations for this room
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get current reservation (if any)
     */
    public function currentReservation()
    {
        return $this->reservations()
            ->where('check_in_date', '<=', now())
            ->where('check_out_date', '>', now())
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();
    }

    /**
     * Get maintenance records for this room
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(RoomMaintenance::class);
    }

    /**
     * Get service orders for this room
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Check if room is available for given dates
     */
    public function isAvailableForDates($checkIn, $checkOut): bool
    {
        if ($this->status !== self::STATUS_AVAILABLE) {
            return false;
        }

        return !$this->reservations()
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->exists();
    }

    /**
     * Get next checkout date
     */
    public function getNextCheckoutAttribute()
    {
        return $this->reservations()
            ->where('check_out_date', '>', now())
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_out_date')
            ->first()?->check_out_date;
    }

    /**
     * Get next checkin date
     */
    public function getNextCheckinAttribute()
    {
        return $this->reservations()
            ->where('check_in_date', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->first()?->check_in_date;
    }

    /**
     * Scope for available rooms
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope for occupied rooms
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    /**
     * Scope for out of order rooms
     */
    public function scopeOutOfOrder($query)
    {
        return $query->where('status', self::STATUS_OUT_OF_ORDER);
    }

    /**
     * Scope for accessible rooms
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Get room status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_OUT_OF_ORDER => 'Out of Order',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_CLEANING => 'Cleaning',
            self::STATUS_DIRTY => 'Needs Cleaning',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get view type display name
     */
    public function getViewTypeDisplayAttribute(): string
    {
        $viewTypes = [
            self::VIEW_CITY => 'City View',
            self::VIEW_OCEAN => 'Ocean View',
            self::VIEW_MOUNTAIN => 'Mountain View',
            self::VIEW_GARDEN => 'Garden View',
            self::VIEW_COURTYARD => 'Courtyard View',
            self::VIEW_POOL => 'Pool View',
        ];

        return $viewTypes[$this->view_type] ?? 'Standard View';
    }

    /**
     * Get full room identifier
     */
    public function getFullRoomNumberAttribute(): string
    {
        return $this->floor ? "Floor {$this->floor} - Room {$this->room_number}" : "Room {$this->room_number}";
    }
}