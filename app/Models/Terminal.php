<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Terminal Model
 * 
 * Corresponds to: pos.terminals table
 * POS terminals
 */
class Terminal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.terminals';
    
    public $timestamps = false;

    protected $fillable = [
        'outlet_id',
        'name',
        'terminal_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'terminal_id');
    }
}
