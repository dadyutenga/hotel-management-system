<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Property Model
 * 
 * Corresponds to: core.properties table
 * Represents physical properties managed by the tenant
 */
class Property extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToTenant;

    protected $table = 'properties';

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'contact_phone',
        'email',
        'website',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function buildings()
    {
        return $this->hasMany(Building::class, 'property_id');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'property_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'property_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'property_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'property_id');
    }

    public function groupBookings()
    {
        return $this->hasMany(GroupBooking::class, 'property_id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'property_id');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'property_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'property_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'property_id');
    }

    public function stocktakes()
    {
        return $this->hasMany(Stocktake::class, 'property_id');
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class, 'property_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'property_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'property_id');
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'property_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'property_id');
    }

    public function lostFoundItems()
    {
        return $this->hasMany(LostFound::class, 'property_id');
    }
}
