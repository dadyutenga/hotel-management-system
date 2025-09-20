<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Building Model
 * 
 * Corresponds to: core.buildings table
 * Buildings within a property
 */
class Building extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'buildings';

    protected $fillable = [
        'property_id',
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function floors()
    {
        return $this->hasMany(Floor::class, 'building_id');
    }
}
