<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Request Details - HotelPro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-info h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .header-info p {
            color: #666;
            font-size: 14px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .detail-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
            width: 140px;
            flex-shrink: 0;
        }
        
        .detail-value {
            color: #333;
            flex: 1;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-assigned { background: #d1ecf1; color: #0c5460; }
        .badge-in_progress { background: #cce5ff; color: #004085; }
        .badge-on_hold { background: #e2e3e5; color: #383d41; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
        
        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .priority-low { background: #d4edda; color: #155724; }
        .priority-medium { background: #fff3cd; color: #856404; }
        .priority-high { background: #ffe5d0; color: #cc5200; }
        .priority-urgent { background: #f8d7da; color: #721c24; }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #4caf50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .staff-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .staff-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .staff-icon {
            width: 35px;
            height: 35px;
            background: #4caf50;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .staff-info {
            flex: 1;
        }
        
        .staff-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .staff-email {
            color: #666;
            font-size: 12px;
        }
        
        .staff-date {
            font-size: 12px;
            color: #999;
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .action-form {
            margin: 0;
        }
        
        @media (max-width: 1024px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .header-actions {
                width: 100%;
                flex-wrap: wrap;
            }
            
            .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Supervisor Sidebar -->
        @include('Users.shared.sidebars.supervisor')

        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Header -->
                <div class="header">
                    <div class="header-info">
                        <h1>Maintenance Request #{{ substr($maintenanceRequest->id, 0, 8) }}</h1>
                        <p>Created {{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('tenant.maintenance.edit', $maintenanceRequest->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('tenant.maintenance.destroy', $maintenanceRequest->id) }}" method="POST" class="action-form" onsubmit="return confirm('Are you sure you want to delete this maintenance request?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="details-grid">
                    <!-- Main Details -->
                    <div>
                        <!-- Request Details -->
                        <div class="card" style="margin-bottom: 30px;">
                            <div class="card-title">
                                <i class="fas fa-info-circle"></i> Request Details
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Status:</div>
                                <div class="detail-value">
                                    <span class="status-badge badge-{{ strtolower($maintenanceRequest->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Priority:</div>
                                <div class="detail-value">
                                    <span class="priority-badge priority-{{ strtolower($maintenanceRequest->priority) }}">
                                        {{ ucfirst($maintenanceRequest->priority) }}
                                    </span>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Issue Type:</div>
                                <div class="detail-value">{{ $maintenanceRequest->issue_type }}</div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-label">Property:</div>
                                <div class="detail-value">{{ $maintenanceRequest->property->name }}</div>
                            </div>

                            @if($maintenanceRequest->room)
                            <div class="detail-row">
                                <div class="detail-label">Room:</div>
                                <div class="detail-value">Room {{ $maintenanceRequest->room->room_number }}</div>
                            </div>
                            @endif

                            @if($maintenanceRequest->location_details)
                            <div class="detail-row">
                                <div class="detail-label">Location:</div>
                                <div class="detail-value">{{ $maintenanceRequest->location_details }}</div>
                            </div>
                            @endif

                            <div class="detail-row">
                                <div class="detail-label">Reported By:</div>
                                <div class="detail-value">
                                    {{ $maintenanceRequest->reportedBy->name }}<br>
                                    <small style="color: #999;">{{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="card" style="margin-bottom: 30px;">
                            <div class="card-title">
                                <i class="fas fa-file-alt"></i> Description
                            </div>
                            <p style="color: #333; white-space: pre-wrap;">{{ $maintenanceRequest->description }}</p>
                        </div>

                        <!-- Resolution (if completed) -->
                        @if($maintenanceRequest->resolution_notes && $maintenanceRequest->status === 'COMPLETED')
                        <div class="card">
                            <div class="card-title">
                                <i class="fas fa-check-circle"></i> Resolution
                            </div>
                            <p style="color: #333; white-space: pre-wrap;">{{ $maintenanceRequest->resolution_notes }}</p>
                            @if($maintenanceRequest->completed_at)
                            <small style="color: #999; margin-top: 10px; display: block;">
                                Completed on {{ \Carbon\Carbon::parse($maintenanceRequest->completed_at)->format('M d, Y g:i A') }}
                            </small>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <!-- Assigned Staff -->
                        <div class="card" style="margin-bottom: 30px;">
                            <div class="card-title">
                                <i class="fas fa-users"></i> Assigned Housekeepers
                            </div>

                            @if($maintenanceRequest->assignedStaff->count() > 0)
                                <div class="staff-list">
                                    @foreach($maintenanceRequest->assignedStaff as $staff)
                                        <div class="staff-item">
                                            <div class="staff-icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="staff-info">
                                                <div class="staff-name">{{ $staff->name }}</div>
                                                <div class="staff-email">{{ $staff->email }}</div>
                                            </div>
                                            <div class="staff-date">
                                                {{ \Carbon\Carbon::parse($staff->pivot->assigned_at)->format('M d') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                    <p>No housekeepers assigned yet</p>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-title">
                                <i class="fas fa-bolt"></i> Quick Actions
                            </div>

                            <div class="quick-actions">
                                <!-- Status Updates -->
                                @if($maintenanceRequest->status !== 'COMPLETED')
                                <form action="{{ route('tenant.maintenance.update-status', $maintenanceRequest->id) }}" method="POST" class="action-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                        <i class="fas fa-check"></i> Mark as Completed
                                    </button>
                                </form>
                                @endif

                                @if($maintenanceRequest->status === 'IN_PROGRESS')
                                <form action="{{ route('tenant.maintenance.update-status', $maintenanceRequest->id) }}" method="POST" class="action-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ON_HOLD">
                                    <button type="submit" class="btn btn-warning btn-sm" style="width: 100%;">
                                        <i class="fas fa-pause"></i> Put On Hold
                                    </button>
                                </form>
                                @endif

                                @if($maintenanceRequest->status === 'ON_HOLD')
                                <form action="{{ route('tenant.maintenance.update-status', $maintenanceRequest->id) }}" method="POST" class="action-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="IN_PROGRESS">
                                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                                        <i class="fas fa-play"></i> Resume Work
                                    </button>
                                </form>
                                @endif

                                @if($maintenanceRequest->status !== 'CANCELLED')
                                <form action="{{ route('tenant.maintenance.update-status', $maintenanceRequest->id) }}" method="POST" class="action-form" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="CANCELLED">
                                    <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">
                                        <i class="fas fa-times"></i> Cancel Request
                                    </button>
                                </form>
                                @endif

                                <!-- Edit and Delete -->
                                <a href="{{ route('tenant.maintenance.edit', $maintenanceRequest->id) }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                                    <i class="fas fa-edit"></i> Edit Request
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
