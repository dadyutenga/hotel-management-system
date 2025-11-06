<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Maintenance Tasks - HotelPro</title>
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
            max-width: 1400px;
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
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
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
        
        .section-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .section-count {
            background: #4caf50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .task-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #4caf50;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .task-card.urgent {
            border-left-color: #dc3545;
        }
        
        .task-card.high {
            border-left-color: #ff9800;
        }
        
        .task-card.medium {
            border-left-color: #ffc107;
        }
        
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .task-id {
            font-family: monospace;
            font-size: 12px;
            color: #666;
            font-weight: 600;
        }
        
        .priority-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .priority-low { background: #d4edda; color: #155724; }
        .priority-medium { background: #fff3cd; color: #856404; }
        .priority-high { background: #ffe5d0; color: #cc5200; }
        .priority-urgent { background: #f8d7da; color: #721c24; }
        
        .task-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .task-location {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .task-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .task-time {
            font-size: 12px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }
        
        .btn-primary {
            background: #4caf50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            color: #ddd;
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #666;
        }
        
        .empty-state p {
            font-size: 14px;
        }
        
        /* Status specific colors */
        .section-card.pending .section-title i {
            color: #ffc107;
        }
        
        .section-card.in-progress .section-title i {
            color: #2196f3;
        }
        
        .section-card.completed .section-title i {
            color: #4caf50;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .task-grid {
                grid-template-columns: 1fr;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
                    <h1>Today's Maintenance Tasks</h1>
                    <p>{{ now()->format('l, F j, Y') }}</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Pending Tasks -->
                <div class="section-card pending">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-clock"></i>
                            Pending / Assigned
                        </div>
                        <span class="section-count">{{ $pendingTasks->count() }}</span>
                    </div>

                    @if($pendingTasks->count() > 0)
                        <div class="task-grid">
                            @foreach($pendingTasks as $task)
                                <div class="task-card {{ strtolower($task->priority) }}" onclick="window.location='{{ route('tenant.maintenance.housekeeper.show', $task->id) }}'">
                                    <div class="task-header">
                                        <span class="task-id">#{{ substr($task->id, 0, 8) }}</span>
                                        <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </div>
                                    
                                    <div class="task-title">{{ $task->issue_type }}</div>
                                    
                                    <div class="task-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>
                                            {{ $task->property->name }} - 
                                            @if($task->room)
                                                Room {{ $task->room->room_number }}
                                            @elseif($task->location_details)
                                                {{ $task->location_details }}
                                            @else
                                                General
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="task-description">{{ $task->description }}</div>
                                    
                                    <div class="task-footer">
                                        <div class="task-time">
                                            <i class="fas fa-clock"></i>
                                            {{ $task->created_at->diffForHumans() }}
                                        </div>
                                        <form action="{{ route('tenant.maintenance.housekeeper.start', $task->id) }}" method="POST" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-play"></i> Start
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <h3>All caught up!</h3>
                            <p>No pending tasks for today</p>
                        </div>
                    @endif
                </div>

                <!-- In Progress Tasks -->
                <div class="section-card in-progress">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-tools"></i>
                            In Progress
                        </div>
                        <span class="section-count" style="background: #2196f3;">{{ $inProgressTasks->count() }}</span>
                    </div>

                    @if($inProgressTasks->count() > 0)
                        <div class="task-grid">
                            @foreach($inProgressTasks as $task)
                                <div class="task-card {{ strtolower($task->priority) }}" onclick="window.location='{{ route('tenant.maintenance.housekeeper.show', $task->id) }}'">
                                    <div class="task-header">
                                        <span class="task-id">#{{ substr($task->id, 0, 8) }}</span>
                                        <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </div>
                                    
                                    <div class="task-title">{{ $task->issue_type }}</div>
                                    
                                    <div class="task-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>
                                            {{ $task->property->name }} - 
                                            @if($task->room)
                                                Room {{ $task->room->room_number }}
                                            @elseif($task->location_details)
                                                {{ $task->location_details }}
                                            @else
                                                General
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="task-description">{{ $task->description }}</div>
                                    
                                    <div class="task-footer">
                                        <div class="task-time">
                                            <i class="fas fa-spinner"></i>
                                            Started {{ $task->updated_at->diffForHumans() }}
                                        </div>
                                        <a href="{{ route('tenant.maintenance.housekeeper.show', $task->id) }}" class="btn btn-info" onclick="event.stopPropagation();">
                                            <i class="fas fa-check"></i> Complete
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No tasks in progress</h3>
                            <p>Start a pending task to see it here</p>
                        </div>
                    @endif
                </div>

                <!-- Completed Tasks (Today) -->
                <div class="section-card completed">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="fas fa-check-circle"></i>
                            Completed Today
                        </div>
                        <span class="section-count" style="background: #4caf50;">{{ $completedTasks->count() }}</span>
                    </div>

                    @if($completedTasks->count() > 0)
                        <div class="task-grid">
                            @foreach($completedTasks as $task)
                                <div class="task-card {{ strtolower($task->priority) }}" onclick="window.location='{{ route('tenant.maintenance.housekeeper.show', $task->id) }}'">
                                    <div class="task-header">
                                        <span class="task-id">#{{ substr($task->id, 0, 8) }}</span>
                                        <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </div>
                                    
                                    <div class="task-title">{{ $task->issue_type }}</div>
                                    
                                    <div class="task-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>
                                            {{ $task->property->name }} - 
                                            @if($task->room)
                                                Room {{ $task->room->room_number }}
                                            @elseif($task->location_details)
                                                {{ $task->location_details }}
                                            @else
                                                General
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="task-description">{{ $task->resolution_notes ?? $task->description }}</div>
                                    
                                    <div class="task-footer">
                                        <div class="task-time">
                                            <i class="fas fa-check"></i>
                                            Completed {{ \Carbon\Carbon::parse($task->completed_at)->format('g:i A') }}
                                        </div>
                                        <a href="{{ route('tenant.maintenance.housekeeper.show', $task->id) }}" class="btn btn-info" onclick="event.stopPropagation();">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <h3>No tasks completed today</h3>
                            <p>Tasks you complete today will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
