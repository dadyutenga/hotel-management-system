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
 * Hotel Model
 * 
 * Represents a hotel property within a tenant's portfolio.
 * Each hotel belongs to a tenant and contains multiple rooms, amenities, and services.
 * 
 * Table: hotels
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Tenant (tenant)
 * - hasMany: Room (rooms)
 * - hasMany: Reservation (reservations)
 * - hasMany: Service (services)
 * - belongsToMany: Amenity (amenities)
 */
class Hotel extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'star_rating',
        'total_rooms',
        'check_in_time',
        'check_out_time',
        'policies',
        'location_coordinates',
        'images',
        'is_active',
        'manager_id',
    ];

    protected $casts = [
        'id' => 'string',
        'tenant_id' => 'string',
        'manager_id' => 'string',
        'star_rating' => 'integer',
        'total_rooms' => 'integer',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'policies' => 'array',
        'location_coordinates' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'check_in_time',
        'check_out_time',
        'deleted_at',
    ];

    /**
     * Get the tenant that owns the hotel
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the hotel manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all rooms in this hotel
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get all reservations for this hotel
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get all services offered by this hotel
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get all amenities available at this hotel
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenities')
                    ->withPivot('is_free', 'price', 'description')
                    ->withTimestamps();
    }

    /**
     * Get available rooms for given dates
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
            });
        });
    }

    /**
     * Get occupancy rate for a given period
     */
    public function getOccupancyRate($startDate, $endDate): float
    {
        $totalRooms = $this->total_rooms;
        $totalDays = now()->parse($startDate)->diffInDays(now()->parse($endDate));
        $totalRoomNights = $totalRooms * $totalDays;

        $occupiedRoomNights = $this->reservations()
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum(\DB::raw('DATEDIFF(check_out_date, check_in_date)'));

        return $totalRoomNights > 0 ? ($occupiedRoomNights / $totalRoomNights) * 100 : 0;
    }

    /**
     * Scope for active hotels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full address attribute
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]));
    }

    /**
     * Get star rating as string
     */
    public function getStarRatingDisplayAttribute(): string
    {
        return str_repeat('★', $this->star_rating) . str_repeat('☆', 5 - $this->star_rating);
    }
}