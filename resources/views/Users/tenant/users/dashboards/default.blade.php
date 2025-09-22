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
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 5px solid #2196f3;
        }
        
        .info-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
            font-size: 20px;
        }
        
        .info-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .info-content {
            color: #666;
            line-height: 1.6;
        }
        
        .property-info {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-content {
            padding: 25px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }
        
        .badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-info {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-success {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .badge-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        @include('Users.shared.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>{{ ucfirst(strtolower(str_replace('_', ' ', $user->role->name))) }} Dashboard</h1>
                <p>Welcome back, {{ $user->full_name }}! Here's your workspace overview.</p>
            </div>
            
            <div class="content">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <h2><i class="fas fa-user"></i> Welcome to Your Dashboard</h2>
                    <p>Access your daily tasks, view important information, and stay connected with your team.</p>
                </div>

                <!-- Information Cards -->
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <div class="info-title">Your Role</div>
                        </div>
                        <div class="info-content">
                            <p>You are assigned as <strong>{{ $user->role->name }}</strong> in the system. Your role determines your access levels and available features.</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="info-title">Your Property</div>
                        </div>
                        <div class="info-content">
                            @if($user->property)
                                <p>You are assigned to <strong>{{ $user->property->name }}</strong>. All your work activities are associated with this property.</p>
                            @else
                                <p>You are not currently assigned to any specific property. Please contact your administrator for assignment.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="info-title">Your Tasks</div>
                        </div>
                        <div class="info-content">
                            <p>Access your assigned tasks, view schedules, and update task progress through the navigation menu.</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-header">
                            <div class="info-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="info-title">Support</div>
                        </div>
                        <div class="info-content">
                            <p>Need help? Contact your supervisor or manager for assistance with tasks, training, or technical support.</p>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="property-info">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i>
                        Your Information
                    </div>
                    
                    <div class="card-content">
                        <div class="detail-row">
                            <div class="detail-label">Full Name</div>
                            <div class="detail-value">{{ $user->full_name }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Username</div>
                            <div class="detail-value">{{ $user->username }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $user->email }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">{{ $user->phone }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Role</div>
                            <div class="detail-value">
                                <span class="badge badge-info">{{ $user->role->name }}</span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Property</div>
                            <div class="detail-value">
                                {{ $user->property ? $user->property->name : 'Not Assigned' }}
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Account Status</div>
                            <div class="detail-value">
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-warning' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Member Since</div>
                            <div class="detail-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Last Login</div>
                            <div class="detail-value">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t g:i A') : 'This is your first login' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>