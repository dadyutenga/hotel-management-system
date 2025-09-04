<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * User Model
 * 
 * Corresponds to: auth.users table
 * All system users (employees)
 */
class User extends Authenticatable
{
    use HasFactory, HasUuids, SoftDeletes, Notifiable, BelongsToTenant;

    protected $table = 'auth.users';

    protected $fillable = [
        'tenant_id',
        'property_id',
        'username',
        'email',
        'password_hash',
        'full_name',
        'role_id',
        'phone',
        'is_active',
        'mfa_enabled',
        'mfa_secret',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'mfa_secret',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mfa_enabled' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Override password field for authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function customPermissions()
    {
        return $this->hasMany(UserCustomPermission::class, 'user_id');
    }

    public function createdReservations()
    {
        return $this->hasMany(Reservation::class, 'created_by');
    }

    public function updatedReservations()
    {
        return $this->hasMany(Reservation::class, 'updated_by');
    }

    public function createdGuests()
    {
        return $this->hasMany(Guest::class, 'created_by');
    }

    public function updatedGuests()
    {
        return $this->hasMany(Guest::class, 'updated_by');
    }

    public function createdCorporateAccounts()
    {
        return $this->hasMany(CorporateAccount::class, 'created_by');
    }

    public function updatedCorporateAccounts()
    {
        return $this->hasMany(CorporateAccount::class, 'updated_by');
    }

    public function createdGroupBookings()
    {
        return $this->hasMany(GroupBooking::class, 'created_by');
    }

    public function updatedGroupBookings()
    {
        return $this->hasMany(GroupBooking::class, 'updated_by');
    }

    public function reservationStatusChanges()
    {
        return $this->hasMany(ReservationStatusHistory::class, 'changed_by');
    }

    public function createdFolios()
    {
        return $this->hasMany(Folio::class, 'created_by');
    }

    public function updatedFolios()
    {
        return $this->hasMany(Folio::class, 'updated_by');
    }

    public function createdFolioItems()
    {
        return $this->hasMany(FolioItem::class, 'created_by');
    }

    public function createdInvoices()
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function createdPayments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function createdRefunds()
    {
        return $this->hasMany(Refund::class, 'created_by');
    }

    public function createdFxRates()
    {
        return $this->hasMany(FxRate::class, 'created_by');
    }

    public function createdJournals()
    {
        return $this->hasMany(Journal::class, 'created_by');
    }

    public function createdPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function approvedPurchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function createdGoodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    public function createdStocktakes()
    {
        return $this->hasMany(Stocktake::class, 'created_by');
    }

    public function completedStocktakes()
    {
        return $this->hasMany(Stocktake::class, 'completed_by');
    }

    public function stocktakeItems()
    {
        return $this->hasMany(StocktakeItem::class, 'counted_by');
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'server_id');
    }

    public function cancelledPosOrders()
    {
        return $this->hasMany(PosOrder::class, 'cancelled_by');
    }

    public function posPayments()
    {
        return $this->hasMany(PosPayment::class, 'created_by');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'ops.employee_departments', 'user_id', 'department_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function headedDepartments()
    {
        return $this->hasMany(Department::class, 'head_id');
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class, 'user_id');
    }

    public function timeClocks()
    {
        return $this->hasMany(TimeClock::class, 'user_id');
    }

    public function assignedHousekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'assigned_to');
    }

    public function createdHousekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'created_by');
    }

    public function updatedHousekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'updated_by');
    }

    public function verifiedHousekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'verified_by');
    }

    public function roomInspections()
    {
        return $this->hasMany(RoomInspection::class, 'inspector_id');
    }

    public function reportedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'reported_by');
    }

    public function assignedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    public function foundLostItems()
    {
        return $this->hasMany(LostFound::class, 'found_by');
    }

    public function systemSettings()
    {
        return $this->hasMany(SystemSetting::class, 'updated_by');
    }

    public function grantedCustomPermissions()
    {
        return $this->hasMany(UserCustomPermission::class, 'granted_by');
    }

    public function passwordResetTokens()
    {
        return $this->hasMany(PasswordResetToken::class, 'user_id');
    }
}
