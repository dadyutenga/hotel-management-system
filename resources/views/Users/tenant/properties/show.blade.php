<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }} - Property Details - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
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
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border-right: 4px solid #f44336;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .breadcrumb a {
            color: #1a237e;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .content {
            padding: 30px;
        }
        
        .property-overview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .property-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .property-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .property-address {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .property-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-active {
            background: rgba(76, 175, 80, 0.2);
            color: #2e7d32;
            border: 2px solid rgba(76, 175, 80, 0.5);
        }
        
        .status-inactive {
            background: rgba(244, 67, 54, 0.2);
            color: #c62828;
            border: 2px solid rgba(244, 67, 54, 0.5);
        }
        
        .property-contact {
            padding: 30px;
            background: #f8f9fa;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a237e;
            color: white;
        }
        
        .contact-info h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .contact-info p {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        
        .data-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .table-content {
            padding: 25px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .badge-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-danger {
            background: #ffebee;
            color: #c62828;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #4caf50;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .content {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">HotelPro</div>
                <div class="brand-subtitle">Property Management</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tenant.properties.index') }}" class="nav-link active">
                        <i class="fas fa-building"></i>
                        Properties
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-door-open"></i>
                        Rooms
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        Reservations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </div>
                
                <div class="logout-section">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn" style="background: none; border: none; color: rgba(255,255,255,0.8); width: 100%; text-align: left; padding: 12px 20px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; font-family: inherit;">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Property Details</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.properties.index') }}">Properties</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>{{ $property->name }}</span>
                    </div>
                </div>
                <div class="header-actions">
                    @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                        <a href="{{ route('tenant.properties.edit', $property->id) }}" class="btn btn-secondary">
                            <i class="fas fa-edit"></i>
                            Edit Property
                        </a>
                    @endif
                    <a href="{{ route('tenant.properties.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Properties
                    </a>
                </div>
            </div>
            
            <div class="content">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Property Overview -->
                <div class="property-overview">
                    <div class="property-header">
                        <div class="property-name">{{ $property->name }}</div>
                        <div class="property-address">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $property->address }}
                        </div>
                        <div style="margin-top: 15px;">
                            <span class="property-status {{ $property->is_active ? 'status-active' : 'status-inactive' }}">
                                <i class="fas fa-{{ $property->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $property->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                <div style="margin-top: 15px;">
                                    <label class="toggle-switch">
                                        <input type="checkbox" 
                                               {{ $property->is_active ? 'checked' : '' }}
                                               onchange="togglePropertyStatus('{{ $property->id }}', this)">
                                        <span class="slider"></span>
                                    </label>
                                    <span style="margin-left: 10px; font-size: 14px;">Toggle Status</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="property-contact">
                        <div class="contact-grid">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-info">
                                    <h4>Phone</h4>
                                    <p>{{ $property->contact_phone }}</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info">
                                    <h4>Email</h4>
                                    <p>{{ $property->email }}</p>
                                </div>
                            </div>
                            
                            @if($property->website)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="contact-info">
                                        <h4>Website</h4>
                                        <p><a href="{{ $property->website }}" target="_blank" style="color: #1a237e;">Visit Website</a></p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-info">
                                    <h4>Timezone</h4>
                                    <p>{{ $property->timezone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div class="stat-title">Total Buildings</div>
                            <div class="stat-icon primary">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['total_buildings'] }}</div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-title">Total Floors</div>
                            <div class="stat-icon success">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['total_floors'] }}</div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-title">Total Rooms</div>
                            <div class="stat-icon warning">
                                <i class="fas fa-door-open"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['total_rooms'] }}</div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-header">
                            <div class="stat-title">Active Staff</div>
                            <div class="stat-icon info">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $stats['active_users'] }}</div>
                        <div style="font-size: 12px; color: #666;">
                            Total: {{ $stats['total_users'] }}
                        </div>
                    </div>
                </div>

                <!-- Buildings and Structure -->
                @if($property->buildings->count() > 0)
                    <div class="data-table">
                        <div class="table-header">
                            <i class="fas fa-building"></i>
                            Buildings & Structure ({{ $property->buildings->count() }})
                        </div>
                        
                        <div class="table-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Building Name</th>
                                        <th>Floors</th>
                                        <th>Rooms</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($property->buildings as $building)
                                        <tr>
                                            <td>
                                                <strong>{{ $building->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    {{ $building->floors->count() }} Floors
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    {{ $building->floors->sum(fn($floor) => $floor->rooms->count()) }} Rooms
                                                </span>
                                            </td>
                                            <td>{{ $building->description ?: 'No description' }}</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="data-table">
                        <div class="table-header">
                            <i class="fas fa-building"></i>
                            Buildings & Structure
                        </div>
                        
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <h3>No Buildings Found</h3>
                            <p>This property doesn't have any buildings yet.</p>
                            <div style="margin-top: 20px;">
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Add Building
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Staff Members -->
                @if($property->users->count() > 0)
                    <div class="data-table">
                        <div class="table-header">
                            <i class="fas fa-users"></i>
                            Staff Members ({{ $property->users->count() }})
                        </div>
                        
                        <div class="table-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($property->users as $user)
                                        <tr>
                                            <td>
                                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    {{ $user->role->name }}
                                                </span>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td>
                                                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                                    <button class="btn btn-danger btn-sm" onclick="removeUser('{{ $user->id }}', '{{ $user->first_name }} {{ $user->last_name }}')">
                                                        <i class="fas fa-times"></i>
                                                        Remove
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="data-table">
                        <div class="table-header">
                            <i class="fas fa-users"></i>
                            Staff Members
                        </div>
                        
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>No Staff Assigned</h3>
                            <p>No staff members are currently assigned to this property.</p>
                            @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                <div style="margin-top: 20px;">
                                    <a href="#" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Assign Staff
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Recent Activity -->
                @if($recentActivity->count() > 0)
                    <div class="data-table">
                        <div class="table-header">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </div>
                        
                        <div class="table-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th>Role</th>
                                        <th>Last Login</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity as $user)
                                        <tr>
                                            <td>
                                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    {{ $user->role->name }}
                                                </span>
                                            </td>
                                            <td>{{ $user->last_login_at->diffForHumans() }}</td>
                                            <td>
                                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Toggle property status
        function togglePropertyStatus(propertyId, toggle) {
            fetch(`/properties/${propertyId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status display
                    const statusElement = document.querySelector('.property-status');
                    const statusIcon = statusElement.querySelector('i');
                    
                    if (data.is_active) {
                        statusElement.className = 'property-status status-active';
                        statusElement.innerHTML = '<i class="fas fa-check-circle"></i> Active';
                    } else {
                        statusElement.className = 'property-status status-inactive';
                        statusElement.innerHTML = '<i class="fas fa-times-circle"></i> Inactive';
                    }
                    
                    showAlert(data.message, 'success');
                } else {
                    // Revert toggle
                    toggle.checked = !toggle.checked;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggle.checked = !toggle.checked;
                showAlert('An error occurred while updating property status.', 'error');
            });
        }

        // Remove user from property
        function removeUser(userId, userName) {
            if (confirm(`Are you sure you want to remove "${userName}" from this property?`)) {
                fetch(`/properties/{{ $property->id }}/remove-user`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: userId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Reload page to update the user list
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while removing the user.', 'error');
                });
            }
        }

        // Show alert messages
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            const content = document.querySelector('.content');
            content.insertBefore(alertDiv, content.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>