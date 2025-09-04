<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FolioItem Model
 * 
 * Corresponds to: fin.folio_items table
 * Line items in folios
 */
class FolioItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fin.folio_items';
    
    public $timestamps = false;

    protected $fillable = [
        'folio_id',
        'description',
        'amount',
        'tax_amount',
        'service_charge',
        'type',
        'reference_id',
        'reference_type',
        'posting_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'service_charge' => 'decimal:4',
        'posting_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Type constants
    const TYPES = [
        'ROOM',
        'F&B',
        'SPA',
        'OTHER',
        'DEPOSIT',
        'REFUND'
    ];

    // Relationships
    public function folio()
    {
        return $this->belongsTo(Folio::class, 'folio_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
