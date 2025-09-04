<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JournalItem Model
 * 
 * Corresponds to: fin.journal_items table
 * Journal entry line items
 */
class JournalItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fin.journal_items';
    
    public $timestamps = false;

    protected $fillable = [
        'journal_id',
        'account_id',
        'debit',
        'credit',
        'description',
    ];

    protected $casts = [
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
