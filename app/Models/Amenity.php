<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Amenity Model
 * 
 * Represents amenities available at hotels or in specific room types.
 * Can be associated with hotels or room types with pricing information.
 * 
 * Table: amenities
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsToMany: Hotel (hotels)
 * - belongsToMany: RoomType (roomTypes)
 */
class Amenity extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    // Amenity categories
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_BUSINESS = 'business';
    const CATEGORY_RECREATION = 'recreation';
    const CATEGORY_HEALTH = 'health';
    const CATEGORY_FOOD = 'food';
    const CATEGORY_CONNECTIVITY = 'connectivity';
    const CATEGORY_ROOM = 'room';
    const CATEGORY_BATHROOM = 'bathroom';
    const CATEGORY_ENTERTAINMENT = 'entertainment';

    /**
     * Get hotels that have this amenity
     */
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_amenities')
                    ->withPivot('is_free', 'price', 'description')
                    ->withTimestamps();
    }

    /**
     * Get room types that include this amenity
     */
    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'room_type_amenities')
                    ->withPivot('is_included', 'additional_cost')
                    ->withTimestamps();
    }

    /**
     * Scope for active amenities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for amenities by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for ordered amenities
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
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_BUSINESS => 'Business',
            self::CATEGORY_RECREATION => 'Recreation',
            self::CATEGORY_HEALTH => 'Health & Fitness',
            self::CATEGORY_FOOD => 'Food & Dining',
            self::CATEGORY_CONNECTIVITY => 'Connectivity',
            self::CATEGORY_ROOM => 'Room Features',
            self::CATEGORY_BATHROOM => 'Bathroom',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get available categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_GENERAL => 'General',
            self::CATEGORY_BUSINESS => 'Business',
            self::CATEGORY_RECREATION => 'Recreation',
            self::CATEGORY_HEALTH => 'Health & Fitness',
            self::CATEGORY_FOOD => 'Food & Dining',
            self::CATEGORY_CONNECTIVITY => 'Connectivity',
            self::CATEGORY_ROOM => 'Room Features',
            self::CATEGORY_BATHROOM => 'Bathroom',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
        ];
    }
}