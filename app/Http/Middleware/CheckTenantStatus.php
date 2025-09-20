<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class CheckTenantStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $tenant = $user->tenant;
            
            // If tenant is rejected, only allow access to rejection page and logout
            if ($tenant->status === Tenant::STATUS_REJECTED) {
                $allowedRoutes = ['dashboard.rejected', 'logout'];
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('dashboard.rejected');
                }
            }
            
            // If tenant is pending, only allow access to pending page and logout
            elseif ($tenant->status === Tenant::STATUS_PENDING) {
                $allowedRoutes = ['dashboard.pending', 'logout'];
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('dashboard.pending');
                }
            }
            
            // If tenant is verified but trying to access pending/rejected pages
            elseif ($tenant->status === Tenant::STATUS_VERIFIED) {
                $restrictedRoutes = ['dashboard.pending', 'dashboard.rejected'];
                
                if (in_array($request->route()->getName(), $restrictedRoutes)) {
                    return redirect()->route('dashboard');
                }
            }
        }
        
        return $next($request);
    }
}