<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RoomFeature Model
 * 
 * Corresponds to: core.room_features table
 * Features available in rooms
 */
class RoomFeature extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'core.room_features';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'core.room_features_map', 'feature_id', 'room_id');
    }
}
