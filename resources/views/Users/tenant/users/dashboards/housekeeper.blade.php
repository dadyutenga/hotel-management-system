<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeper Dashboard - HotelPro</title>
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
            color: #795548;
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
            background: linear-gradient(135deg, #795548 0%, #8d6e63 100%);
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
            border-left: 5px solid #795548;
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
            background: rgba(121, 85, 72, 0.1);
            color: #795548;
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
            background: linear-gradient(135deg, #795548 0%, #8d6e63 100%);
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
            border-color: #795548;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(121, 85, 72, 0.1);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #795548;
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
        
        .tasks-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .tasks-card, .priority-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .tasks-header {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .priority-header {
            background: linear-gradient(135deg, #f44336 0%, #ef5350 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .task-list {
            padding: 25px;
        }
        
        .task-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .task-item:last-child {
            border-bottom: none;
        }
        
        .task-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .task-info {
            flex: 1;
        }
        
        .task-info h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .task-info p {
            font-size: 14px;
            color: #666;
        }
        
        .task-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-urgent {
            background: #f8d7da;
            color: #721c24;
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
            
            .tasks-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Housekeeper-Specific Sidebar -->
        @include('Users.shared.sidebars.housekeeper')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Housekeeper Dashboard</h1>
                <p>Welcome back, {{ $user->full_name }}! Maintain cleanliness and comfort for our guests.</p>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2><i class="fas fa-broom"></i> Housekeeping Operations Center</h2>
                    <p>Manage room cleaning, maintenance tasks, and ensure exceptional standards.</p>
                </div>

                <!-- Housekeeping Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Rooms to Clean</div>
                            <div class="stat-icon">
                                <i class="fas fa-bed"></i>
                            </div>
                        </div>
                        <div class="stat-value">18</div>
                        <div class="stat-label">Assigned today</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Completed</div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-value">12</div>
                        <div class="stat-label">Rooms cleaned</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Inspections</div>
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Pending review</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Maintenance</div>
                            <div class="stat-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                        <div class="stat-value">2</div>
                        <div class="stat-label">Issues reported</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        Housekeeping Tools
                    </div>
                    
                    <div class="card-content">
                        <div class="actions-grid">
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Today's Tasks</h4>
                                    <p>View assigned rooms</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Room Status</h4>
                                    <p>Update room conditions</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Supplies Inventory</h4>
                                    <p>Check stock levels</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Report Issue</h4>
                                    <p>Log maintenance needs</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Time Tracking</h4>
                                    <p>Log work hours</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Lost & Found</h4>
                                    <p>Manage guest items</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tasks and Priority Items -->
                <div class="tasks-section">
                    <!-- Today's Tasks -->
                    <div class="tasks-card">
                        <div class="tasks-header">
                            <i class="fas fa-list-check"></i>
                            Today's Cleaning Schedule
                        </div>
                        
                        <div class="task-list">
                            <div class="task-item">
                                <div class="task-checkbox"></div>
                                <div class="task-info">
                                    <h5>Room 205 - Checkout Cleaning</h5>
                                    <p>Guest departed at 11:00 AM</p>
                                </div>
                                <div class="task-status status-pending">Pending</div>
                            </div>
                            
                            <div class="task-item">
                                <div class="task-checkbox"></div>
                                <div class="task-info">
                                    <h5>Room 312 - Deep Clean</h5>
                                    <p>Scheduled maintenance clean</p>
                                </div>
                                <div class="task-status status-pending">Pending</div>
                            </div>
                            
                            <div class="task-item">
                                <div class="task-checkbox"></div>
                                <div class="task-info">
                                    <h5>Suite 401 - VIP Preparation</h5>
                                    <p>Guest arriving at 3:00 PM</p>
                                </div>
                                <div class="task-status status-urgent">Urgent</div>
                            </div>
                            
                            <div class="task-item">
                                <div class="task-checkbox"></div>
                                <div class="task-info">
                                    <h5>Laundry Collection - Floor 2</h5>
                                    <p>Collect linens from rooms</p>
                                </div>
                                <div class="task-status status-pending">Pending</div>
                            </div>
                        </div>
                    </div>

                    <!-- Priority Items -->
                    <div class="priority-card">
                        <div class="priority-header">
                            <i class="fas fa-exclamation-circle"></i>
                            Priority Items
                        </div>
                        
                        <div class="task-list">
                            <div class="task-item">
                                <div class="task-info">
                                    <h5>Bathroom Supplies</h5>
                                    <p>Running low on amenities</p>
                                </div>
                            </div>
                            
                            <div class="task-item">
                                <div class="task-info">
                                    <h5>Room 108 AC Issue</h5>
                                    <p>Guest complaint reported</p>
                                </div>
                            </div>
                            
                            <div class="task-item">
                                <div class="task-info">
                                    <h5>Vacuum Maintenance</h5>
                                    <p>Equipment needs servicing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>