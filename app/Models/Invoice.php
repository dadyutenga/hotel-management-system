<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Invoice Model
 * 
 * Corresponds to: fin.invoices table
 * Invoices generated from folios
 */
class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'fin.invoices';

    protected $fillable = [
        'folio_id',
        'type',
        'invoice_number',
        'amount',
        'tax_amount',
        'service_charge',
        'total_amount',
        'notes',
        'due_date',
        'pdf_path',
        'currency',
        'base_amount',
        'fx_rate',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'service_charge' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'base_amount' => 'decimal:4',
        'fx_rate' => 'decimal:6',
        'generated_at' => 'datetime',
        'due_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    // Type constants
    const TYPES = [
        'PROFORMA',
        'ACTUAL'
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

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
