<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Tender Dashboard - HotelPro</title>
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
            color: #7b1fa2;
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
            background: linear-gradient(135deg, #7b1fa2 0%, #ab47bc 100%);
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
            border-left: 5px solid #7b1fa2;
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
            background: rgba(123, 31, 162, 0.1);
            color: #7b1fa2;
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
            background: linear-gradient(135deg, #7b1fa2 0%, #ab47bc 100%);
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
            border-color: #7b1fa2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 31, 162, 0.1);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #7b1fa2;
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
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Bar Tender-Specific Sidebar -->
        @include('Users.shared.sidebars.bar_tender')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Bar Tender Dashboard</h1>
                <p>Welcome back, {{ $user->full_name }}! Manage bar operations and deliver exceptional service.</p>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2><i class="fas fa-cocktail"></i> Bar Operations Center</h2>
                    <p>Manage orders, track inventory, and create memorable experiences for guests.</p>
                </div>

                <!-- Bar Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Today's Orders</div>
                            <div class="stat-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="stat-value">47</div>
                        <div class="stat-label">+8 from yesterday</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Revenue Today</div>
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="stat-value">$685</div>
                        <div class="stat-label">12% above target</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Tables</div>
                            <div class="stat-icon">
                                <i class="fas fa-chair"></i>
                            </div>
                        </div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Out of 15 tables</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Low Stock Items</div>
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Need restocking</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="card-header">
                        <i class="fas fa-bolt"></i>
                        Bar Operations
                    </div>
                    
                    <div class="card-content">
                        <div class="actions-grid">
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="action-info">
                                    <h4>New Order</h4>
                                    <p>Create customer order</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-list-alt"></i>
                                </div>
                                <div class="action-info">
                                    <h4>View Menu</h4>
                                    <p>Browse drink menu</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Inventory Check</h4>
                                    <p>Monitor stock levels</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Process Payment</h4>
                                    <p>Handle transactions</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Sales Report</h4>
                                    <p>View daily sales</p>
                                </div>
                            </a>
                            
                            <a href="#" class="action-item">
                                <div class="action-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="action-info">
                                    <h4>Shift Schedule</h4>
                                    <p>Check work schedule</p>
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