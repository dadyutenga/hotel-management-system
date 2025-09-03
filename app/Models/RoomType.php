<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * RoomType Model
 * 
 * Represents different types of rooms available in a hotel (Standard, Deluxe, Suite, etc.).
 * Defines pricing, capacity, and amenities for each room type.
 * 
 * Table: room_types
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Hotel (hotel)
 * - hasMany: Room (rooms)
 * - belongsToMany: Amenity (amenities)
 */
class RoomType extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'base_price',
        'max_occupancy',
        'max_adults',
        'max_children',
        'bed_configuration',
        'room_size',
        'room_features',
        'images',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'string',
        'hotel_id' => 'string',
        'base_price' => 'decimal:2',
        'max_occupancy' => 'integer',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'room_size' => 'decimal:2',
        'room_features' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Bed configurations
    const BED_SINGLE = 'single';
    const BED_DOUBLE = 'double';
    const BED_QUEEN = 'queen';
    const BED_KING = 'king';
    const BED_TWIN = 'twin';
    const BED_SOFA = 'sofa_bed';

    /**
     * Get the hotel that owns this room type
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get all rooms of this type
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get amenities included with this room type
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_type_amenities')
                    ->withPivot('is_included', 'additional_cost')
                    ->withTimestamps();
    }

    /**
     * Get pricing rules for this room type
     */
    public function pricingRules(): HasMany
    {
        return $this->hasMany(RoomTypePricing::class);
    }

    /**
     * Get available rooms of this type for given dates
     */
    public function availableRooms($checkIn, $checkOut)
    {
        return $this->rooms()->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut) {
            $query->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                  ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in_date', '<=', $checkIn)
                         ->where('check_out_date', '>=', $checkOut);
                  });
            })->whereIn('status', ['confirmed', 'checked_in']);
        })->where('status', 'available');
    }

    /**
     * Get price for specific dates (considering seasonal pricing, etc.)
     */
    public function getPriceForDates($checkIn, $checkOut): float
    {
        // Basic implementation - can be extended for seasonal pricing
        $nights = now()->parse($checkIn)->diffInDays(now()->parse($checkOut));
        
        // Check for special pricing rules
        $specialPrice = $this->pricingRules()
            ->where('valid_from', '<=', $checkIn)
            ->where('valid_to', '>=', $checkOut)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->first();

        if ($specialPrice) {
            return $specialPrice->price * $nights;
        }

        return $this->base_price * $nights;
    }

    /**
     * Scope for active room types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for room types ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get bed configuration display name
     */
    public function getBedConfigurationDisplayAttribute(): string
    {
        $bedTypes = [
            self::BED_SINGLE => 'Single Bed',
            self::BED_DOUBLE => 'Double Bed',
            self::BED_QUEEN => 'Queen Bed',
            self::BED_KING => 'King Bed',
            self::BED_TWIN => 'Twin Beds',
            self::BED_SOFA => 'Sofa Bed',
        ];

        return $bedTypes[$this->bed_configuration] ?? $this->bed_configuration;
    }

    /**
     * Get room size with unit
     */
    public function getRoomSizeDisplayAttribute(): string
    {
        return $this->room_size ? $this->room_size . ' sqm' : 'Size not specified';
    }
}