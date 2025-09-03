<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * TenantSetting Model
 * 
 * Stores configuration settings for each tenant.
 * Allows customization of hotel management system features per tenant.
 * 
 * Table: tenant_settings
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Tenant (tenant)
 */
class TenantSetting extends Model implements Auditable
{
    use HasFactory, HasUuids, UsesTenantConnection, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'id' => 'string',
        'tenant_id' => 'string',
        'value' => 'array',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Setting types
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';

    /**
     * Get the tenant that owns this setting
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get typed value
     */
    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $this->value;
            case self::TYPE_FLOAT:
                return (float) $this->value;
            case self::TYPE_BOOLEAN:
                return (bool) $this->value;
            case self::TYPE_JSON:
            case self::TYPE_ARRAY:
                return is_array($this->value) ? $this->value : json_decode($this->value, true);
            case self::TYPE_STRING:
            default:
                return (string) $this->value;
        }
    }

    /**
     * Scope for public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private settings
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Get setting by key
     */
    public static function getByKey($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->typed_value : $default;
    }

    /**
     * Set setting by key
     */
    public static function setByKey($key, $value, $type = self::TYPE_STRING, $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}