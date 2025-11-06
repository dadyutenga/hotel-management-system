<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Maintenance Tasks - HotelPro</title>
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
            color: white;
        }
        
        .stat-icon.pending { background: linear-gradient(135deg, #ffc107, #ff9800); }
        .stat-icon.in-progress { background: linear-gradient(135deg, #2196f3, #1976d2); }
        .stat-icon.completed { background: linear-gradient(135deg, #4caf50, #388e3c); }
        
        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #666;
            font-size: 14px;
        }
        
        .filters-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4caf50;
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
            justify-content: center;
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
        
        .tasks-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .table-responsive {
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
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tbody tr {
            transition: background-color 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f8f9fa;
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
        
        .action-btn {
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
        
        .action-btn-primary {
            background: #4caf50;
            color: white;
        }
        
        .action-btn-primary:hover {
            background: #45a049;
        }
        
        .action-btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .action-btn-info:hover {
            background: #138496;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
            color: #ddd;
        }
        
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #666;
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
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px;
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
                    <h1>My Maintenance Tasks</h1>
                    <p>View and manage your assigned maintenance work</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $stats['pending'] ?? 0 }}</h3>
                            <p>Pending Tasks</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon in-progress">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $stats['in_progress'] ?? 0 }}</h3>
                            <p>In Progress</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $stats['completed'] ?? 0 }}</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-card">
                    <form method="GET" action="{{ route('tenant.maintenance.housekeeper.index') }}">
                        <div class="filters-grid">
                            <div class="form-group">
                                <label for="status">Filter by Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>Open</option>
                                <option value="ASSIGNED" {{ request('status') == 'ASSIGNED' ? 'selected' : '' }}>Assigned</option>
                                <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                <option value="ON_HOLD" {{ request('status') == 'ON_HOLD' ? 'selected' : '' }}>On Hold</option>
                                <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                            </select>
                            </div>

                            <div class="form-group">
                                <label for="priority">Filter by Priority</label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="">All Priorities</option>
                                    <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                                    <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                    <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                    <option value="URGENT" {{ request('priority') == 'URGENT' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tasks Table -->
                <div class="tasks-card">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Property</th>
                                        <th>Location</th>
                                        <th>Issue Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assigned</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td style="font-family: monospace; color: #666;">{{ substr($task->id, 0, 8) }}</td>
                                            <td>{{ $task->property->name }}</td>
                                            <td>
                                                @if($task->room)
                                                    Room {{ $task->room->room_number }}
                                                @elseif($task->location_details)
                                                    {{ $task->location_details }}
                                                @else
                                                    General
                                                @endif
                                            </td>
                                            <td>{{ $task->issue_type }}</td>
                                            <td>
                                                <span class="priority-badge priority-{{ strtolower($task->priority) }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge badge-{{ strtolower($task->status) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $assignedToMe = $task->assignedStaff->where('id', auth()->id())->first();
                                                @endphp
                                                @if($assignedToMe)
                                                    {{ \Carbon\Carbon::parse($assignedToMe->pivot->assigned_at)->format('M d, Y') }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tenant.maintenance.housekeeper.show', $task->id) }}" class="action-btn action-btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                                @if(in_array($task->status, ['PENDING', 'ASSIGNED']))
                                                    <form action="{{ route('tenant.maintenance.housekeeper.start', $task->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="action-btn action-btn-primary">
                                                            <i class="fas fa-play"></i> Start
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 20px;">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No tasks found</h3>
                            <p>You don't have any assigned maintenance tasks yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
