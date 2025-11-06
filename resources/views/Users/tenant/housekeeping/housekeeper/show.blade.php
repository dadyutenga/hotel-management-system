<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details - HotelPro</title>
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
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }
        
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
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #ffc107;
            color: #333;
        }
        
        .badge-in-progress {
            background: #17a2b8;
            color: white;
        }
        
        .badge-completed {
            background: #28a745;
            color: white;
        }
        
        .badge-verified {
            background: #6f42c1;
            color: white;
        }
        
        .badge-high {
            background: #dc3545;
            color: white;
        }
        
        .badge-medium {
            background: #ffc107;
            color: #333;
        }
        
        .badge-low {
            background: #28a745;
            color: white;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .details-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #666;
        }
        
        .detail-value {
            color: #333;
            text-align: right;
        }
        
        .actions-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }
        
        .action-group {
            margin-bottom: 20px;
        }
        
        .action-group:last-child {
            margin-bottom: 0;
        }
        
        .action-group h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .notes-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .notes-section h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .notes-content {
            white-space: pre-wrap;
            color: #666;
        }
        
        .status-indicator {
            width: 100%;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 968px) {
            .main-content {
                margin-left: 0;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
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
                <!-- Header -->
                <div class="header">
                    <div>
                        <h1>Task Details</h1>
                        <p>Room {{ $housekeeping->room->room_number }} - {{ str_replace('_', ' ', $housekeeping->task_type) }}</p>
                    </div>
                    <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to My Tasks
                    </a>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Status Indicator -->
                <div class="status-indicator status-{{ strtolower(str_replace('_', '-', $housekeeping->status)) }}">
                    <i class="fas 
                        @if($housekeeping->status == 'PENDING') fa-clock
                        @elseif($housekeeping->status == 'IN_PROGRESS') fa-spinner
                        @else fa-check-circle
                        @endif
                    "></i>
                    Task Status: {{ str_replace('_', ' ', $housekeeping->status) }}
                </div>

                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Task Details -->
                    <div class="details-card">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i> Task Information
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Property</span>
                            <span class="detail-value">{{ $housekeeping->property->name }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Room Number</span>
                            <span class="detail-value"><strong>{{ $housekeeping->room->room_number }}</strong></span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Room Type</span>
                            <span class="detail-value">{{ $housekeeping->room->roomType->name ?? 'N/A' }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Task Type</span>
                            <span class="detail-value">{{ str_replace('_', ' ', $housekeeping->task_type) }}</span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Priority</span>
                            <span class="detail-value">
                                <span class="badge badge-{{ strtolower($housekeeping->priority) }}">
                                    {{ $housekeeping->priority }}
                                </span>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">Scheduled Date</span>
                            <span class="detail-value">{{ $housekeeping->scheduled_date->format('F j, Y') }}</span>
                        </div>

                        @if($housekeeping->scheduled_time)
                            <div class="detail-row">
                                <span class="detail-label">Scheduled Time</span>
                                <span class="detail-value">{{ $housekeeping->scheduled_time }}</span>
                            </div>
                        @endif

                        @if($housekeeping->started_at)
                            <div class="detail-row">
                                <span class="detail-label">Started At</span>
                                <span class="detail-value">{{ $housekeeping->started_at->format('M j, Y H:i') }}</span>
                            </div>
                        @endif

                        @if($housekeeping->completed_at)
                            <div class="detail-row">
                                <span class="detail-label">Completed At</span>
                                <span class="detail-value">{{ $housekeeping->completed_at->format('M j, Y H:i') }}</span>
                            </div>
                        @endif

                        @if($housekeeping->notes)
                            <div class="notes-section">
                                <h3><i class="fas fa-sticky-note"></i> Task Notes</h3>
                                <div class="notes-content">{{ $housekeeping->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Sidebar -->
                    <div class="actions-card">
                        <div class="section-title">
                            <i class="fas fa-tasks"></i> Quick Actions
                        </div>

                        <!-- Start Task Button (Only for Pending) -->
                        @if($housekeeping->status == 'PENDING')
                            <div class="action-group">
                                <h3>Start Working</h3>
                                <form method="POST" action="{{ route('tenant.housekeeper.tasks.start', $housekeeping) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" style="width: 100%;" onclick="return confirm('Start working on this task?')">
                                        <i class="fas fa-play"></i> Start Task
                                    </button>
                                </form>
                                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                                    This will change the status to "In Progress"
                                </p>
                            </div>
                        @endif

                        <!-- Complete Task Button (Only for In Progress) -->
                        @if($housekeeping->status == 'IN_PROGRESS')
                            <div class="action-group">
                                <h3>Complete Task</h3>
                                <form method="POST" action="{{ route('tenant.housekeeper.tasks.complete', $housekeeping) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="width: 100%;" onclick="return confirm('Mark this task as completed?')">
                                        <i class="fas fa-check"></i> Mark as Complete
                                    </button>
                                </form>
                                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                                    This will mark the room as clean and ready
                                </p>
                            </div>
                        @endif

                        <!-- Progress Notes (Only for In Progress) -->
                        @if($housekeeping->status == 'IN_PROGRESS')
                            <div class="action-group">
                                <h3>Add Progress Notes</h3>
                                <form method="POST" action="{{ route('tenant.housekeeper.tasks.progress', $housekeeping) }}">
                                    @csrf
                                    <textarea name="progress_notes" class="form-control" placeholder="Add any notes about the progress or issues found..." required></textarea>
                                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                                        <i class="fas fa-comment"></i> Add Notes
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Task Completed Message -->
                        @if(in_array($housekeeping->status, ['COMPLETED', 'VERIFIED']))
                            <div class="action-group">
                                <div style="background: #d4edda; padding: 20px; border-radius: 8px; text-align: center;">
                                    <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 10px;"></i>
                                    <h3 style="color: #155724; margin-bottom: 5px;">Task Completed!</h3>
                                    <p style="font-size: 14px; color: #155724;">
                                        This task has been marked as {{ strtolower($housekeeping->status) }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
