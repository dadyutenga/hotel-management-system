<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * User Model
 * 
 * Represents users in the hotel management system (staff, managers, admins, guests).
 * Users are scoped to tenants for multi-tenancy.
 * 
 * Table: users
 * Primary Key: UUID
 * 
 * Relationships:
 * - belongsTo: Tenant (tenant)
 * - hasMany: Reservation (reservations)
 * - hasMany: Payment (payments)
 * - hasMany: ServiceOrder (serviceOrders)
 */
class User extends Authenticatable implements Auditable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, UsesTenantConnection, HasRoles, \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'user_type',
        'employee_id',
        'department',
        'job_title',
        'date_of_birth',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'hire_date',
        'salary',
        'is_active',
        'profile_photo',
        'preferences',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'salary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'tenant_id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected $dates = [
        'email_verified_at',
        'date_of_birth',
        'hire_date',
        'last_login_at',
        'deleted_at',
    ];

    // User types
    const USER_TYPE_ADMIN = 'admin';
    const USER_TYPE_MANAGER = 'manager';
    const USER_TYPE_STAFF = 'staff';
    const USER_TYPE_GUEST = 'guest';

    /**
     * Get the tenant that owns the user
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get reservations made by this user (if guest) or handled by this user (if staff)
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'guest_id');
    }

    /**
     * Get reservations handled by this staff member
     */
    public function handledReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'handled_by');
    }

    /**
     * Get payments made by this user
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get service orders made by this user
     */
    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for staff users
     */
    public function scopeStaff($query)
    {
        return $query->whereIn('user_type', [self::USER_TYPE_ADMIN, self::USER_TYPE_MANAGER, self::USER_TYPE_STAFF]);
    }

    /**
     * Scope for guests
     */
    public function scopeGuests($query)
    {
        return $query->where('user_type', self::USER_TYPE_GUEST);
    }

    /**
     * Check if user is staff member
     */
    public function isStaff(): bool
    {
        return in_array($this->user_type, [self::USER_TYPE_ADMIN, self::USER_TYPE_MANAGER, self::USER_TYPE_STAFF]);
    }

    /**
     * Check if user is guest
     */
    public function isGuest(): bool
    {
        return $this->user_type === self::USER_TYPE_GUEST;
    }
}
