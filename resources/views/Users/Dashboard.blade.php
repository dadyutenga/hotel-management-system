<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Import external CSS files -->
    <link rel="stylesheet" href="{{ asset('css/user-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-dashboard.css') }}">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Sidebar Component -->
        @include('Users.shared.sidebar')
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="header-title">Dashboard</h1>
                <div class="user-info">
                    Welcome, {{ $user->full_name }}
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 15px;">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <div class="welcome-content">
                        <h2 class="welcome-title">Welcome to {{ $tenant->name }}!</h2>
                        <p class="welcome-subtitle">
                            Your {{ ucfirst(strtolower($tenant->business_type)) }} management system is ready to use. 
                            Get started by setting up your properties and rooms.
                        </p>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Total Properties</div>
                                <div class="stat-value">0</div>
                                <div class="stat-change">
                                    <i class="fas fa-info-circle"></i> No properties yet
                                </div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Total Rooms</div>
                                <div class="stat-value">0</div>
                                <div class="stat-change">
                                    <i class="fas fa-info-circle"></i> Add rooms to get started
                                </div>
                            </div>
                            <div class="stat-icon success">
                                <i class="fas fa-bed"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Active Reservations</div>
                                <div class="stat-value">0</div>
                                <div class="stat-change">
                                    <i class="fas fa-calendar"></i> Ready for bookings
                                </div>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card danger">
                        <div class="stat-header">
                            <div>
                                <div class="stat-title">Staff Members</div>
                                <div class="stat-value">1</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-user"></i> You (Admin)
                                </div>
                            </div>
                            <div class="stat-icon danger">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="actions-header">
                        <i class="fas fa-rocket"></i> Quick Setup Actions
                    </div>
                    <div class="actions-grid">
                        <div class="action-card" onclick="alert('Property setup coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="action-title">Add Property</div>
                            <div class="action-description">Set up your first property location</div>
                        </div>
                        
                        <div class="action-card" onclick="alert('Room management coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div class="action-title">Configure Rooms</div>
                            <div class="action-description">Add room types and categories</div>
                        </div>
                        
                        <div class="action-card" onclick="alert('Staff management coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-title">Add Staff</div>
                            <div class="action-description">Invite team members to the system</div>
                        </div>
                        
                        <div class="action-card" onclick="alert('Payment setup coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="action-title">Payment Methods</div>
                            <div class="action-description">Configure accepted payment options</div>
                        </div>
                        
                        <div class="action-card" onclick="alert('Rate management coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="action-title">Set Room Rates</div>
                            <div class="action-description">Define pricing for different seasons</div>
                        </div>
                        
                        <div class="action-card" onclick="alert('System settings coming soon!')">
                            <div class="action-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="action-title">System Settings</div>
                            <div class="action-description">Customize system preferences</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
