<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SystemSetting Model
 * 
 * Corresponds to: ops.system_settings table
 * System configuration
 */
class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ops.system_settings';

    protected $fillable = [
        'tenant_id',
        'category',
        'setting_key',
        'setting_value',
        'data_type',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Data type constants
    const DATA_TYPES = [
        'STRING',
        'NUMBER',
        'BOOLEAN',
        'JSON'
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Get properly typed value
    public function getTypedValueAttribute()
    {
        switch ($this->data_type) {
            case 'NUMBER':
                return is_numeric($this->setting_value) ? (float) $this->setting_value : null;
            case 'BOOLEAN':
                return filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN);
            case 'JSON':
                return json_decode($this->setting_value, true);
            default:
                return $this->setting_value;
        }
    }
}
