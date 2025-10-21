<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - HotelPro</title>
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
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.pending {
            background: linear-gradient(135deg, #ffc107, #ff9800);
        }
        
        .stat-icon.in-progress {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        
        .stat-icon.completed {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        
        .stat-details {
            flex: 1;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
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
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8f9fa;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        tr:hover {
            background: #f8f9fa;
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
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .ml-2 {
            margin-left: 0.5rem !important;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 1.5rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .empty-text {
            color: #adb5bd;
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
        
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebars.housekeeper')
        
        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1 class="page-title">My Tasks</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('user.dashboard') }}">Home</a>
                        <span>/</span>
                        <span>My Tasks</span>
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

                @if(session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>{{ session('info') }}</div>
                    </div>
                @endif

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ $stats['pending'] ?? 0 }}</h3>
                            <p class="stat-label">Pending Tasks</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon in-progress">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ $stats['in_progress'] ?? 0 }}</h3>
                            <p class="stat-label">In Progress</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-details">
                            <h3 class="stat-value">{{ $stats['completed_today'] ?? 0 }}</h3>
                            <p class="stat-label">Completed Today</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> My Assigned Tasks
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        @if($tasks->count() > 0)
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Task Type</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Scheduled</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $task)
                                            <tr>
                                                <td>
                                                    <strong>{{ $task->room->room_number }}</strong><br>
                                                    <small>{{ $task->room->roomType->name }}</small>
                                                </td>
                                                <td>{{ ucwords(str_replace('_', ' ', $task->task_type)) }}</td>
                                                <td>
                                                    @if($task->status == 'PENDING')
                                                        <span class="badge badge-pending">Pending</span>
                                                    @elseif($task->status == 'IN_PROGRESS')
                                                        <span class="badge badge-in-progress">In Progress</span>
                                                    @elseif($task->status == 'COMPLETED')
                                                        <span class="badge badge-completed">Completed</span>
                                                    @elseif($task->status == 'VERIFIED')
                                                        <span class="badge badge-verified">Verified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->priority == 'HIGH')
                                                        <span class="badge badge-high">High</span>
                                                    @elseif($task->priority == 'MEDIUM')
                                                        <span class="badge badge-medium">Medium</span>
                                                    @else
                                                        <span class="badge badge-low">Low</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M j, Y') }}
                                                    @if($task->scheduled_time)
                                                        <br><small>{{ \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('housekeeper.tasks.show', $task) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if(in_array($task->status, ['PENDING', 'IN_PROGRESS']))
                                                        <a href="{{ route('housekeeper.tasks.manage', $task) }}" class="btn btn-success btn-sm ml-2">
                                                            <i class="fas fa-tasks"></i> Manage
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($tasks->hasPages())
                                <div style="margin-top: 20px;">
                                    {{ $tasks->links() }}
                                </div>
                            @endif
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h3 class="empty-title">No Tasks Assigned</h3>
                                <p class="empty-text">You don't have any tasks assigned at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
