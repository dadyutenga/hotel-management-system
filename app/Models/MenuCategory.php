<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MenuCategory Model
 * 
 * Corresponds to: pos.menu_categories table
 * Menu categories
 */
class MenuCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.menu_categories';
    
    public $timestamps = false;

    protected $fillable = [
        'menu_id',
        'name',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }
}
