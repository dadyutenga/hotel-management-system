<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ItemCategory Model
 * 
 * Corresponds to: inv.item_categories table
 * Item categories
 */
class ItemCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.item_categories';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'parent_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function parent()
    {
        return $this->belongsTo(ItemCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ItemCategory::class, 'parent_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
