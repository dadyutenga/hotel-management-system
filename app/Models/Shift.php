<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Shift Model
 * 
 * Corresponds to: ops.shifts table
 * Employee work shifts
 */
class Shift extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.shifts';
    
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'name',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'time',
        'end_time' => 'time',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'shift_id');
    }
}
