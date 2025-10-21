<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Task - HotelPro</title>
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
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .info-box h4 {
            margin: 0 0 15px 0;
            color: #004085;
            font-size: 1.1rem;
        }
        
        .info-row {
            display: flex;
            padding: 8px 0;
        }
        
        .info-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        .is-invalid {
            border-color: #dc3545;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            background: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
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
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
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
        @include('Users.shared.sidebars.housekeeper')
        
        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1 class="page-title">Manage Task</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('user.dashboard') }}">Home</a>
                        <span>/</span>
                        <a href="{{ route('housekeeper.tasks.index') }}">My Tasks</a>
                        <span>/</span>
                        <span>Manage #{{ $task->id }}</span>
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

                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Task Information</h4>
                    <div class="info-row">
                        <div class="info-label">Room:</div>
                        <div class="info-value">{{ $task->room->room_number }} - {{ $task->room->roomType->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Task Type:</div>
                        <div class="info-value">{{ ucwords(str_replace('_', ' ', $task->task_type)) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Priority:</div>
                        <div class="info-value">
                            @if($task->priority == 'HIGH')
                                <span class="badge badge-high">High</span>
                            @elseif($task->priority == 'MEDIUM')
                                <span class="badge badge-medium">Medium</span>
                            @else
                                <span class="badge badge-low">Low</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Current Status:</div>
                        <div class="info-value">
                            @if($task->status == 'PENDING')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($task->status == 'IN_PROGRESS')
                                <span class="badge badge-in-progress">In Progress</span>
                            @endif
                        </div>
                    </div>
                    @if($task->notes)
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value">{{ $task->notes }}</div>
                        </div>
                    @endif
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i> Update Task Status
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        @if($task->status == 'PENDING')
                            <form action="{{ route('housekeeper.tasks.start', $task) }}" method="POST">
                                @csrf
                                <p style="margin-bottom: 20px;">Click the button below to start working on this task. The task status will be changed to "In Progress".</p>
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    <i class="fas fa-play"></i> Start Task
                                </button>
                            </form>
                        @elseif($task->status == 'IN_PROGRESS')
                            <form action="{{ route('housekeeper.tasks.complete', $task) }}" method="POST" id="completeForm">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="completion_notes">Completion Notes (Optional)</label>
                                    <textarea name="completion_notes" id="completion_notes" 
                                              class="form-control @error('completion_notes') is-invalid @enderror" 
                                              rows="4" 
                                              placeholder="Add any notes about the completed task, issues found, or maintenance needed...">{{ old('completion_notes') }}</textarea>
                                    @error('completion_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small style="display: block; margin-top: 5px; color: #6c757d;">
                                        Describe the work completed and report any issues or maintenance needs.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-success" style="width: 100%;" onclick="return confirm('Are you sure you want to mark this task as completed?')">
                                    <i class="fas fa-check"></i> Complete Task
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('housekeeper.tasks.show', $task) }}" class="btn btn-secondary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('housekeeper.tasks.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>
