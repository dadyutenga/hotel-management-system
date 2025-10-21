<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housekeeping Tasks - HotelPro</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
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
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #bd2130;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
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
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-pending {
            background: #ffc107;
            color: #212529;
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
            background: #007bff;
            color: white;
        }
        
        .badge-cancelled {
            background: #6c757d;
            color: white;
        }
        
        .badge-high {
            background: #dc3545;
            color: white;
        }
        
        .badge-medium {
            background: #ffc107;
            color: #212529;
        }
        
        .badge-low {
            background: #28a745;
            color: white;
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                overflow-x: scroll;
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
                    <div>
                        <h1 class="page-title">Housekeeping Tasks</h1>
                        <div class="breadcrumb">
                            <a href="{{ route('user.dashboard') }}">Home</a>
                            <span>/</span>
                            <span>Housekeeping Tasks</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('supervisor.housekeeping.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Task
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ session('info') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i> Filter Tasks
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('supervisor.housekeeping.index') }}" class="filter-form">
                            <div>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                    <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                    <option value="VERIFIED" {{ request('status') == 'VERIFIED' ? 'selected' : '' }}>Verified</option>
                                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <select name="priority" class="form-control">
                                    <option value="">All Priorities</option>
                                    <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                                    <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                    <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                            
                            <div>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            
                            <div>
                                <select name="assigned_to" class="form-control">
                                    <option value="">All Housekeepers</option>
                                    @foreach($housekeepers as $housekeeper)
                                        <option value="{{ $housekeeper->id }}" {{ request('assigned_to') == $housekeeper->id ? 'selected' : '' }}>
                                            {{ $housekeeper->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('supervisor.housekeeping.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Tasks List
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($tasks->count() > 0)
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Task Type</th>
                                            <th>Assigned To</th>
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
                                                <td>{{ str_replace('_', ' ', $task->task_type) }}</td>
                                                <td>{{ $task->assignedTo->full_name }}</td>
                                                <td>
                                                    <span class="badge badge-{{ strtolower(str_replace('_', '-', $task->status)) }}">
                                                        {{ str_replace('_', ' ', $task->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ strtolower($task->priority) }}">
                                                        {{ $task->priority }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $task->scheduled_date->format('M d, Y') }}<br>
                                                    @if($task->scheduled_time)
                                                        <small>{{ $task->scheduled_time }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="display: flex; gap: 5px;">
                                                        <a href="{{ route('supervisor.housekeeping.show', $task) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        @if($task->status == 'COMPLETED')
                                                            <form action="{{ route('supervisor.housekeeping.verify', $task) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verify this task?')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        @if(in_array($task->status, ['PENDING', 'IN_PROGRESS']))
                                                            <form action="{{ route('supervisor.housekeeping.destroy', $task) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this task?')">
                                                                    <i class="fas fa-trash"></i>
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
                            
                            <div class="pagination">
                                {{ $tasks->links() }}
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <h3>No tasks found</h3>
                                <p>No housekeeping tasks match your current filters.</p>
                                <a href="{{ route('supervisor.housekeeping.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Task
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
