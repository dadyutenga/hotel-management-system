<!-- Bar Tender Sidebar - Bar & Beverage Management -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Bar Tender</div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <!-- Bar Operations -->
        <div class="nav-section">
            <div class="nav-section-title">Bar Operations</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-glass-cheers"></i>
                <span>Current Orders</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-cash-register"></i>
                <span>POS System</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-receipt"></i>
                <span>Daily Sales</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-clock"></i>
                <span>My Shift</span>
            </a>
        </div>
        
        <!-- Inventory Management -->
        <div class="nav-section">
            <div class="nav-section-title">Inventory Management</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-wine-bottle"></i>
                <span>Beverage Stock</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Low Stock Alerts</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-clipboard-list"></i>
                <span>Stock Count</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-truck"></i>
                <span>Deliveries</span>
            </a>
        </div>
        
        <!-- Menu & Pricing -->
        <div class="nav-section">
            <div class="nav-section-title">Menu & Pricing</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-list"></i>
                <span>Drink Menu</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-star"></i>
                <span>Specials</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tags"></i>
                <span>Pricing</span>
            </a>
        </div>
        
        <!-- Customer Service -->
        <div class="nav-section">
            <div class="nav-section-title">Customer Service</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Guest Orders</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-comments"></i>
                <span>Guest Feedback</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-bell"></i>
                <span>Special Requests</span>
            </a>
        </div>
        
        <!-- Reports -->
        <div class="nav-section">
            <div class="nav-section-title">Reports</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Sales Report</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-trophy"></i>
                <span>Top Sellers</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-day"></i>
                <span>Daily Summary</span>
            </a>
        </div>
    </nav>
    
    <!-- User Info -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-cocktail"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->full_name }}</div>
                <div class="user-role">Bar Tender</div>
                @if(Auth::user()->property)
                    <div class="user-property">{{ Auth::user()->property->name }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Bar Tender Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #9c27b0 0%, #ba68c8 100%);
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
    border-right: 4px solid #e91e63;
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

@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }
}
</style>