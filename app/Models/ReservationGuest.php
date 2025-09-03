<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * ReservationGuest Model
 * 
 * Represents additional guests in a reservation beyond the primary guest.
 * Stores guest information for group bookings and family reservations.
 * 
 * Table: reservation_guests
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Reservation (reservation)
 */
class ReservationGuest extends Model implements Auditable
{
    use HasFactory, HasUuids, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'reservation_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'nationality',
        'id_type',
        'id_number',
        'is_child',
        'special_needs',
        'dietary_restrictions',
    ];

    protected $casts = [
        'id' => 'string',
        'reservation_id' => 'string',
        'date_of_birth' => 'date',
        'is_child' => 'boolean',
        'special_needs' => 'array',
        'dietary_restrictions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'date_of_birth',
    ];

    // Gender options
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHER = 'other';

    // ID types
    const ID_PASSPORT = 'passport';
    const ID_NATIONAL_ID = 'national_id';
    const ID_DRIVERS_LICENSE = 'drivers_license';
    const ID_OTHER = 'other';

    /**
     * Get the reservation this guest belongs to
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get age attribute
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Scope for children
     */
    public function scopeChildren($query)
    {
        return $query->where('is_child', true);
    }

    /**
     * Scope for adults
     */
    public function scopeAdults($query)
    {
        return $query->where('is_child', false);
    }

    /**
     * Get gender display name
     */
    public function getGenderDisplayAttribute(): string
    {
        $genders = [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];

        return $genders[$this->gender] ?? 'Not specified';
    }

    /**
     * Get ID type display name
     */
    public function getIdTypeDisplayAttribute(): string
    {
        $idTypes = [
            self::ID_PASSPORT => 'Passport',
            self::ID_NATIONAL_ID => 'National ID',
            self::ID_DRIVERS_LICENSE => 'Driver\'s License',
            self::ID_OTHER => 'Other',
        ];

        return $idTypes[$this->id_type] ?? 'Not specified';
    }
}