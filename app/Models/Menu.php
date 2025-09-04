<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Menu Model
 * 
 * Corresponds to: pos.menus table
 * Menus for outlets
 */
class Menu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.menus';

    protected $fillable = [
        'outlet_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'time',
        'end_time' => 'time',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class, 'menu_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }
}
