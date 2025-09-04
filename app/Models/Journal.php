<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Journal Model
 * 
 * Corresponds to: fin.journals table
 * Accounting journal entries
 */
class Journal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'fin.journals';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'date',
        'reference_type',
        'reference_id',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_debit' => 'decimal:4',
        'total_credit' => 'decimal:4',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Status constants
    const STATUSES = [
        'DRAFT',
        'POSTED',
        'REVERSED'
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

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class, 'journal_id');
    }
}
