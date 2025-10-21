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
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 10px 0;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .card-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .detail-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            flex: 0 0 200px;
            font-weight: 600;
            color: #495057;
        }
        
        .detail-value {
            flex: 1;
            color: #212529;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-in-progress {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-verified {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-low {
            background: #d4edda;
            color: #155724;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #1e7e34;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-1px);
        }
        
        .ml-2 {
            margin-left: 0.5rem !important;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: start;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .container {
                padding: 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .detail-row {
                flex-direction: column;
                padding: 10px 0;
            }
            
            .detail-label {
                flex: none;
                margin-bottom: 5px;
            }
            
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 0;
                margin-bottom: 10px;
            }
            
            .text-right {
                text-align: left !important;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebars.supervisor')
        
        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1 class="page-title">Task Details</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('user.dashboard') }}">Home</a>
                        <span>/</span>
                        <a href="{{ route('supervisor.housekeeping.index') }}">Housekeeping Tasks</a>
                        <span>/</span>
                        <span>Task #{{ $task->id }}</span>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-broom"></i> Task Information
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-label">Task ID:</div>
                            <div class="detail-value">#{{ $task->id }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Property:</div>
                            <div class="detail-value">{{ $task->room->property->name }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Room:</div>
                            <div class="detail-value">
                                {{ $task->room->room_number }} - {{ $task->room->roomType->name }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Task Type:</div>
                            <div class="detail-value">
                                {{ ucwords(str_replace('_', ' ', $task->task_type)) }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Assigned To:</div>
                            <div class="detail-value">{{ $task->assignedTo->full_name }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                @if($task->status == 'PENDING')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($task->status == 'IN_PROGRESS')
                                    <span class="badge badge-in-progress">In Progress</span>
                                @elseif($task->status == 'COMPLETED')
                                    <span class="badge badge-completed">Completed</span>
                                @elseif($task->status == 'VERIFIED')
                                    <span class="badge badge-verified">Verified</span>
                                @elseif($task->status == 'CANCELLED')
                                    <span class="badge badge-cancelled">Cancelled</span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Priority:</div>
                            <div class="detail-value">
                                @if($task->priority == 'HIGH')
                                    <span class="badge badge-high">High</span>
                                @elseif($task->priority == 'MEDIUM')
                                    <span class="badge badge-medium">Medium</span>
                                @else
                                    <span class="badge badge-low">Low</span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Scheduled Date:</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($task->scheduled_date)->format('F j, Y') }}
                                @if($task->scheduled_time)
                                    at {{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}
                                @endif
                            </div>
                        </div>

                        @if($task->started_at)
                            <div class="detail-row">
                                <div class="detail-label">Started At:</div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($task->started_at)->format('F j, Y g:i A') }}
                                </div>
                            </div>
                        @endif

                        @if($task->completed_at)
                            <div class="detail-row">
                                <div class="detail-label">Completed At:</div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($task->completed_at)->format('F j, Y g:i A') }}
                                </div>
                            </div>
                        @endif

                        @if($task->verified_at)
                            <div class="detail-row">
                                <div class="detail-label">Verified At:</div>
                                <div class="detail-value">
                                    {{ \Carbon\Carbon::parse($task->verified_at)->format('F j, Y g:i A') }}
                                </div>
                            </div>
                        @endif

                        @if($task->notes)
                            <div class="detail-row">
                                <div class="detail-label">Notes:</div>
                                <div class="detail-value">{{ $task->notes }}</div>
                            </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-label">Created At:</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($task->created_at)->format('F j, Y g:i A') }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Last Updated:</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($task->updated_at)->format('F j, Y g:i A') }}
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                @if($task->status == 'COMPLETED')
                                    <form action="{{ route('supervisor.housekeeping.verify', $task) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to verify this task?')">
                                            <i class="fas fa-check-double"></i> Verify Task
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($task->status, ['PENDING', 'IN_PROGRESS']))
                                    <form action="{{ route('supervisor.housekeeping.destroy', $task) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger ml-2" onclick="return confirm('Are you sure you want to delete this task?')">
                                            <i class="fas fa-trash"></i> Delete Task
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('supervisor.housekeeping.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
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
