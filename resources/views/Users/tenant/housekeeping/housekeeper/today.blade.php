<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Tasks - HotelPro</title>
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
        
        .header .date {
            font-size: 16px;
            color: #666;
            margin-top: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .icon-total {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        .icon-pending {
            background: #fff3cd;
            color: #ffc107;
        }
        
        .icon-progress {
            background: #d1ecf1;
            color: #17a2b8;
        }
        
        .icon-completed {
            background: #d4edda;
            color: #28a745;
        }
        
        .stat-info h3 {
            font-size: 32px;
            font-weight: 600;
            color: #333;
        }
        
        .stat-info p {
            color: #666;
            font-size: 14px;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .tasks-section {
            margin-bottom: 30px;
        }
        
        .section-header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .task-title {
            flex: 1;
        }
        
        .task-title h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .task-title p {
            font-size: 14px;
            color: #666;
        }
        
        .task-badges {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
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
        
        .task-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 15px 0;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 15px;
        }
        
        .task-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .task-detail-item i {
            color: #4caf50;
            width: 20px;
        }
        
        .task-detail-item span {
            font-size: 14px;
            color: #666;
        }
        
        .task-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
        
        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 60px 20px;
            text-align: center;
            color: #999;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 968px) {
            .main-content {
                margin-left: 0;
            }
            
            .task-header {
                flex-direction: column;
            }
            
            .task-badges {
                margin-top: 10px;
            }
            
            .task-actions {
                flex-direction: column;
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
        <!-- Include Housekeeper Sidebar -->
        @include('Users.shared.sidebars.housekeeper')

        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <div>
                        <h1>Today's Tasks</h1>
                        <div class="date">
                            <i class="fas fa-calendar"></i> {{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                    <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> All Tasks
                    </a>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-total">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $tasks->count() }}</h3>
                            <p>Total Today</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $tasks->where('status', 'PENDING')->count() }}</h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-progress">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $tasks->where('status', 'IN_PROGRESS')->count() }}</h3>
                            <p>In Progress</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $tasks->where('status', 'COMPLETED')->count() }}</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                @if($tasks->count() > 0)
                    <!-- Pending Tasks -->
                    @php $pendingTasks = $tasks->where('status', 'PENDING'); @endphp
                    @if($pendingTasks->count() > 0)
                        <div class="tasks-section">
                            <div class="section-header">
                                <h2><i class="fas fa-clock"></i> Pending Tasks ({{ $pendingTasks->count() }})</h2>
                            </div>
                            @foreach($pendingTasks as $task)
                                <div class="task-card">
                                    <div class="task-header">
                                        <div class="task-title">
                                            <h3>Room {{ $task->room->room_number }}</h3>
                                            <p>{{ str_replace('_', ' ', $task->task_type) }} - {{ $task->property->name }}</p>
                                        </div>
                                        <div class="task-badges">
                                            <span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                                            <span class="badge badge-pending">PENDING</span>
                                        </div>
                                    </div>
                                    <div class="task-details">
                                        <div class="task-detail-item">
                                            <i class="fas fa-door-open"></i>
                                            <span>{{ $task->room->roomType->name ?? 'N/A' }}</span>
                                        </div>
                                        @if($task->scheduled_time)
                                            <div class="task-detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $task->scheduled_time }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="task-actions">
                                        <a href="{{ route('tenant.housekeeper.tasks.show', $task) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <form method="POST" action="{{ route('tenant.housekeeper.tasks.start', $task) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-play"></i> Start Task
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- In Progress Tasks -->
                    @php $inProgressTasks = $tasks->where('status', 'IN_PROGRESS'); @endphp
                    @if($inProgressTasks->count() > 0)
                        <div class="tasks-section">
                            <div class="section-header">
                                <h2><i class="fas fa-spinner"></i> In Progress ({{ $inProgressTasks->count() }})</h2>
                            </div>
                            @foreach($inProgressTasks as $task)
                                <div class="task-card">
                                    <div class="task-header">
                                        <div class="task-title">
                                            <h3>Room {{ $task->room->room_number }}</h3>
                                            <p>{{ str_replace('_', ' ', $task->task_type) }} - {{ $task->property->name }}</p>
                                        </div>
                                        <div class="task-badges">
                                            <span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                                            <span class="badge badge-in-progress">IN PROGRESS</span>
                                        </div>
                                    </div>
                                    <div class="task-details">
                                        <div class="task-detail-item">
                                            <i class="fas fa-door-open"></i>
                                            <span>{{ $task->room->roomType->name ?? 'N/A' }}</span>
                                        </div>
                                        @if($task->started_at)
                                            <div class="task-detail-item">
                                                <i class="fas fa-hourglass-start"></i>
                                                <span>Started {{ $task->started_at->diffForHumans() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="task-actions">
                                        <a href="{{ route('tenant.housekeeper.tasks.show', $task) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <form method="POST" action="{{ route('tenant.housekeeper.tasks.complete', $task) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Mark task as completed?')">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Completed Tasks -->
                    @php $completedTasks = $tasks->whereIn('status', ['COMPLETED', 'VERIFIED']); @endphp
                    @if($completedTasks->count() > 0)
                        <div class="tasks-section">
                            <div class="section-header">
                                <h2><i class="fas fa-check-circle"></i> Completed ({{ $completedTasks->count() }})</h2>
                            </div>
                            @foreach($completedTasks as $task)
                                <div class="task-card">
                                    <div class="task-header">
                                        <div class="task-title">
                                            <h3>Room {{ $task->room->room_number }}</h3>
                                            <p>{{ str_replace('_', ' ', $task->task_type) }} - {{ $task->property->name }}</p>
                                        </div>
                                        <div class="task-badges">
                                            <span class="badge badge-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                                            <span class="badge badge-completed">{{ $task->status }}</span>
                                        </div>
                                    </div>
                                    <div class="task-details">
                                        <div class="task-detail-item">
                                            <i class="fas fa-door-open"></i>
                                            <span>{{ $task->room->roomType->name ?? 'N/A' }}</span>
                                        </div>
                                        @if($task->completed_at)
                                            <div class="task-detail-item">
                                                <i class="fas fa-check"></i>
                                                <span>Completed at {{ $task->completed_at->format('g:i A') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="task-actions">
                                        <a href="{{ route('tenant.housekeeper.tasks.show', $task) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <h3>No tasks for today</h3>
                        <p>You don't have any tasks scheduled for today. Enjoy your day!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
