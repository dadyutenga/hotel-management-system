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
        
        <!-- Room Management -->
        <div class="nav-section">
            <div class="nav-section-title">Room Management</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tasks"></i>
                <span>Today's Tasks</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-door-open"></i>
                <span>Room Status</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-bed"></i>
                <span>Cleaning Schedule</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-clipboard-check"></i>
                <span>Room Inspections</span>
            </a>
        </div>
        
        <!-- Housekeeping Tasks -->
        <div class="nav-section">
            <div class="nav-section-title">Housekeeping Tasks</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-broom"></i>
                <span>Room Cleaning</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-shower"></i>
                <span>Bathroom Maintenance</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tshirt"></i>
                <span>Laundry Service</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-vacuum"></i>
                <span>Deep Cleaning</span>
            </a>
        </div>
        
        <!-- Inventory Management -->
        <div class="nav-section">
            <div class="nav-section-title">Inventory</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-boxes"></i>
                <span>Supplies Inventory</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                <span>Request Supplies</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-clipboard-list"></i>
                <span>Amenities Stock</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-wine-bottle"></i>
                <span>Minibar Restocking</span>
            </a>
        </div>
        
        <!-- Maintenance -->
        <div class="nav-section">
            <div class="nav-section-title">Maintenance</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Report Issues</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-tools"></i>
                <span>Maintenance Requests</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-key"></i>
                <span>Lost & Found</span>
            </a>
        </div>
        
        <!-- Reports & Logs -->
        <div class="nav-section">
            <div class="nav-section-title">Reports & Logs</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-clock"></i>
                <span>Time Tracking</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-check-circle"></i>
                <span>Completed Tasks</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Daily Reports</span>
            </a>
        </div>
        
        <!-- Guest Services -->
        <div class="nav-section">
            <div class="nav-section-title">Guest Services</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-concierge-bell"></i>
                <span>Guest Requests</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-hand-sparkles"></i>
                <span>Special Cleaning</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-gift"></i>
                <span>Room Setup</span>
            </a>
        </div>
    </nav>
    
    <!-- User Info -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-user-ninja"></i>
            </div>
            <div class="user-details">
                <div class="user-name">{{ Auth::user()->full_name }}</div>
                <div class="user-role">Housekeeper</div>
                @if(Auth::user()->property)
                    <div class="user-property">{{ Auth::user()->property->name }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Housekeeper Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #795548 0%, #8d6e63 100%);
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
    border-right: 4px solid #ffeb3b;
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