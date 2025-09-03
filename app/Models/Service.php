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
 * Service Model
 * 
 * Represents services offered by the hotel (room service, spa, restaurant, etc.).
 * Services can be ordered by guests and have associated pricing.
 * 
 * Table: services
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Hotel (hotel)
 * - hasMany: ServiceOrder (serviceOrders)
 * - hasMany: ServiceItem (serviceItems)
 */
class Service extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'hotel_id',
        'name',
        'description',
        'category',
        'base_price',
        'pricing_type',
        'is_active',
        'is_available_24h',
        'operating_hours',
        'contact_phone',
        'contact_extension',
        'location',
        'capacity',
        'advance_booking_required',
        'booking_lead_time',
        'cancellation_policy',
        'special_instructions',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'string',
        'hotel_id' => 'string',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_available_24h' => 'boolean',
        'operating_hours' => 'array',
        'capacity' => 'integer',
        'advance_booking_required' => 'boolean',
        'booking_lead_time' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Service categories
    const CATEGORY_ROOM_SERVICE = 'room_service';
    const CATEGORY_HOUSEKEEPING = 'housekeeping';
    const CATEGORY_CONCIERGE = 'concierge';
    const CATEGORY_TRANSPORTATION = 'transportation';
    const CATEGORY_SPA = 'spa';
    const CATEGORY_RESTAURANT = 'restaurant';
    const CATEGORY_BAR = 'bar';
    const CATEGORY_LAUNDRY = 'laundry';
    const CATEGORY_BUSINESS = 'business';
    const CATEGORY_RECREATION = 'recreation';

    // Pricing types
    const PRICING_FIXED = 'fixed';
    const PRICING_HOURLY = 'hourly';
    const PRICING_PER_ITEM = 'per_item';
    const PRICING_PER_PERSON = 'per_person';

    /**
     * Get the hotel that offers this service
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get all orders for this service
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Get all items/menu items for this service
     */
    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    /**
     * Check if service is currently available
     */
    public function isCurrentlyAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_available_24h) {
            return true;
        }

        // Check operating hours
        $now = now();
        $currentTime = $now->format('H:i');
        $dayOfWeek = strtolower($now->format('l'));

        if (isset($this->operating_hours[$dayOfWeek])) {
            $hours = $this->operating_hours[$dayOfWeek];
            if (isset($hours['open']) && isset($hours['close'])) {
                return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
            }
        }

        return false;
    }

    /**
     * Calculate price for given quantity and duration
     */
    public function calculatePrice($quantity = 1, $duration = 1): float
    {
        switch ($this->pricing_type) {
            case self::PRICING_HOURLY:
                return $this->base_price * $duration * $quantity;
            case self::PRICING_PER_ITEM:
            case self::PRICING_PER_PERSON:
                return $this->base_price * $quantity;
            case self::PRICING_FIXED:
            default:
                return $this->base_price;
        }
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for services by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for 24-hour services
     */
    public function scopeAvailable24h($query)
    {
        return $query->where('is_available_24h', true);
    }

    /**
     * Scope for services that require advance booking
     */
    public function scopeRequiresBooking($query)
    {
        return $query->where('advance_booking_required', true);
    }

    /**
     * Scope for ordered services
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        $categories = [
            self::CATEGORY_ROOM_SERVICE => 'Room Service',
            self::CATEGORY_HOUSEKEEPING => 'Housekeeping',
            self::CATEGORY_CONCIERGE => 'Concierge',
            self::CATEGORY_TRANSPORTATION => 'Transportation',
            self::CATEGORY_SPA => 'Spa & Wellness',
            self::CATEGORY_RESTAURANT => 'Restaurant',
            self::CATEGORY_BAR => 'Bar & Lounge',
            self::CATEGORY_LAUNDRY => 'Laundry & Dry Cleaning',
            self::CATEGORY_BUSINESS => 'Business Services',
            self::CATEGORY_RECREATION => 'Recreation',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get pricing type display name
     */
    public function getPricingTypeDisplayAttribute(): string
    {
        $types = [
            self::PRICING_FIXED => 'Fixed Price',
            self::PRICING_HOURLY => 'Per Hour',
            self::PRICING_PER_ITEM => 'Per Item',
            self::PRICING_PER_PERSON => 'Per Person',
        ];

        return $types[$this->pricing_type] ?? $this->pricing_type;
    }

    /**
     * Get available categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ROOM_SERVICE => 'Room Service',
            self::CATEGORY_HOUSEKEEPING => 'Housekeeping',
            self::CATEGORY_CONCIERGE => 'Concierge',
            self::CATEGORY_TRANSPORTATION => 'Transportation',
            self::CATEGORY_SPA => 'Spa & Wellness',
            self::CATEGORY_RESTAURANT => 'Restaurant',
            self::CATEGORY_BAR => 'Bar & Lounge',
            self::CATEGORY_LAUNDRY => 'Laundry & Dry Cleaning',
            self::CATEGORY_BUSINESS => 'Business Services',
            self::CATEGORY_RECREATION => 'Recreation',
        ];
    }
}