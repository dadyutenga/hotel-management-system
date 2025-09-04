<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PosOrder Model
 * 
 * Corresponds to: pos.orders table
 * POS orders
 */
class PosOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos.orders';
    
    public $timestamps = false;

    protected $fillable = [
        'outlet_id',
        'terminal_id',
        'folio_id',
        'room_id',
        'order_number',
        'server_id',
        'status',
        'order_type',
        'guest_count',
        'table_number',
        'subtotal',
        'tax_amount',
        'service_charge',
        'discount_amount',
        'total_amount',
        'notes',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
    ];

    protected $casts = [
        'guest_count' => 'integer',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'service_charge' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'OPEN',
        'COMPLETED',
        'CANCELLED'
    ];

    // Order type constants
    const ORDER_TYPES = [
        'DINE_IN',
        'TAKE_AWAY',
        'ROOM_SERVICE'
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }

    public function folio()
    {
        return $this->belongsTo(Folio::class, 'folio_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function server()
    {
        return $this->belongsTo(User::class, 'server_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function posOrderItems()
    {
        return $this->hasMany(PosOrderItem::class, 'order_id');
    }

    public function posPayments()
    {
        return $this->hasMany(PosPayment::class, 'order_id');
    }
}
