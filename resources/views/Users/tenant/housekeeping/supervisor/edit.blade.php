<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Housekeeping Task - HotelPro</title>
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
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-grid.single {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group label .required {
            color: #dc3545;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4caf50;
        }
        
        .form-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
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
        
        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4caf50;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
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
        <!-- Include Supervisor Sidebar -->
        @include('Users.shared.sidebars.supervisor')

        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <h1>Edit Housekeeping Task</h1>
                    <p>Update task details and assignment</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Please correct the following errors:</strong>
                            <ul style="margin-top: 5px; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <div class="form-container">
                    <form method="POST" action="{{ route('tenant.housekeeping.update', $housekeeping) }}" id="taskForm">
                        @csrf
                        @method('PUT')

                        <!-- Location Information -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Location Information
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="property_id">Property <span class="required">*</span></label>
                                    <select name="property_id" id="property_id" class="form-control" required>
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" 
                                                {{ old('property_id', $housekeeping->property_id) == $property->id ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="room_id">Room <span class="required">*</span></label>
                                    <select name="room_id" id="room_id" class="form-control" required>
                                        <option value="">Loading rooms...</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" 
                                                {{ old('room_id', $housekeeping->room_id) == $room->id ? 'selected' : '' }}>
                                                Room {{ $room->room_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="loading" id="roomLoading">
                                        <div class="spinner"></div>
                                        <span>Loading rooms...</span>
                                    </div>
                                    @error('room_id')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Task Details -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-tasks"></i> Task Details
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="task_type">Task Type <span class="required">*</span></label>
                                    <select name="task_type" id="task_type" class="form-control" required>
                                        <option value="">Select Task Type</option>
                                        <option value="DAILY_CLEAN" {{ old('task_type', $housekeeping->task_type) == 'DAILY_CLEAN' ? 'selected' : '' }}>Daily Clean</option>
                                        <option value="DEEP_CLEAN" {{ old('task_type', $housekeeping->task_type) == 'DEEP_CLEAN' ? 'selected' : '' }}>Deep Clean</option>
                                        <option value="TURNDOWN" {{ old('task_type', $housekeeping->task_type) == 'TURNDOWN' ? 'selected' : '' }}>Turndown Service</option>
                                        <option value="INSPECTION" {{ old('task_type', $housekeeping->task_type) == 'INSPECTION' ? 'selected' : '' }}>Inspection</option>
                                        <option value="OTHER" {{ old('task_type', $housekeeping->task_type) == 'OTHER' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('task_type')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority <span class="required">*</span></label>
                                    <select name="priority" id="priority" class="form-control" required>
                                        <option value="">Select Priority</option>
                                        <option value="LOW" {{ old('priority', $housekeeping->priority) == 'LOW' ? 'selected' : '' }}>Low</option>
                                        <option value="MEDIUM" {{ old('priority', $housekeeping->priority) == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                        <option value="HIGH" {{ old('priority', $housekeeping->priority) == 'HIGH' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Assignment & Schedule -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-user-clock"></i> Assignment & Schedule
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="assigned_to">Assign To <span class="required">*</span></label>
                                    <select name="assigned_to" id="assigned_to" class="form-control" required>
                                        <option value="">Select Housekeeper</option>
                                        @foreach($housekeepers as $housekeeper)
                                            <option value="{{ $housekeeper->id }}" 
                                                {{ old('assigned_to', $housekeeping->assigned_to) == $housekeeper->id ? 'selected' : '' }}>
                                                {{ $housekeeper->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="scheduled_date">Scheduled Date <span class="required">*</span></label>
                                    <input type="date" name="scheduled_date" id="scheduled_date" class="form-control" 
                                           value="{{ old('scheduled_date', $housekeeping->scheduled_date->format('Y-m-d')) }}" required>
                                    @error('scheduled_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="scheduled_time">Scheduled Time (Optional)</label>
                                    <input type="time" name="scheduled_time" id="scheduled_time" class="form-control" 
                                           value="{{ old('scheduled_time', $housekeeping->scheduled_time) }}">
                                    <span class="form-text">Leave empty for any time</span>
                                    @error('scheduled_time')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-sticky-note"></i> Additional Notes
                            </div>

                            <div class="form-grid single">
                                <div class="form-group">
                                    <label for="notes">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="4" class="form-control" 
                                              placeholder="Any special instructions or notes for the housekeeper...">{{ old('notes', $housekeeping->notes) }}</textarea>
                                    @error('notes')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('tenant.housekeeping.show', $housekeeping) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Update Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load rooms when property is selected
            $('#property_id').change(function() {
                const propertyId = $(this).val();
                const roomSelect = $('#room_id');
                const loading = $('#roomLoading');
                const currentRoomId = '{{ old('room_id', $housekeeping->room_id) }}';

                if (propertyId) {
                    loading.css('display', 'flex');
                    roomSelect.prop('disabled', true).html('<option value="">Loading...</option>');

                    $.ajax({
                        url: `/housekeeping/property/${propertyId}/rooms`,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            roomSelect.html('<option value="">Select Room</option>');
                            data.forEach(room => {
                                const selected = room.id === currentRoomId ? 'selected' : '';
                                roomSelect.append(`<option value="${room.id}" ${selected}>Room ${room.room_number} (${room.status})</option>`);
                            });
                            roomSelect.prop('disabled', false);
                            loading.hide();
                        },
                        error: function() {
                            roomSelect.html('<option value="">Error loading rooms</option>');
                            loading.hide();
                            alert('Failed to load rooms. Please try again.');
                        }
                    });
                } else {
                    roomSelect.prop('disabled', true).html('<option value="">Select Property First</option>');
                    loading.hide();
                }
            });

            // Form validation
            $('#taskForm').submit(function(e) {
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('<div class="spinner"></div> Updating...');
            });
        });
    </script>
</body>
</html>
