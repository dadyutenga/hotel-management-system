<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsolation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip if user is not authenticated
        if (!$user) {
            return $next($request);
        }

        // Ensure user has a valid tenant
        if (!$user->tenant_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Your account is not associated with any tenant. Please contact support.'
            ]);
        }

        // Load tenant relationship if not already loaded
        if (!$user->relationLoaded('tenant')) {
            $user->load('tenant');
        }

        // Ensure tenant exists
        if (!$user->tenant) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Invalid tenant association. Please contact support.'
            ]);
        }

        // For property-related operations, ensure property belongs to user's tenant
        if ($user->property_id && !$user->relationLoaded('property')) {
            $user->load('property');
        }

        if ($user->property && $user->property->tenant_id !== $user->tenant_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'error' => 'Data integrity violation detected. Please contact support.'
            ]);
        }

        // Add tenant context to request for controllers to use
        $request->merge(['current_tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}