<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - HotelPro</title>
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
            color: #e91e63;
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
            background: linear-gradient(135deg, #e91e63 0%, #f06292 100%);
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
            border-left: 5px solid #e91e63;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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
            background: rgba(233, 30, 99, 0.1);
            color: #e91e63;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #e91e63 0%, #f06292 100%);
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
            border-color: #e91e63;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.1);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e91e63;
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
        
        .upcoming-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .arrivals-card, .departures-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .arrivals-header {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .departures-header {
            background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .guest-list {
            padding: 25px;
        }
        
        .guest-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .guest-item:last-child {
            border-bottom: none;
        }
        
        .guest-info h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .guest-info p {
            font-size: 14px;
            color: #666;
        }
        
        .room-number {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
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
            
            .upcoming-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Receptionist-Specific Sidebar -->
        @include('Users.shared.sidebars.receptionist')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Receptionist Dashboard</h1>
                <p>Welcome back, {{ $user->full_name }}! Provide excellent guest service and manage front desk operations.</p>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2><i class="fas fa-concierge-bell"></i> Front Desk Command Center</h2>
                    <p>Manage guest arrivals, departures, and provide exceptional customer service.</p>
                </div>

                <!-- Reception Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Today's Arrivals</div>
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="stat-value">12</div>
                        <div class="stat-label">Expected guests</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Today's Departures</div>
                            <div class="stat-icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                        </div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Check-outs pending</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Current Occupancy</div>
                            <div class="stat-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                        </div>
                        <div class="stat-value">85%</div>
                        <div class="stat-label">68 of 80 rooms</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Guest Requests</div>
                            <div class="stat-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                        <div class="stat-value">5</div>
                        <div class="stat-label">Pending requests</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        Front Desk Operations
                    </div>
                    
                    <div class="card-content">
                        <div class="actions-grid">
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Check-in Guest</h4>
                                    <p>Process arrivals</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Check-out Guest</h4>
                                    <p>Process departures</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="action-info">
                                    <h4>New Reservation</h4>
                                    <p>Create booking</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Guest Directory</h4>
                                    <p>View guest information</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Room Status</h4>
                                    <p>Check availability</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Guest Billing</h4>
                                    <p>Manage folios</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="upcoming-section">
                    <!-- Arrivals -->
                    <div class="arrivals-card">
                        <div class="arrivals-header">
                            <i class="fas fa-calendar-check"></i>
                            Today's Arrivals
                        </div>
                        
                        <div class="guest-list">
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>John Smith</h5>
                                    <p>Expected: 2:00 PM</p>
                                </div>
                                <div class="room-number">205</div>
                            </div>
                            
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>Maria Garcia</h5>
                                    <p>Expected: 3:30 PM</p>
                                </div>
                                <div class="room-number">312</div>
                            </div>
                            
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>David Johnson</h5>
                                    <p>Expected: 4:15 PM</p>
                                </div>
                                <div class="room-number">108</div>
                            </div>
                        </div>
                    </div>

                    <!-- Departures -->
                    <div class="departures-card">
                        <div class="departures-header">
                            <i class="fas fa-sign-out-alt"></i>
                            Today's Departures
                        </div>
                        
                        <div class="guest-list">
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>Sarah Wilson</h5>
                                    <p>Check-out: 11:00 AM</p>
                                </div>
                                <div class="room-number">401</div>
                            </div>
                            
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>Robert Brown</h5>
                                    <p>Check-out: 12:00 PM</p>
                                </div>
                                <div class="room-number">215</div>
                            </div>
                            
                            <div class="guest-item">
                                <div class="guest-info">
                                    <h5>Lisa Davis</h5>
                                    <p>Check-out: 1:30 PM</p>
                                </div>
                                <div class="room-number">320</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>