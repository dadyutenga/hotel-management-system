<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Floor Model
 * 
 * Corresponds to: core.floors table
 * Floors within buildings
 */
class Floor extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'core.floors';

    protected $fillable = [
        'building_id',
        'number',
        'description',
    ];

    protected $casts = [
        'number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'floor_id');
    }
}
