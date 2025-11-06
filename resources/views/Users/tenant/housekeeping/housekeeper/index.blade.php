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
        
        .filters-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filters-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        .form-control {
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
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
        
        .tasks-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .tasks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .tasks-header h2 {
            font-size: 22px;
            font-weight: 600;
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
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        tbody tr {
            transition: background 0.3s ease;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
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
        
        .action-buttons {
            display: flex;
            gap: 5px;
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
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
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
            
            .filter-form {
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
                        <h1>My Tasks</h1>
                        <p>Manage your assigned housekeeping tasks</p>
                    </div>
                    <a href="{{ route('tenant.housekeeper.tasks.today') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-day"></i> Today's Tasks
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
                        <div class="stat-icon icon-pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $tasks->where('status', 'PENDING')->count() }}</h3>
                            <p>Pending Tasks</p>
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
                            <p>Completed Today</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters-card">
                    <div class="filters-title">
                        <i class="fas fa-filter"></i> Filter Tasks
                    </div>
                    <form method="GET" action="{{ route('tenant.housekeeper.tasks.index') }}" class="filter-form">
                        <div class="form-group">
                            <label for="property_id">Property</label>
                            <select name="property_id" id="property_id" class="form-control">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                <option value="VERIFIED" {{ request('status') == 'VERIFIED' ? 'selected' : '' }}>Verified</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="">All Priorities</option>
                                <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end; gap: 10px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('tenant.housekeeper.tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tasks Table -->
                <div class="tasks-card">
                    <div class="tasks-header">
                        <h2>All Tasks ({{ $tasks->total() }})</h2>
                        <a href="{{ route('tenant.housekeeper.statistics') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> View Statistics
                        </a>
                    </div>

                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Property</th>
                                        <th>Task Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Scheduled</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td><strong>{{ $task->room->room_number }}</strong></td>
                                            <td>{{ $task->property->name }}</td>
                                            <td>{{ str_replace('_', ' ', $task->task_type) }}</td>
                                            <td>
                                                <span class="badge badge-{{ strtolower($task->priority) }}">
                                                    {{ $task->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ strtolower(str_replace('_', '-', $task->status)) }}">
                                                    {{ str_replace('_', ' ', $task->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $task->scheduled_date->format('M j, Y') }}
                                                @if($task->scheduled_time)
                                                    <br>{{ $task->scheduled_time }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('tenant.housekeeper.tasks.show', $task) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($task->status == 'PENDING')
                                                        <form method="POST" action="{{ route('tenant.housekeeper.tasks.start', $task) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-play"></i> Start
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($task->status == 'IN_PROGRESS')
                                                        <form method="POST" action="{{ route('tenant.housekeeper.tasks.complete', $task) }}" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Mark task as completed?')">
                                                                <i class="fas fa-check"></i> Complete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="pagination">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-tasks"></i>
                            <h3>No tasks found</h3>
                            <p>You don't have any assigned tasks matching the selected filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
