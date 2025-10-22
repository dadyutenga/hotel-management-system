<!-- Housekeeper Sidebar - Housekeeping Operations -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Housekeeper</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">My Tasks</div>
        </div>

        <div class="nav-item">
            <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="nav-link {{ request()->routeIs('tenant.housekeeper.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-broom"></i>
                <span>Housekeeping Tasks</span>
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
/* Housekeeper Sidebar Styles */
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
</style>
