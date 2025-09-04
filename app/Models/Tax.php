<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tax Model
 * 
 * Corresponds to: fin.taxes table
 * Tax configuration
 */
class Tax extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fin.taxes';

    protected $fillable = [
        'tenant_id',
        'name',
        'rate',
        'type',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Type constants
    const TYPES = [
        'VAT',
        'SERVICE',
        'OTHER'
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
