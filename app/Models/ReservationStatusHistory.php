<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReservationStatusHistory Model
 * 
 * Corresponds to: res.reservation_status_history table
 * Track reservation status changes
 */
class ReservationStatusHistory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'res.reservation_status_history';
    
    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'old_status',
        'new_status',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
