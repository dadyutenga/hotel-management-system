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
        
        <!-- Financial Overview -->
        <div class="nav-section">
            <div class="nav-section-title">Financial Overview</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-pie"></i>
                <span>Financial Summary</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-dollar-sign"></i>
                <span>Revenue</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-credit-card"></i>
                <span>Expenses</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-balance-scale"></i>
                <span>Profit & Loss</span>
            </a>
        </div>
        
        <!-- Billing & Invoicing -->
        <div class="nav-section">
            <div class="nav-section-title">Billing & Invoicing</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-file-invoice"></i>
                <span>Guest Invoices</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-receipt"></i>
                <span>Payments</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-undo"></i>
                <span>Refunds</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-exclamation-circle"></i>
                <span>Outstanding Bills</span>
            </a>
        </div>
        
        <!-- Accounting Records -->
        <div class="nav-section">
            <div class="nav-section-title">Accounting Records</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-book"></i>
                <span>General Ledger</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-list"></i>
                <span>Chart of Accounts</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-exchange-alt"></i>
                <span>Transactions</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-calculator"></i>
                <span>Journal Entries</span>
            </a>
        </div>
        
        <!-- Financial Reports -->
        <div class="nav-section">
            <div class="nav-section-title">Financial Reports</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i>
                <span>Monthly Reports</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-file-excel"></i>
                <span>Financial Statements</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-percent"></i>
                <span>Tax Reports</span>
            </a>
        </div>
        
        <!-- Budgeting -->
        <div class="nav-section">
            <div class="nav-section-title">Budgeting</div>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-piggy-bank"></i>
                <span>Budget Planning</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-chart-line"></i>
                <span>Budget vs Actual</span>
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
        </div>
    </div>
</div>

<style>
/* Accountant Sidebar Styles */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
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
    border-right: 4px solid #4caf50;
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