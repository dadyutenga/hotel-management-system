<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Housekeeping Task - HotelPro</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .required::after {
            content: " *";
            color: red;
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
        
        .form-text {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .text-right {
            text-align: right !important;
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
        
        .ml-2 {
            margin-left: 0.5rem !important;
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
            
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 0;
                margin-bottom: 20px;
            }
            
            .text-right {
                text-align: left !important;
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
                    <h1 class="page-title">Create Housekeeping Task</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('user.dashboard') }}">Home</a>
                        <span>/</span>
                        <a href="{{ route('supervisor.housekeeping.index') }}">Housekeeping Tasks</a>
                        <span>/</span>
                        <span>Create</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-broom"></i> Create New Housekeeping Task
                        </h3>
                    </div>
                    
                    <form action="{{ route('supervisor.housekeeping.store') }}" method="POST" id="taskForm">
                        @csrf
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="property_id" class="required">Property</label>
                                        <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                                            <option value="">Select Property</option>
                                            @foreach($properties as $property)
                                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                                    {{ $property->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('property_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="room_id" class="required">Room</label>
                                        <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                                            <option value="">Select Room</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                    {{ $room->room_number }} - {{ $room->roomType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="assigned_to" class="required">Assign To</label>
                                        <select name="assigned_to" id="assigned_to" class="form-control @error('assigned_to') is-invalid @enderror" required>
                                            <option value="">Select Housekeeper</option>
                                            @foreach($housekeepers as $housekeeper)
                                                <option value="{{ $housekeeper->id }}" {{ old('assigned_to') == $housekeeper->id ? 'selected' : '' }}>
                                                    {{ $housekeeper->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="task_type" class="required">Task Type</label>
                                        <select name="task_type" id="task_type" class="form-control @error('task_type') is-invalid @enderror" required>
                                            <option value="">Select Task Type</option>
                                            <option value="DAILY_CLEAN" {{ old('task_type') == 'DAILY_CLEAN' ? 'selected' : '' }}>Daily Clean</option>
                                            <option value="DEEP_CLEAN" {{ old('task_type') == 'DEEP_CLEAN' ? 'selected' : '' }}>Deep Clean</option>
                                            <option value="TURNDOWN" {{ old('task_type') == 'TURNDOWN' ? 'selected' : '' }}>Turndown Service</option>
                                            <option value="INSPECTION" {{ old('task_type') == 'INSPECTION' ? 'selected' : '' }}>Inspection</option>
                                            <option value="OTHER" {{ old('task_type') == 'OTHER' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('task_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority" class="required">Priority</label>
                                        <select name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                            <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                                            <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                            <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="scheduled_date" class="required">Scheduled Date</label>
                                        <input type="date" name="scheduled_date" id="scheduled_date" 
                                               class="form-control @error('scheduled_date') is-invalid @enderror" 
                                               value="{{ old('scheduled_date', date('Y-m-d')) }}" 
                                               required>
                                        @error('scheduled_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="scheduled_time">Scheduled Time</label>
                                <input type="time" name="scheduled_time" id="scheduled_time" 
                                       class="form-control @error('scheduled_time') is-invalid @enderror" 
                                       value="{{ old('scheduled_time') }}">
                                @error('scheduled_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text">Optional: Specify a time for the task</small>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" 
                                          class="form-control @error('notes') is-invalid @enderror" 
                                          rows="3" 
                                          placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Create Task
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('supervisor.housekeeping.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const taskForm = document.getElementById('taskForm');

        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
            input.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        });

        function resetForm() {
            if (taskForm) {
                taskForm.reset();
                document.querySelectorAll('.form-control').forEach(function(input) {
                    input.classList.remove('is-invalid');
                });
            }
        }
    </script>
</body>
</html>
