
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">
            <i class="fas fa-hotel"></i> HotelPro
        </div>
        <div class="brand-subtitle">Management System</div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="/superadmin/dashboard" class="nav-link {{ request()->is('superadmin/dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link {{ request()->is('superadmin/tenants') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                Tenants
            </a>
        </div>
        <div class="nav-item">
            <a href="/superadmin/verify-accounts" class="nav-link {{ request()->is('superadmin/verify-accounts') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i>
                Verify Accounts
            </a>
        </div>
        <div class="nav-item">
            <a href="/superadmin/view-accounts" class="nav-link {{ request()->is('superadmin/view-accounts') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                View Accounts
            </a>
        </div>
        <div class="nav-item">
            <a href="#" class="nav-link {{ request()->is('superadmin/settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                System Settings
            </a>
        </div>
        
        <div class="logout-section">
            <form action="/superadmin/logout" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </nav>
</div>

