<style>
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
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

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-link:hover {
    background-color: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(5px);
}

.nav-link.active {
    background-color: rgba(255,255,255,0.2);
    color: white;
    border-right: 4px solid #f44336;
}

.nav-link i {
    margin-right: 12px;
    width: 18px;
    text-align: center;
}

.logout-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.logout-btn {
    background: none;
    border: none;
    color: rgba(255,255,255,0.8);
    width: 100%;
    text-align: left;
    padding: 12px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    font-family: inherit;
    display: flex;
    align-items: center;
}

.logout-btn:hover {
    background-color: rgba(244, 67, 54, 0.2);
    color: white;
}

.logout-btn i {
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

<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Management System</div>
    </div>
    
    <div class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('tenant.properties.index') }}" class="nav-link {{ request()->routeIs('tenant.properties.*') ? 'active' : '' }}">
            <i class="fas fa-building"></i> Properties
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-bed"></i> Rooms
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-calendar-check"></i> Reservations
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-users"></i> Staff
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-tags"></i> Rates
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>
    
    <div class="logout-section">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>