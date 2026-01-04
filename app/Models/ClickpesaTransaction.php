<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickpesaTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transaction_id',
        'reference',
        'amount',
        'currency',
        'msisdn',
        'provider',
        'status',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];
}
