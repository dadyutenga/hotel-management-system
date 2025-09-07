<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">HotelPro</div>
        <div class="brand-subtitle">Management System</div>
    </div>
    
    <div class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="#" class="nav-link">
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