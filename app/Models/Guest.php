<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Guest Model
 * 
 * Corresponds to: guests table
 * Guest profiles
 */
class Guest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'guests';

    protected $fillable = [
        'tenant_id',
        'full_name',
        'email',
        'phone',
        'address',
        'nationality',
        'id_type',
        'id_number',
        'date_of_birth',
        'gender',
        'preferences',
        'loyalty_program_info',
        'marketing_consent',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'preferences' => 'json',
        'loyalty_program_info' => 'json',
        'marketing_consent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function contacts()
    {
        return $this->hasMany(GuestContact::class, 'guest_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'guest_id');
    }

    public function groupBookings()
    {
        return $this->hasMany(GroupBooking::class, 'leader_guest_id');
    }

    public function lostFoundItems()
    {
        return $this->hasMany(LostFound::class, 'guest_id');
    }
}
