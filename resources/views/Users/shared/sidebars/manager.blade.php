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
            <a href="{{ route('supervisor.housekeeping.index') }}" class="nav-link {{ request()->routeIs('supervisor.housekeeping.*') ? 'active' : '' }}">
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

        <!-- Logout -->
        <div class="nav-item" style="margin-top: 30px;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; background: none; border: none; cursor: pointer; font-family: inherit;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</div>

<style>
/* Manager Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
    color: white;
    position: fixed;
    height: 100vh;
    z-index: 1000;
    box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 30px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    flex-shrink: 0;
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
    flex: 1;
    overflow-y: auto;
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

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }
}
</style>