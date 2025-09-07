<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HotelPro</title>
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
            flex: 1;
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a237e;
        }
        
        .user-info {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .welcome-content {
            position: relative;
            z-index: 2;
        }
        
        .welcome-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .welcome-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
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
        
        .stat-card.danger {
            border-left-color: #f44336;
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
            letter-spacing: 0.5px;
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
        
        .stat-icon.danger {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 12px;
            font-weight: 500;
        }
        
        .stat-change.positive {
            color: #4caf50;
        }
        
        .stat-change.negative {
            color: #f44336;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .actions-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 25px;
        }
        
        .action-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .action-card:hover {
            background: #fff;
            border-color: #1a237e;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .action-icon {
            font-size: 32px;
            color: #1a237e;
            margin-bottom: 15px;
        }
        
        .action-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .action-description {
            font-size: 14px;
            color: #666;
        }
        
        /* Add styles for the logout button */
        .btn-logout {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }
        
        .btn-logout i {
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
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
