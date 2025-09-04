<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * TimeClock Model
 * 
 * Corresponds to: ops.time_clock table
 * Track employee attendance
 */
class TimeClock extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.time_clock';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'notes',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Calculate worked hours
    public function getWorkedHoursAttribute()
    {
        if ($this->clock_in && $this->clock_out) {
            return $this->clock_out->diffInHours($this->clock_in);
        }
        return null;
    }
}
