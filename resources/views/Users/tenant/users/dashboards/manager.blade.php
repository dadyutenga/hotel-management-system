<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
        }
        
        .content {
            padding: 30px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .welcome-card h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .welcome-card p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border-left: 5px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.primary {
            border-left-color: #1a237e;
        }
        
        .stat-card.success {
            border-left-color: #4caf50;
        }
        
        .stat-card.warning {
            border-left-color: #ff9800;
        }
        
        .stat-card.info {
            border-left-color: #2196f3;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .stat-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .stat-icon.primary {
            background: rgba(26, 35, 126, 0.1);
            color: #1a237e;
        }
        
        .stat-icon.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }
        
        .stat-icon.warning {
            background: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }
        
        .stat-icon.info {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-content {
            padding: 25px;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .action-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        
        .action-item:hover {
            border-color: #4caf50;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4caf50;
            color: white;
            font-size: 20px;
        }
        
        .action-info h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .action-info p {
            font-size: 14px;
            color: #666;
        }
        
        .property-info {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .property-header {
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .property-content {
            padding: 25px;
        }
        
        .property-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .property-detail {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .property-detail h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .property-detail p {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .property-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Manager-Specific Sidebar -->
        @include('Users.shared.sidebars.manager')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Manager Dashboard</h1>
                <p>Welcome back, {{ $user->full_name }}! Manage your property operations efficiently.</p>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2><i class="fas fa-user-tie"></i> Property Manager Control Panel</h2>
                    <p>Oversee daily operations, manage staff, and ensure excellent service delivery at your property.</p>
                </div>

                <!-- Property Information -->
                @if($user->property)
                    <div class="property-info">
                        <div class="property-header">
                            <i class="fas fa-building"></i>
                            Property: {{ $user->property->name }}
                        </div>
                        
                        <div class="property-content">
                            <div class="property-details">
                                <div class="property-detail">
                                    <h4>Address</h4>
                                    <p>{{ $user->property->address }}</p>
                                </div>
                                
                                <div class="property-detail">
                                    <h4>Phone</h4>
                                    <p>{{ $user->property->contact_phone }}</p>
                                </div>
                                
                                <div class="property-detail">
                                    <h4>Email</h4>
                                    <p>{{ $user->property->email }}</p>
                                </div>
                                
                                <div class="property-detail">
                                    <h4>Status</h4>
                                    <p style="color: {{ $user->property->is_active ? '#4caf50' : '#f44336' }};">
                                        {{ $user->property->is_active ? 'Active' : 'Inactive' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div class="stat-title">Property Staff</div>
                            <div class="stat-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $dashboardData['property_users'] ?? 0 }}</div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-title">Active Staff</div>
                            <div class="stat-icon success">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $dashboardData['active_users'] ?? 0 }}</div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-title">Buildings</div>
                            <div class="stat-icon warning">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $dashboardData['buildings'] ?? 0 }}</div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-header">
                            <div class="stat-title">Tasks Today</div>
                            <div class="stat-icon info">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                        <div class="stat-value">0</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </div>
                    
                    <div class="card-content">
                        <div class="actions-grid">
                            <a href="{{ route('tenant.rooms.index') }}" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Room Management</h4>
                                    <p>Manage rooms and availability</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('tenant.room-types.index') }}" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-bed"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Room Types</h4>
                                    <p>Configure room categories</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('tenant.floors.index') }}" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Floor Management</h4>
                                    <p>Manage building floors</p>
                                </div>
                            </a>
                            
                            <a href="{{ route('tenant.rooms.create') }}" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Add New Room</h4>
                                    <p>Create new room entry</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Reservations</h4>
                                    <p>View and manage bookings</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Property Reports</h4>
                                    <p>View performance analytics</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-broom"></i>
                                </div>
                                <div class="action-info">
                                    <h4>View Housekeeping & Maintenance Tasks</h4>
                                    <p>View housekeeping and maintenance tasks</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>