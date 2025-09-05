<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Model
 * 
 * Corresponds to: ops.notifications table
 * System notifications
 */
class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';
    
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'superadmin_id',
        'title',
        'message',
        'type',
        'is_read',
        'created_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function superadmin()
    {
        return $this->belongsTo(Superadmin::class, 'superadmin_id');
    }
}
