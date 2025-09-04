<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Department Model
 * 
 * Corresponds to: ops.departments table
 * Hotel departments
 */
class Department extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.departments';

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'head_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'ops.employee_departments', 'department_id', 'user_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
}
