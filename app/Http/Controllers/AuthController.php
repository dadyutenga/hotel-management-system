<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Notification;
use App\Models\Superadmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('Auth.Register');
    }

    /**
     * Handle business registration
     */
    public function register(Request $request)
    {
                // Validate the form data
        $validated = $request->validate([
            // Business details
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:500',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'business_email' => 'required|email|unique:tenants,contact_email',
            'business_phone' => 'required|string|max:20',
            'business_type' => 'required|in:HOTEL,LODGE,RESTAURANT,BAR,PUB',
            'certification_type' => 'required|in:BRELA,VAT,TIN',
            'tin_vat_number' => 'required|string|max:50',
            
            // Documents
            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'owner_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'registration_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Admin User Details
            'admin_username' => 'required|string|max:255|unique:users,username',
            'admin_email' => 'required|email|unique:users,email',
            'admin_full_name' => 'required|string|max:255',
            'admin_phone' => 'nullable|string|max:20',
            'admin_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'admin_password_confirmation' => 'required|same:admin_password',
            'mfa_enabled' => 'boolean',
        ]);

        try {
            // Store uploaded documents
            $documents = [];
            $documentFields = ['business_license', 'tax_certificate', 'owner_id', 'registration_certificate'];
            
            foreach ($documentFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $field . '_' . time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('tenant-documents', $filename, 'public');
                    $documents[$field] = $path;
                }
            }

            // Create tenant
                        // Create the tenant record
            $tenant = Tenant::create([
                'id' => Str::uuid(),
                'name' => $validated['business_name'],
                'address' => $validated['business_address'] . ', ' . $validated['city'] . ', ' . $validated['country'],
                'contact_email' => $validated['business_email'],
                'contact_phone' => $validated['business_phone'],
                'certification_type' => $validated['certification_type'],
                'business_type' => $validated['business_type'],
                'base_currency' => 'TZS',
                'status' => 'pending',
                'is_active' => false,
                'tin_vat_number' => $validated['tin_vat_number'],
                'business_license' => $documents['business_license'] ?? null,
                'tax_certificate' => $documents['tax_certificate'] ?? null,
                'owner_id' => $documents['owner_id'] ?? null,
                'registration_certificate' => $documents['registration_certificate'] ?? null,
            ]);

            // Get or create admin role
            $adminRole = Role::firstOrCreate(
                ['name' => 'DIRECTOR'],
                ['description' => 'Business Director/Admin']
            );

            // Create admin user
            $adminUser = User::create([
                'tenant_id' => $tenant->id,
                'username' => $validated['admin_username'],
                'email' => $validated['admin_email'],
                'password_hash' => Hash::make($validated['admin_password']),
                'full_name' => $validated['admin_full_name'],
                'role_id' => $adminRole->id,
                'phone' => $validated['admin_phone'],
                'is_active' => true,
                'mfa_enabled' => $request->boolean('mfa_enabled', false),
            ]);

            // Send notification to all superadmins
            $this->notifySuperadmins($tenant);

            return redirect()->route('login')->with('success', 
                'Registration successful! Your application is pending verification. You will be notified once approved.');

        } catch (\Exception $e) {
            // Clean up uploaded files if tenant creation fails
            foreach ($documents as $path) {
                Storage::disk('public')->delete($path);
            }
            
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('Auth.Login');
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Account not found.'],
            ]);
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        // Check tenant status first
        $tenant = $user->tenant;
        
        // Allow login for rejected users so they can see rejection page
        if ($tenant->status === Tenant::STATUS_REJECTED) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);
            return redirect()->route('dashboard.rejected');
        }
        
        // For other statuses, check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact support.'],
            ]);
        }

        // Login successful
        Auth::login($user);
        
        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Redirect based on tenant status
        if ($tenant->status === Tenant::STATUS_VERIFIED) {
            return redirect()->intended(route('dashboard'));
        } elseif ($tenant->status === Tenant::STATUS_PENDING) {
            return redirect()->route('dashboard.pending');
        }

        // Fallback to pending if status is unknown
        return redirect()->route('dashboard.pending');
    }

    /**
     * Show pending verification dashboard
     */
    public function showPendingDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // Check tenant status and redirect accordingly
        if ($tenant->status === Tenant::STATUS_VERIFIED) {
            return redirect()->route('dashboard');
        } elseif ($tenant->status === Tenant::STATUS_REJECTED) {
            return redirect()->route('dashboard.rejected');
        }

        return view('Users.PendingDashboard', compact('tenant'));
    }

    /**
     * Show main dashboard
     */
    public function showDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // Ensure tenant is verified, redirect to appropriate status page
        if ($tenant->status === Tenant::STATUS_PENDING) {
            return redirect()->route('dashboard.pending');
        } elseif ($tenant->status === Tenant::STATUS_REJECTED) {
            return redirect()->route('dashboard.rejected');
        } elseif ($tenant->status !== Tenant::STATUS_VERIFIED) {
            return redirect()->route('dashboard.pending');
        }

        return view('Users.Dashboard', compact('user', 'tenant'));
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Send notification to all superadmins about new registration
     */
    private function notifySuperadmins(Tenant $tenant)
    {
        $superadmins = Superadmin::where('is_active', true)->get();
        
        foreach ($superadmins as $superadmin) {
            Notification::create([
                'tenant_id' => null, // System notification
                'user_id' => null,
                'superadmin_id' => $superadmin->id,
                'title' => 'New Business Registration',
                'message' => "A new {$tenant->business_type} '{$tenant->name}' has registered and requires verification.",
                'type' => 'registration',
                'is_read' => false,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Show rejected verification dashboard
     */
    public function showRejectedDashboard()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // Check if the tenant status has changed and redirect accordingly
        if ($tenant->status === Tenant::STATUS_VERIFIED) {
            return redirect()->route('dashboard');
        } elseif ($tenant->status === Tenant::STATUS_PENDING) {
            return redirect()->route('dashboard.pending');
        }

        // Get the rejection notification for this tenant
        $rejectionNotification = Notification::where('tenant_id', $tenant->id)
            ->where('type', 'account_rejected')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('Users.Retry', compact('tenant', 'rejectionNotification'));
    }
}
