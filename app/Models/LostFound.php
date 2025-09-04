<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LostFound Model
 * 
 * Corresponds to: ops.lost_found table
 * Lost and found items
 */
class LostFound extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.lost_found';

    protected $fillable = [
        'property_id',
        'item_name',
        'description',
        'location_found',
        'room_id',
        'found_by',
        'found_date',
        'status',
        'guest_id',
        'claimed_date',
        'claimed_by',
        'claimed_id_type',
        'claimed_id_number',
        'notes',
    ];

    protected $casts = [
        'found_date' => 'date',
        'claimed_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'FOUND',
        'CLAIMED',
        'DISPOSED'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function foundBy()
    {
        return $this->belongsTo(User::class, 'found_by');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
}
