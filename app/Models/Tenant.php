<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\CentralConnection;


class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes, CentralConnection;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'certification_type',
        'certification_proof',
        'business_type',
        'base_currency',
        'status',
        'is_active',
        'tin_vat_number',
        'business_license',
        'tax_certificate',
        'owner_id',
        'registration_certificate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Business type constants
    const BUSINESS_TYPES = [
        'HOTEL',
        'LODGE',
        'RESTAURANT',
        'BAR',
        'PUB'
    ];

    // Certification type constants
    const CERT_TYPE_BRELA = 'BRELA';
    const CERT_TYPE_VAT = 'VAT';
    const CERT_TYPE_TIN = 'TIN';

    const CERTIFICATION_TYPES = [
        self::CERT_TYPE_BRELA,
        self::CERT_TYPE_VAT,
        self::CERT_TYPE_TIN,
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_VERIFIED,
        self::STATUS_REJECTED,
    ];

    // Relationships
    public function properties()
    {
        return $this->hasMany(Property::class, 'tenant_id');
    }

    public function staffLimits()
    {
        return $this->hasMany(TenantStaffLimit::class, 'tenant_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    public function roomFeatures()
    {
        return $this->hasMany(RoomFeature::class, 'tenant_id');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'tenant_id');
    }

    public function corporateAccounts()
    {
        return $this->hasMany(CorporateAccount::class, 'tenant_id');
    }

    public function taxes()
    {
        return $this->hasMany(Tax::class, 'tenant_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'tenant_id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'tenant_id');
    }

    public function uoms()
    {
        return $this->hasMany(Uom::class, 'tenant_id');
    }

    public function itemCategories()
    {
        return $this->hasMany(ItemCategory::class, 'tenant_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'tenant_id');
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'tenant_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'tenant_id');
    }

    public function systemSettings()
    {
        return $this->hasMany(SystemSetting::class, 'tenant_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'tenant_id');
    }
}
