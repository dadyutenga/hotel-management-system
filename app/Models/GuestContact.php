<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * GuestContact Model
 * 
 * Corresponds to: guest_contacts table
 * Additional contact details for guests
 */
class GuestContact extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'guest_contacts';
    
    public $timestamps = false;

    protected $fillable = [
        'guest_id',
        'type',
        'value',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Contact type constants
    const TYPES = [
        'PHONE',
        'EMAIL',
        'ADDRESS'
    ];

    // Relationships
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
}
