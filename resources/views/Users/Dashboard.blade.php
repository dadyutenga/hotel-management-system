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
                                <div class="stat-value">{{ $tenant->properties->count() ?? 0 }}</div>
                                <div class="stat-change">
                                    @if($tenant->properties->count() > 0)
                                        <i class="fas fa-building"></i> Properties configured
                                    @else
                                        <i class="fas fa-info-circle"></i> No properties yet
                                    @endif
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
                                <div class="stat-value">{{ $tenant->properties->sum(function($property) { return $property->rooms->count(); }) ?? 0 }}</div>
                                <div class="stat-change">
                                    @if($tenant->properties->sum(function($property) { return $property->rooms->count(); }) > 0)
                                        <i class="fas fa-bed"></i> Rooms available
                                    @else
                                        <i class="fas fa-info-circle"></i> Add rooms to get started
                                    @endif
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
                                <div class="stat-value">{{ $tenant->properties->sum(function($property) { return $property->reservations->where('status', 'CONFIRMED')->count(); }) ?? 0 }}</div>
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
                                <div class="stat-value">{{ $tenant->users->count() ?? 1 }}</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-user"></i> 
                                    @if($tenant->users->count() > 1)
                                        {{ $tenant->users->count() }} Team members
                                    @else
                                        You (Admin)
                                    @endif
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
                        <!-- Add Property Action -->
                        <a href="{{ route('tenant.properties.create') }}" class="action-card" style="text-decoration: none; color: inherit;">
                            <div class="action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="action-title">Add Property</div>
                            <div class="action-description">Set up your first property location</div>
                        </a>
                        
                        <!-- View Properties Action -->
                        <a href="{{ route('tenant.properties.index') }}" class="action-card" style="text-decoration: none; color: inherit;">
                            <div class="action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="action-title">Manage Properties</div>
                            <div class="action-description">View and manage all your properties</div>
                        </a>
                        
                        <!-- Configure Rooms Action -->
                        <div class="action-card" onclick="showComingSoonAlert('Room management')">
                            <div class="action-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                            <div class="action-title">Configure Rooms</div>
                            <div class="action-description">Add room types and categories</div>
                        </div>
                        
                        <!-- Add Staff Action -->
                        <a href="{{ route('tenant.users.create') }}" class="action-card" style="text-decoration: none; color: inherit;">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-title">Add Staff</div>
                            <div class="action-description">Invite team members to the system</div>
                        </a>
                        
                        <!-- Manage Staff Action -->
                        <a href="{{ route('tenant.users.index') }}" class="action-card" style="text-decoration: none; color: inherit;">
                            <div class="action-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="action-title">Manage Staff</div>
                            <div class="action-description">View and manage existing team members</div>
                        </a>
                        
                        <!-- Payment Methods Action -->
                        <div class="action-card" onclick="showComingSoonAlert('Payment setup')">
                            <div class="action-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="action-title">Payment Methods</div>
                            <div class="action-description">Configure accepted payment options</div>
                        </div>
                        
                        <!-- Room Rates Action -->
                        <div class="action-card" onclick="showComingSoonAlert('Rate management')">
                            <div class="action-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="action-title">Set Room Rates</div>
                            <div class="action-description">Define pricing for different seasons</div>
                        </div>
                        
                        <!-- User Dashboard Action -->
                        <a href="{{ route('user.dashboard') }}" class="action-card" style="text-decoration: none; color: inherit;">
                            <div class="action-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="action-title">Role Dashboard</div>
                            <div class="action-description">Access your role-specific dashboard</div>
                        </a>
                        
                        <!-- System Settings Action -->
                        <div class="action-card" onclick="showComingSoonAlert('System settings')">
                            <div class="action-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="action-title">System Settings</div>
                            <div class="action-description">Customize system preferences</div>
                        </div>
                        
                        <!-- Reports Action -->
                        <div class="action-card" onclick="showComingSoonAlert('Reports')">
                            <div class="action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="action-title">View Reports</div>
                            <div class="action-description">Access business analytics and reports</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section (if there are properties) -->
                @if($tenant->properties->count() > 0)
                <div class="recent-activity">
                    <div class="activity-header">
                        <i class="fas fa-clock"></i> Recent Activity
                    </div>
                    <div class="activity-list">
                        @foreach($tenant->properties->take(3) as $property)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Property: {{ $property->name }}</div>
                                <div class="activity-description">
                                    {{ $property->rooms->count() }} rooms | 
                                    {{ $property->users->count() }} staff members |
                                    Status: {{ $property->is_active ? 'Active' : 'Inactive' }}
                                </div>
                                <div class="activity-time">
                                    Created {{ $property->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="activity-actions">
                                <a href="{{ route('tenant.properties.show', $property->id) }}" class="btn-sm btn-primary">View</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Function to show coming soon alerts
        function showComingSoonAlert(feature) {
            alert(feature + ' coming soon! This feature is currently under development.');
        }

        // Add hover effects for action cards
        document.addEventListener('DOMContentLoaded', function() {
            const actionCards = document.querySelectorAll('.action-card');
            
            actionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                });
            });
        });

        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            // You can add AJAX calls here to refresh statistics
            console.log('Stats refreshed at:', new Date().toLocaleTimeString());
        }, 30000);
    </script>

    <style>
        /* Additional styles for new elements */
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 30px;
        }

        .activity-header {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-header i {
            color: #3b82f6;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .activity-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .activity-time {
            color: #9ca3af;
            font-size: 12px;
        }

        .activity-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        /* Update action card hover styles */
        .action-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Link styles for action cards */
        a.action-card:hover {
            text-decoration: none;
            color: inherit;
        }
    </style>
</body>
</html>
