<!-- Supervisor Sidebar -->
<aside class="sidebar">
    <!-- Brand Header -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-hotel"></i>
        </div>
        <div class="brand-content">
            <h1 class="brand-title">HotelPro</h1>
            <p class="brand-subtitle">Supervisor</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-menu">
        <!-- Dashboard -->
        <a href="{{ route('user.dashboard') }}" class="menu-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="menu-icon fas fa-tachometer-alt"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        <!-- Operational Control Section -->
        <div class="menu-section">
            <span class="section-label">Operational Control</span>
        </div>

        <a href="{{ route('tenant.housekeeping.index') }}" class="menu-item {{ request()->routeIs('tenant.housekeeping.*') ? 'active' : '' }}">
            <i class="menu-icon fas fa-clipboard-check"></i>
            <span class="menu-text">Housekeeping</span>
        </a>

        <a href="{{ route('tenant.maintenance.index') }}" class="menu-item {{ request()->routeIs('tenant.maintenance.*') ? 'active' : '' }}">
            <i class="menu-icon fas fa-tools"></i>
            <span class="menu-text">Maintenance</span>
        </a>

        <a href="{{ route('tenant.reports.housekeeping') }}" class="menu-item {{ request()->routeIs('tenant.reports.housekeeping') ? 'active' : '' }}">
            <i class="menu-icon fas fa-chart-line"></i>
            <span class="menu-text">Housekeeping Report</span>
        </a>

        <!-- Logout Section -->
        <div class="menu-section">
            <span class="section-label">Account</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="menu-item logout-menu-item">
                <i class="menu-icon fas fa-sign-out-alt"></i>
                <span class="menu-text">Logout</span>
            </button>
        </form>
    </nav>

    <!-- User Profile Footer -->
    <div class="sidebar-footer">
    </div>
</aside>

<style>
/* ========== UNIFIED SIDEBAR DESIGN ========== */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, #7c2d12 0%, #9a3412 50%, #c2410c 100%);
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
    display: flex;
    flex-direction: column;
    z-index: 1000;
    overflow: hidden;
}

/* ========== BRAND HEADER ========== */

.sidebar-brand {
    padding: 24px 20px;
    background: rgba(0, 0, 0, 0.15);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 14px;
}

.brand-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    flex-shrink: 0;
}

.brand-content {
    flex: 1;
    min-width: 0;
}

.brand-title {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    line-height: 1.2;
}

.brand-subtitle {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.75);
    margin: 2px 0 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ========== NAVIGATION MENU ========== */

.sidebar-menu {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 16px;
}

.sidebar-menu::-webkit-scrollbar {
    width: 6px;
}

.sidebar-menu::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.sidebar-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* ========== MENU SECTIONS ========== */

.menu-section {
    padding: 20px 20px 8px;
    margin-top: 8px;
}

.menu-section:first-child {
    margin-top: 0;
}

.section-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: rgba(255, 255, 255, 0.5);
}

/* ========== MENU ITEMS ========== */

.menu-item {
    display: flex;
    align-items: center;
    height: 44px;
    padding: 0 20px;
    margin: 2px 12px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 14px;
    font-weight: 500;
    position: relative;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    transform: translateX(4px);
}

.menu-item.active {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.menu-item.active::before {
    content: '';
    position: absolute;
    left: -12px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 24px;
    background: #fff;
    border-radius: 0 4px 4px 0;
}

.menu-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.menu-text {
    margin-left: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ========== LOGOUT MENU ITEM ========== */

.logout-form {
    margin: 2px 12px;
}

.logout-menu-item {
    width: 100%;
    display: flex;
    align-items: center;
    height: 44px;
    padding: 0 20px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 14px;
    font-weight: 500;
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
}

.logout-menu-item:hover {
    background: rgba(255, 59, 48, 0.2);
    color: #ffffff;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(255, 59, 48, 0.3);
}

/* ========== USER PROFILE FOOTER ========== */

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.2);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0;
}

/* ========== RESPONSIVE ========== */

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }
    
    .sidebar-menu {
        padding-bottom: 16px;
    }
    
    .sidebar-footer {
        position: relative;
    }
}
</style>