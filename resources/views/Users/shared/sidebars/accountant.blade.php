<!-- Accountant Sidebar - Financial Management -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Accountant</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Financial Operations</div>
        </div>

        @php($currentFolio = request()->route('folio'))
        @if($currentFolio)
        <div class="nav-item">
            <a href="{{ route('tenant.folios.show', $currentFolio) }}" class="nav-link {{ request()->routeIs('tenant.folios.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i>
                <span>Folios</span>
            </a>
        </div>
        @endif

        <div class="nav-item">
            <a href="{{ route('tenant.invoices.index') }}" class="nav-link {{ request()->routeIs('tenant.invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Invoices</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.revenue') }}" class="nav-link {{ request()->routeIs('tenant.reports.revenue') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Revenue Reports</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.reports.occupancy') }}" class="nav-link {{ request()->routeIs('tenant.reports.occupancy') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Occupancy Reports</span>
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
                <i class="fas fa-calculator"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->full_name }}</div>
                <div class="user-role">Accountant</div>
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
/* Accountant Sidebar Styles */
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
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.nav-link i {
    width: 24px;
    margin-right: 12px;
    font-size: 16px;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    padding-left: 24px;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.user-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 12px;
}

.user-name {
    font-size: 16px;
    font-weight: 600;
}

.user-role {
    font-size: 14px;
    opacity: 0.8;
}

.user-property {
    font-size: 13px;
    opacity: 0.7;
    margin-top: 4px;
}

.logout-btn {
    margin-top: 12px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.6);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.logout-btn:hover {
    background: rgba(255,255,255,0.2);
    border-color: white;
}
</style>
