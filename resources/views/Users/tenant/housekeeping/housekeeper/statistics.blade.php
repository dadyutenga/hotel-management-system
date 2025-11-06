<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Statistics - HotelPro</title>
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
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
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        
        .icon-total {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        .icon-completed {
            background: #d4edda;
            color: #28a745;
        }
        
        .icon-progress {
            background: #d1ecf1;
            color: #17a2b8;
        }
        
        .icon-pending {
            background: #fff3cd;
            color: #ffc107;
        }
        
        .stat-card h3 {
            font-size: 42px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            font-size: 16px;
            color: #666;
        }
        
        .performance-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .card-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .progress-bar-container {
            margin-bottom: 20px;
        }
        
        .progress-bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .progress-bar-label span:first-child {
            font-weight: 600;
            color: #333;
        }
        
        .progress-bar-label span:last-child {
            color: #666;
        }
        
        .progress-bar {
            width: 100%;
            height: 12px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4caf50, #81c784);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .monthly-breakdown {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .month-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .month-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .month-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .month-stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .month-stat-item .number {
            font-size: 28px;
            font-weight: 700;
            color: #333;
        }
        
        .month-stat-item .label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        
        .achievement-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .achievement-badge i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .achievement-badge h3 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .achievement-badge p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        @media (max-width: 968px) {
            .main-content {
                margin-left: 0;
            }
            
            .monthly-breakdown {
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
                        <h1>My Statistics</h1>
                        <p>Track your housekeeping performance</p>
                    </div>
                    <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tasks
                    </a>
                </div>

                <!-- Achievement Badge -->
                @if($statistics['overall']['completion_rate'] >= 90)
                    <div class="achievement-badge">
                        <i class="fas fa-trophy"></i>
                        <h3>Outstanding Performance!</h3>
                        <p>You have a {{ number_format($statistics['overall']['completion_rate'], 1) }}% completion rate. Keep up the excellent work!</p>
                    </div>
                @endif

                <!-- Overall Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-total">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>{{ $statistics['overall']['total_tasks'] }}</h3>
                        <p>Total Tasks</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>{{ $statistics['overall']['completed_tasks'] }}</h3>
                        <p>Completed</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-progress">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <h3>{{ $statistics['overall']['in_progress_tasks'] }}</h3>
                        <p>In Progress</p>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>{{ $statistics['overall']['pending_tasks'] }}</h3>
                        <p>Pending</p>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="performance-card">
                    <div class="card-title">
                        <i class="fas fa-chart-line"></i> Performance Metrics
                    </div>
                    
                    <div class="progress-bar-container">
                        <div class="progress-bar-label">
                            <span>Completion Rate</span>
                            <span>{{ number_format($statistics['overall']['completion_rate'], 1) }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $statistics['overall']['completion_rate'] }}%"></div>
                        </div>
                    </div>

                    @if($statistics['overall']['total_tasks'] > 0)
                        @php
                            $avgTimeMinutes = $statistics['overall']['average_completion_time'];
                            $hours = floor($avgTimeMinutes / 60);
                            $minutes = $avgTimeMinutes % 60;
                        @endphp
                        <div class="progress-bar-container">
                            <div class="progress-bar-label">
                                <span>Average Completion Time</span>
                                <span>
                                    @if($hours > 0)
                                        {{ $hours }}h {{ $minutes }}m
                                    @else
                                        {{ $minutes }} minutes
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Monthly Breakdown -->
                <div class="performance-card">
                    <div class="card-title">
                        <i class="fas fa-calendar-alt"></i> Monthly Breakdown
                    </div>
                    <div class="monthly-breakdown">
                        @foreach($statistics['monthly'] as $month => $data)
                            <div class="month-card">
                                <div class="month-header">
                                    <i class="fas fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                                </div>
                                <div class="month-stats">
                                    <div class="month-stat-item">
                                        <div class="number">{{ $data['total'] }}</div>
                                        <div class="label">Total Tasks</div>
                                    </div>
                                    <div class="month-stat-item">
                                        <div class="number" style="color: #28a745;">{{ $data['completed'] }}</div>
                                        <div class="label">Completed</div>
                                    </div>
                                    <div class="month-stat-item">
                                        <div class="number" style="color: #17a2b8;">{{ $data['in_progress'] }}</div>
                                        <div class="label">In Progress</div>
                                    </div>
                                    <div class="month-stat-item">
                                        <div class="number" style="color: #ffc107;">{{ $data['pending'] }}</div>
                                        <div class="label">Pending</div>
                                    </div>
                                </div>
                                @if($data['total'] > 0)
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef; text-align: center;">
                                        <strong>Completion Rate:</strong> 
                                        <span style="color: #28a745; font-weight: 600;">
                                            {{ number_format(($data['completed'] / $data['total']) * 100, 1) }}%
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Task Type Breakdown -->
                <div class="performance-card">
                    <div class="card-title">
                        <i class="fas fa-th-list"></i> Task Types Completed
                    </div>
                    <div class="monthly-breakdown">
                        @php
                            $taskTypes = [
                                'REGULAR_CLEANING' => ['icon' => 'broom', 'name' => 'Regular Cleaning'],
                                'DEEP_CLEANING' => ['icon' => 'spray-can', 'name' => 'Deep Cleaning'],
                                'TURNDOWN_SERVICE' => ['icon' => 'bed', 'name' => 'Turndown Service'],
                                'CHECK_OUT_CLEANING' => ['icon' => 'door-open', 'name' => 'Check-Out Cleaning'],
                                'MAINTENANCE' => ['icon' => 'tools', 'name' => 'Maintenance'],
                                'INSPECTION' => ['icon' => 'clipboard-check', 'name' => 'Inspection']
                            ];
                            
                            $completedTasks = collect($statistics['overall']['tasks_by_type'] ?? []);
                        @endphp
                        
                        @foreach($taskTypes as $type => $info)
                            @php $count = $completedTasks->get($type, 0); @endphp
                            <div class="month-card">
                                <div class="month-header">
                                    <i class="fas fa-{{ $info['icon'] }}"></i>
                                    {{ $info['name'] }}
                                </div>
                                <div style="text-align: center; padding: 20px 0;">
                                    <div style="font-size: 48px; font-weight: 700; color: #4caf50;">{{ $count }}</div>
                                    <div style="color: #666; margin-top: 10px;">Tasks Completed</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
