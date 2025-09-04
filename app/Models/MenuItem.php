<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MenuItem Model
 * 
 * Corresponds to: pos.menu_items table
 * Items on menus
 */
class MenuItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.menu_items';

    protected $fillable = [
        'menu_id',
        'category_id',
        'item_id',
        'name',
        'description',
        'price',
        'discount_price',
        'is_available',
        'display_order',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'discount_price' => 'decimal:4',
        'is_available' => 'boolean',
        'display_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function posOrderItems()
    {
        return $this->hasMany(PosOrderItem::class, 'menu_item_id');
    }
}
