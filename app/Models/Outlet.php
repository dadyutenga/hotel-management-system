<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Outlet Model
 * 
 * Corresponds to: pos.outlets table
 * Sales outlets
 */
class Outlet extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.outlets';

    protected $fillable = [
        'property_id',
        'name',
        'type',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Type constants
    const TYPES = [
        'RESTAURANT',
        'BAR',
        'CAFE',
        'SPA',
        'GIFT_SHOP'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function terminals()
    {
        return $this->hasMany(Terminal::class, 'outlet_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'outlet_id');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'outlet_id');
    }
}
