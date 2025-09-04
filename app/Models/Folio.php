<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Folio Model
 * 
 * Corresponds to: fin.folios table
 * Financial records for reservations
 */
class Folio extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'fin.folios';

    protected $fillable = [
        'reservation_id',
        'status',
        'balance',
        'currency',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'OPEN',
        'CLOSED'
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function folioItems()
    {
        return $this->hasMany(FolioItem::class, 'folio_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'folio_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'folio_id');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'folio_id');
    }
}
