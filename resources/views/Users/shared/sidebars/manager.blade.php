<!-- Manager Sidebar - Property Management -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Property Manager</div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <!-- Property Overview -->
        <div class="nav-section">
            <div class="nav-section-title">Property Operations</div>
        </div>
        
        @if(Auth::user()->property)
        <div class="nav-item">
            <a href="{{ route('tenant.properties.show', Auth::user()->property->id) }}" class="nav-link {{ request()->routeIs('tenant.properties.show') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>My Property</span>
            </a>
        </div>
        @endif
        
        <div class="nav-item">
            <a href="{{ route('tenant.rooms.index') }}" class="nav-link {{ request()->routeIs('tenant.rooms.*') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i>
                <span>Room Management</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('tenant.floors.index') }}" class="nav-link {{ request()->routeIs('tenant.floors.*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                <span>Floor Management</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('tenant.room-types.index') }}" class="nav-link {{ request()->routeIs('tenant.room-types.*') ? 'active' : '' }}">
                <i class="fas fa-bed"></i>
                <span>Room Types</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('tenant.reservations.index') }}" class="nav-link {{ request()->routeIs('tenant.reservations.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Reservations</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.guests.index') }}" class="nav-link {{ request()->routeIs('tenant.guests.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Guest Management</span>
            </a>
        </div>
        
        <!-- Staff Management -->
        <div class="nav-section">
            <div class="nav-section-title">Staff Management</div>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('tenant.users.index') }}" class="nav-link {{ request()->routeIs('tenant.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Property Staff</span>
            </a>
        </div>
        
        <!-- Daily Operations -->
        <div class="nav-section">
            <div class="nav-section-title">Daily Operations</div>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.housekeeping.index') }}" class="nav-link {{ request()->routeIs('tenant.housekeeping.*') ? 'active' : '' }}">
                <i class="fas fa-broom"></i>
                <span>Housekeeping</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.maintenance.index') }}" class="nav-link {{ request()->routeIs('tenant.maintenance.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i>
                <span>Maintenance</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.pos.index') }}" class="nav-link {{ request()->routeIs('tenant.pos.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i>
                <span>POS</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.invoices.index') }}" class="nav-link {{ request()->routeIs('tenant.invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Invoices</span>
            </a>
        </div>

        <!-- Reports -->
        <div class="nav-section">
            <div class="nav-section-title">Reports & Analytics</div>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.index') }}" class="nav-link {{ request()->routeIs('tenant.reports.index') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Reports Dashboard</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.occupancy') }}" class="nav-link {{ request()->routeIs('tenant.reports.occupancy') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Occupancy Reports</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.revenue') }}" class="nav-link {{ request()->routeIs('tenant.reports.revenue') ? 'active' : '' }}">
                <i class="fas fa-dollar-sign"></i>
                <span>Revenue Reports</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.guests') }}" class="nav-link {{ request()->routeIs('tenant.reports.guests') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Guest Reports</span>
            </a>
        </div>
    </nav>
    
    <!-- User Info -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->full_name }}</div>
                <div class="user-role">Property Manager</div>
                @if(Auth::user()->property)
                    <div class="user-property">{{ Auth::user()->property->name }}</div>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* Manager Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
    color: white;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 4px 0 15px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 30px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.brand-logo {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.brand-subtitle {
    font-size: 14px;
    opacity: 0.8;
    font-weight: 500;
}

.sidebar-nav {
    padding: 20px 0;
}

.nav-section {
    padding: 15px 20px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 10px;
}

.nav-section-title {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 0.7;
    letter-spacing: 1px;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
    border-radius: 0 25px 25px 0;
    margin-right: 10px;
}

.nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(5px);
}

.nav-link.active {
    background-color: rgba(255,255,255,0.2);
    color: white;
    border-right: 4px solid #ff5722;
}

.nav-link i {
    margin-right: 12px;
    width: 18px;
    text-align: center;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.2);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.user-details {
    flex: 1;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
}

.user-role {
    font-size: 12px;
    opacity: 0.8;
}

.user-property {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 2px;
}

/* Logout Button Styles */
.logout-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    justify-content: center;
}

.logout-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }
}
</style>