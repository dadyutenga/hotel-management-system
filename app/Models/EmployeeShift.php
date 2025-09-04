<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EmployeeShift Model
 * 
 * Corresponds to: ops.employee_shifts table
 * Assign employees to shifts
 */
class EmployeeShift extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.employee_shifts';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
