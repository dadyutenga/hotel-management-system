<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperadminController extends Controller
{
    /**
     * Show pending tenant registrations for verification
     */
    public function verifyAccounts()
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        // Get all pending tenants with their admin users
        $pendingTenants = Tenant::with(['users' => function($query) {
            $query->whereHas('roles', function($roleQuery) {
                $roleQuery->where('name', 'admin');
            });
        }])
        ->where('status', Tenant::STATUS_PENDING)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('Superadmin.VerifyAcc', compact('pendingTenants'));
    }

    /**
     * Show all tenant accounts
     */
    public function viewAccounts()
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        // Get all tenants with their admin users
        $tenants = Tenant::with(['users' => function($query) {
            $query->whereHas('roles', function($roleQuery) {
                $roleQuery->where('name', 'admin');
            });
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('Superadmin.ViewAcc', compact('tenants'));
    }

    /**
     * Show tenant details for verification
     */
    public function showTenantDetails($tenantId)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        $tenant = Tenant::with(['users' => function($query) {
            $query->whereHas('roles', function($roleQuery) {
                $roleQuery->where('name', 'admin');
            });
        }])->findOrFail($tenantId);

        return response()->json([
            'tenant' => $tenant,
            'admin_user' => $tenant->users->first(),
            'documents' => [
                'business_license' => $tenant->business_license ? Storage::url($tenant->business_license) : null,
                'tax_certificate' => $tenant->tax_certificate ? Storage::url($tenant->tax_certificate) : null,
                'owner_id' => $tenant->owner_id ? Storage::url($tenant->owner_id) : null,
                'registration_certificate' => $tenant->registration_certificate ? Storage::url($tenant->registration_certificate) : null,
            ]
        ]);
    }

    /**
     * Approve a tenant registration
     */
    public function approveTenant(Request $request, $tenantId)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        $tenant = Tenant::findOrFail($tenantId);
        
        if ($tenant->status !== Tenant::STATUS_PENDING) {
            return response()->json(['error' => 'Tenant is not in pending status'], 400);
        }

        // Update tenant status
        $tenant->update([
            'status' => Tenant::STATUS_VERIFIED,
            'is_active' => true,
        ]);

        // Activate the admin user
        $adminUser = $tenant->users()->whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        if ($adminUser) {
            $adminUser->update(['is_active' => true]);
        }

        // Create notification for the tenant
        Notification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'tenant_id' => $tenant->id,
            'superadmin_id' => $superadmin->id,
            'type' => 'account_approved',
            'title' => 'Account Approved',
            'message' => 'Your business registration has been approved. You can now access the full system.',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tenant approved successfully'
        ]);
    }

    /**
     * Reject a tenant registration
     */
    public function rejectTenant(Request $request, $tenantId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $superadmin = Auth::guard('superadmin')->user();
        
        $tenant = Tenant::findOrFail($tenantId);
        
        if ($tenant->status !== Tenant::STATUS_PENDING) {
            return response()->json(['error' => 'Tenant is not in pending status'], 400);
        }

        // Update tenant status
        $tenant->update([
            'status' => Tenant::STATUS_REJECTED,
            'is_active' => false,
        ]);

        // Deactivate the admin user
        $adminUser = $tenant->users()->whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        if ($adminUser) {
            $adminUser->update(['is_active' => false]);
        }

        // Create notification for the tenant
        Notification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'tenant_id' => $tenant->id,
            'superadmin_id' => $superadmin->id,
            'type' => 'account_rejected',
            'title' => 'Account Rejected',
            'message' => 'Your business registration has been rejected. Reason: ' . $request->rejection_reason,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tenant rejected successfully'
        ]);
    }

    /**
     * Download a document
     */
    public function downloadDocument($tenantId, $documentType)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        $tenant = Tenant::findOrFail($tenantId);
        
        $allowedTypes = ['business_license', 'tax_certificate', 'owner_id', 'registration_certificate'];
        
        if (!in_array($documentType, $allowedTypes)) {
            abort(400, 'Invalid document type');
        }

        $filePath = $tenant->$documentType;
        
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        return Storage::disk('public')->download($filePath);
    }
}
