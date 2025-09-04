<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * CorporateAccount Model
 * 
 * Corresponds to: res.corporate_accounts table
 * Corporate clients
 */
class CorporateAccount extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'res.corporate_accounts';

    protected $fillable = [
        'tenant_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'billing_address',
        'tax_id',
        'credit_limit',
        'payment_terms',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function groupBookings()
    {
        return $this->hasMany(GroupBooking::class, 'corporate_account_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'corporate_account_id');
    }
}
