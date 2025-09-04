<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Stocktake Model
 * 
 * Corresponds to: inv.stocktakes table
 * Physical inventory counts
 */
class Stocktake extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.stocktakes';
    
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'warehouse_id',
        'status',
        'start_date',
        'end_date',
        'notes',
        'created_by',
        'completed_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'DRAFT',
        'IN_PROGRESS',
        'COMPLETED',
        'CANCELLED'
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function stocktakeItems()
    {
        return $this->hasMany(StocktakeItem::class, 'stocktake_id');
    }
}
