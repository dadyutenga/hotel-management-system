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
     * Show tenant registrations for verification (pending and rejected)
     */
    public function verifyAccounts(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        // Get status filter from request, default to 'pending'
        $status = $request->get('status', 'pending');
        $allowedStatuses = ['pending', 'rejected', 'all'];
        
        if (!in_array($status, $allowedStatuses)) {
            $status = 'pending';
        }
        
        // Build query based on status filter
        $query = Tenant::with(['users' => function($query) {
            $query->whereHas('role', function($roleQuery) {
                $roleQuery->where('name', 'DIRECTOR');
            });
        }]);
        
        if ($status === 'pending') {
            $query->where('status', Tenant::STATUS_PENDING);
        } elseif ($status === 'rejected') {
            $query->where('status', Tenant::STATUS_REJECTED);
        }
        // For 'all', we don't add any status filter
        
        $tenants = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get counts for stats
        $pendingCount = Tenant::where('status', Tenant::STATUS_PENDING)->count();
        $rejectedCount = Tenant::where('status', Tenant::STATUS_REJECTED)->count();
        $verifiedCount = Tenant::where('status', Tenant::STATUS_VERIFIED)->count();
        
        return view('Superadmin.VerifyAcc', compact(
            'tenants', 
            'status', 
            'pendingCount',
            'rejectedCount',
            'verifiedCount'
        ));
    }

    /**
     * Show all tenant accounts (for ViewAcc page)
     */
    public function viewAccounts(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        // Build query with filters
        $query = Tenant::with(['users' => function($query) {
            $query->whereHas('role', function($roleQuery) {
                $roleQuery->where('name', 'DIRECTOR');
            });
        }]);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }
        
        $tenants = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('Superadmin.ViewAcc', compact('tenants'));
    }

    /**
     * Show tenant details for verification
     */
    public function showTenantDetails($tenantId)
    {
        try {
            $superadmin = Auth::guard('superadmin')->user();
            
            $tenant = Tenant::with(['users' => function($query) {
                $query->whereHas('role', function($roleQuery) {
                    $roleQuery->where('name', 'DIRECTOR');
                });
            }])->findOrFail($tenantId);

            $adminUser = $tenant->users->first();

            return response()->json([
                'success' => true,
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'business_type' => $tenant->business_type,
                    'contact_email' => $tenant->contact_email,
                    'contact_phone' => $tenant->contact_phone,
                    'address' => $tenant->address,
                    'city' => $tenant->city,
                    'country' => $tenant->country,
                    'certification_type' => $tenant->certification_type,
                    'tin_vat_number' => $tenant->tin_vat_number,
                    'status' => $tenant->status,
                    'is_active' => $tenant->is_active,
                    'created_at' => $tenant->created_at->format('M d, Y H:i'),
                ],
                'admin_user' => $adminUser ? [
                    'name' => $adminUser->full_name,  // Changed from 'name' to 'full_name'
                    'full_name' => $adminUser->full_name,  // Added this for clarity
                    'username' => $adminUser->username,
                    'email' => $adminUser->email,
                    'phone' => $adminUser->phone,
                    'is_active' => $adminUser->is_active,
                ] : null,
                'documents' => [
                    'business_license' => $tenant->business_license,
                    'tax_certificate' => $tenant->tax_certificate,
                    'owner_id' => $tenant->owner_id,
                    'registration_certificate' => $tenant->registration_certificate,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching tenant details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading tenant details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a tenant registration (works for both pending and rejected)
     */
    public function approveTenant(Request $request, $tenantId)
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        $tenant = Tenant::findOrFail($tenantId);
        
        // Allow approval for both pending and rejected tenants
        if (!in_array($tenant->status, [Tenant::STATUS_PENDING, Tenant::STATUS_REJECTED])) {
            return response()->json(['error' => 'Tenant cannot be approved in current status'], 400);
        }

        $wasRejected = $tenant->status === Tenant::STATUS_REJECTED;

        // Update tenant status
        $tenant->update([
            'status' => Tenant::STATUS_VERIFIED,
            'is_active' => true,
        ]);

        // Activate the admin user (DIRECTOR role)
        $adminUser = $tenant->users()->whereHas('role', function($query) {
            $query->where('name', 'DIRECTOR');
        })->first();

        if ($adminUser) {
            $adminUser->update(['is_active' => true]);
        }

        // Create notification for the tenant
        $notificationMessage = $wasRejected 
            ? 'Your business registration has been re-approved after review. You can now access the full system.'
            : 'Your business registration has been approved. You can now access the full system.';

        Notification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'tenant_id' => $tenant->id,
            'superadmin_id' => $superadmin->id,
            'type' => $wasRejected ? 'account_reapproved' : 'account_approved',
            'title' => $wasRejected ? 'Account Re-approved' : 'Account Approved',
            'message' => $notificationMessage,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => $wasRejected ? 'Tenant re-approved successfully' : 'Tenant approved successfully'
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
            return response()->json(['error' => 'Only pending tenants can be rejected'], 400);
        }

        // Update tenant status
        $tenant->update([
            'status' => Tenant::STATUS_REJECTED,
            'is_active' => false,
        ]);

        // Deactivate the admin user (DIRECTOR role)
        $adminUser = $tenant->users()->whereHas('role', function($query) {
            $query->where('name', 'DIRECTOR');
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

    /**
     * Mark a notification as read
     */
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * View a document in browser
     */
    public function viewDocument($tenantId, $documentType)
    {
        try {
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

            // Return the file with proper headers for viewing in browser
            $fullPath = storage_path('app/public/' . $filePath);
            $mimeType = mime_content_type($fullPath);
            
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error viewing document: ' . $e->getMessage());
            abort(404, 'Document not found');
        }
    }
}
