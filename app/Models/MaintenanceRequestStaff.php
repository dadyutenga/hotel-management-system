<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * MaintenanceRequestStaff Pivot Model
 * 
 * Corresponds to: maintenance_request_staff pivot table
 * Links maintenance requests to assigned housekeepers
 */
class MaintenanceRequestStaff extends Pivot
{
    protected $table = 'maintenance_request_staff';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maintenance_request_id',
        'user_id',
        'assigned_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the maintenance request.
     */
    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    /**
     * Get the user (housekeeper).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
