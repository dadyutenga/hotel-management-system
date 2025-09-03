<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * RoomTypePricing Model
 * 
 * Represents dynamic pricing rules for room types.
 * Allows seasonal pricing, weekend rates, and special event pricing.
 * 
 * Table: room_type_pricing
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: RoomType (roomType)
 */
class RoomTypePricing extends Model implements Auditable
{
    use HasFactory, HasUuids, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $table = 'room_type_pricing';

    protected $fillable = [
        'room_type_id',
        'name',
        'description',
        'price',
        'valid_from',
        'valid_to',
        'days_of_week',
        'min_advance_booking',
        'max_advance_booking',
        'min_stay_duration',
        'max_stay_duration',
        'is_active',
        'priority',
        'conditions',
    ];

    protected $casts = [
        'id' => 'string',
        'room_type_id' => 'string',
        'price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'days_of_week' => 'array',
        'min_advance_booking' => 'integer',
        'max_advance_booking' => 'integer',
        'min_stay_duration' => 'integer',
        'max_stay_duration' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'conditions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'valid_from',
        'valid_to',
    ];

    /**
     * Get the room type this pricing belongs to
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Check if pricing is valid for given dates
     */
    public function isValidForDates($checkIn, $checkOut): bool
    {
        $checkInDate = is_string($checkIn) ? now()->parse($checkIn) : $checkIn;
        $checkOutDate = is_string($checkOut) ? now()->parse($checkOut) : $checkOut;

        // Check date range
        if ($this->valid_from && $checkInDate->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && $checkOutDate->gt($this->valid_to)) {
            return false;
        }

        // Check days of week
        if ($this->days_of_week && count($this->days_of_week) > 0) {
            $currentDate = $checkInDate->copy();
            $validDay = false;
            
            while ($currentDate->lt($checkOutDate)) {
                if (in_array($currentDate->dayOfWeek, $this->days_of_week)) {
                    $validDay = true;
                    break;
                }
                $currentDate->addDay();
            }
            
            if (!$validDay) {
                return false;
            }
        }

        // Check advance booking period
        $daysInAdvance = now()->diffInDays($checkInDate);
        
        if ($this->min_advance_booking && $daysInAdvance < $this->min_advance_booking) {
            return false;
        }

        if ($this->max_advance_booking && $daysInAdvance > $this->max_advance_booking) {
            return false;
        }

        // Check stay duration
        $stayDuration = $checkInDate->diffInDays($checkOutDate);
        
        if ($this->min_stay_duration && $stayDuration < $this->min_stay_duration) {
            return false;
        }

        if ($this->max_stay_duration && $stayDuration > $this->max_stay_duration) {
            return false;
        }

        return true;
    }

    /**
     * Scope for active pricing rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current pricing rules
     */
    public function scopeCurrent($query)
    {
        return $query->where('valid_from', '<=', now())
                    ->where('valid_to', '>=', now());
    }

    /**
     * Scope for pricing by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get days of week display
     */
    public function getDaysOfWeekDisplayAttribute(): string
    {
        if (!$this->days_of_week || count($this->days_of_week) === 0) {
            return 'All days';
        }

        $dayNames = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $days = array_map(function($day) use ($dayNames) {
            return $dayNames[$day] ?? $day;
        }, $this->days_of_week);

        return implode(', ', $days);
    }

    /**
     * Get validity period display
     */
    public function getValidityPeriodAttribute(): string
    {
        if ($this->valid_from && $this->valid_to) {
            return $this->valid_from->format('M d, Y') . ' - ' . $this->valid_to->format('M d, Y');
        } elseif ($this->valid_from) {
            return 'From ' . $this->valid_from->format('M d, Y');
        } elseif ($this->valid_to) {
            return 'Until ' . $this->valid_to->format('M d, Y');
        }

        return 'No date restrictions';
    }
}