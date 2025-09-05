<?php

namespace App\Http\Controllers;

use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SuperAdminAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('Auth.Superadmin');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the superadmin by email
        $superadmin = Superadmin::where('email', $credentials['email'])->first();

        // Check if user exists and is active
        if (!$superadmin || !$superadmin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records or account is inactive.'],
            ]);
        }

        // Verify password
        if (!Hash::check($credentials['password'], $superadmin->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        // Login successful
        Auth::guard('superadmin')->login($superadmin);
        
        // Update last login timestamp
        $superadmin->update([
            'last_login_at' => now(),
        ]);

        return redirect()->intended(route('superadmin.dashboard'));
    }

    /**
     * Show the dashboard
     */
    public function dashboard()
    {
        $superadmin = Auth::guard('superadmin')->user();
        
        // Get notifications for this superadmin
        $notifications = \App\Models\Notification::where('superadmin_id', $superadmin->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get pending tenant registrations
        $pendingTenants = \App\Models\Tenant::where('status', \App\Models\Tenant::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get statistics
        $stats = [
            'total_tenants' => \App\Models\Tenant::count(),
            'active_tenants' => \App\Models\Tenant::where('status', \App\Models\Tenant::STATUS_VERIFIED)->count(),
            'pending_tenants' => \App\Models\Tenant::where('status', \App\Models\Tenant::STATUS_PENDING)->count(),
            'rejected_tenants' => \App\Models\Tenant::where('status', \App\Models\Tenant::STATUS_REJECTED)->count(),
        ];

        return view('Superadmin.Dashboard', compact('notifications', 'pendingTenants', 'stats'));
    }

    /**
     * Logout the superadmin
     */
    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('superadmin.login');
    }
}