<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SeasonalRate Model
 * 
 * Corresponds to: core.seasonal_rates table
 * Seasonal adjustments to rates
 */
class SeasonalRate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'seasonal_rates';
    
    public $timestamps = false;

    protected $fillable = [
        'rate_plan_id',
        'start_date',
        'end_date',
        'adjustment_factor',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'adjustment_factor' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function ratePlan()
    {
        return $this->belongsTo(RatePlan::class, 'rate_plan_id');
    }
}
