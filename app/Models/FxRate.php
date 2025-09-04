<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FxRate Model
 * 
 * Corresponds to: fin.fx_rates table
 * Exchange rates
 */
class FxRate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fin.fx_rates';
    
    public $timestamps = false;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'effective_date' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
