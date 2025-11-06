<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Task Details - HotelPro</title>
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
            max-width: 1000px;
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
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
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
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            display: block;
        }
        
        .form-group label .required {
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            font-family: 'Figtree', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4caf50;
        }
        
        .form-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
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
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <!-- Include Housekeeper Sidebar -->
        @include('Users.shared.sidebars.housekeeper')

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

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Please correct the following errors:</strong>
                            <ul style="margin-top: 5px; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Header -->
                <div class="header">
                    <div class="header-info">
                        <h1>Task #{{ substr($task->id, 0, 8) }}</h1>
                        <p>Assigned {{ \Carbon\Carbon::parse($task->assignedStaff->where('id', auth()->id())->first()->pivot->assigned_at ?? $task->created_at)->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('tenant.maintenance.housekeeper.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Task Details -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i> Task Details
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Status:</div>
                        <div class="detail-value">
                            <span class="status-badge badge-{{ strtolower($task->status) }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Priority:</div>
                        <div class="detail-value">
                            <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Issue Type:</div>
                        <div class="detail-value">{{ $task->issue_type }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">Property:</div>
                        <div class="detail-value">{{ $task->property->name }}</div>
                    </div>

                    @if($task->room)
                    <div class="detail-row">
                        <div class="detail-label">Room:</div>
                        <div class="detail-value">Room {{ $task->room->room_number }}</div>
                    </div>
                    @endif

                    @if($task->location_details)
                    <div class="detail-row">
                        <div class="detail-label">Location:</div>
                        <div class="detail-value">{{ $task->location_details }}</div>
                    </div>
                    @endif

                    <div class="detail-row">
                        <div class="detail-label">Reported By:</div>
                        <div class="detail-value">
                            {{ $task->reportedBy->name }}<br>
                            <small style="color: #999;">{{ $task->created_at->format('M d, Y g:i A') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-file-alt"></i> Description
                    </div>
                    <p style="color: #333; white-space: pre-wrap;">{{ $task->description }}</p>
                </div>

                <!-- Other Assigned Staff -->
                @if($task->assignedStaff->count() > 1)
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-users"></i> Other Assigned Housekeepers
                    </div>

                    <div class="staff-list">
                        @foreach($task->assignedStaff as $staff)
                            @if($staff->id !== auth()->id())
                                <div class="staff-item">
                                    <div class="staff-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="staff-info">
                                        <div class="staff-name">{{ $staff->name }}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Actions -->
                @if(in_array($task->status, ['OPEN', 'ASSIGNED']))
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-play-circle"></i> Start Working
                    </div>
                    <p style="margin-bottom: 20px; color: #666;">Click the button below to mark this task as in progress.</p>
                    <form action="{{ route('tenant.maintenance.housekeeper.start', $task->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play"></i> Start Task
                        </button>
                    </form>
                </div>
                @endif

                @if($task->status === 'IN_PROGRESS')
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-check-circle"></i> Complete Task
                    </div>
                    <form action="{{ route('tenant.maintenance.housekeeper.complete', $task->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label for="resolution_notes">Resolution Notes <span class="required">*</span></label>
                            <textarea name="resolution_notes" id="resolution_notes" class="form-control" rows="5" required>{{ old('resolution_notes') }}</textarea>
                            @error('resolution_notes')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            <span class="form-text">Describe what was done to fix the issue</span>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Complete Task
                        </button>
                    </form>
                </div>
                @endif

                <!-- Resolution (if completed) -->
                @if($task->resolution_notes && $task->status === 'COMPLETED')
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-clipboard-check"></i> Resolution
                    </div>
                    <p style="color: #333; white-space: pre-wrap;">{{ $task->resolution_notes }}</p>
                    @if($task->completed_at)
                    <small style="color: #999; margin-top: 10px; display: block;">
                        Completed on {{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y g:i A') }}
                    </small>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
