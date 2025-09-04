<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Uom Model (Unit of Measurement)
 * 
 * Corresponds to: inv.uoms table
 * Units of measurement
 */
class Uom extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inv.uoms';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'name',
        'symbol',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'uom_id');
    }
}
