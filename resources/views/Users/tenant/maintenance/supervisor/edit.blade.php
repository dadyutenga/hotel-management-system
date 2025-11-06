<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Request - HotelPro</title>
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
        
        .form-control:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
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
        
        /* Multi-select styling */
        select[multiple] {
            min-height: 120px;
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
                    <h1>Edit Maintenance Request</h1>
                    <p>Update maintenance request details and assignment</p>
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
                    <form method="POST" action="{{ route('tenant.maintenance.update', $maintenanceRequest->id) }}" id="maintenanceForm">
                        @csrf
                        @method('PUT')

                        <!-- Request Info -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i> Request Information
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Request ID</label>
                                    <input type="text" class="form-control" value="{{ substr($maintenanceRequest->id, 0, 8) }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Current Status</label>
                                    <div>
                                        <span class="status-badge badge-{{ strtolower($maintenanceRequest->status) }}">
                                            {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                {{ (old('property_id', $maintenanceRequest->property_id) == $property->id) ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="room_id">Room (Optional)</label>
                                    <select name="room_id" id="room_id" class="form-control">
                                        <option value="">Loading...</option>
                                    </select>
                                    <div class="loading" id="roomLoading">
                                        <div class="spinner"></div>
                                        <span>Loading rooms...</span>
                                    </div>
                                    @error('room_id')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                    <span class="form-text">Leave blank for general maintenance</span>
                                </div>
                            </div>

                            <div class="form-grid single">
                                <div class="form-group">
                                    <label for="location_details">Location Details</label>
                                    <input type="text" name="location_details" id="location_details" class="form-control" 
                                           value="{{ old('location_details', $maintenanceRequest->location_details) }}" 
                                           placeholder="e.g., Main Lobby, Parking Area, Roof">
                                    @error('location_details')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                    <span class="form-text">Specify exact location if not room-specific</span>
                                </div>
                            </div>
                        </div>

                        <!-- Issue Details -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-tools"></i> Issue Details
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="issue_type">Issue Type <span class="required">*</span></label>
                                    <input type="text" name="issue_type" id="issue_type" class="form-control" 
                                           value="{{ old('issue_type', $maintenanceRequest->issue_type) }}" 
                                           placeholder="e.g., Plumbing, Electrical, HVAC" required>
                                    @error('issue_type')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority <span class="required">*</span></label>
                                    <select name="priority" id="priority" class="form-control" required>
                                        <option value="">Select Priority</option>
                                        <option value="LOW" {{ old('priority', $maintenanceRequest->priority) == 'LOW' ? 'selected' : '' }}>Low</option>
                                        <option value="MEDIUM" {{ old('priority', $maintenanceRequest->priority) == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                        <option value="HIGH" {{ old('priority', $maintenanceRequest->priority) == 'HIGH' ? 'selected' : '' }}>High</option>
                                        <option value="URGENT" {{ old('priority', $maintenanceRequest->priority) == 'URGENT' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                    <span class="form-text">Urgent requests will mark the room as MAINTENANCE</span>
                                </div>
                            </div>

                            <div class="form-grid single">
                                <div class="form-group">
                                    <label for="description">Description <span class="required">*</span></label>
                                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $maintenanceRequest->description) }}</textarea>
                                    @error('description')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                    <span class="form-text">Provide detailed description of the issue</span>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-users"></i> Assign Housekeepers
                            </div>

                            <div class="form-grid single">
                                <div class="form-group">
                                    <label for="housekeeper_ids">Select Housekeepers (Optional)</label>
                                    <select name="housekeeper_ids[]" id="housekeeper_ids" class="form-control" multiple>
                                        @foreach($housekeepers as $housekeeper)
                                            <option value="{{ $housekeeper->id }}" 
                                                {{ (is_array(old('housekeeper_ids')) ? in_array($housekeeper->id, old('housekeeper_ids')) : $maintenanceRequest->assignedStaff->contains($housekeeper->id)) ? 'selected' : '' }}>
                                                {{ $housekeeper->name }} - {{ $housekeeper->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('housekeeper_ids')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                    <span class="form-text">Hold Ctrl (Cmd on Mac) to select multiple housekeepers. Leave empty to assign later.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('tenant.maintenance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Update Request
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
            const currentPropertyId = '{{ old('property_id', $maintenanceRequest->property_id) }}';
            const currentRoomId = '{{ old('room_id', $maintenanceRequest->room_id) }}';

            // Function to load rooms
            function loadRooms(propertyId, selectedRoomId = null) {
                const roomSelect = $('#room_id');
                const loading = $('#roomLoading');

                if (propertyId) {
                    loading.css('display', 'flex');
                    roomSelect.prop('disabled', true).html('<option value="">Loading...</option>');

                    $.ajax({
                        url: '/maintenance/property/' + propertyId + '/rooms',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            roomSelect.html('<option value="">Select Room (Optional)</option>');
                            if (data && data.length > 0) {
                                data.forEach(room => {
                                    const selected = selectedRoomId && room.id === selectedRoomId ? 'selected' : '';
                                    roomSelect.append('<option value="' + room.id + '" ' + selected + '>Room ' + room.room_number + ' (' + room.status + ')</option>');
                                });
                            } else {
                                roomSelect.append('<option value="">No rooms available</option>');
                            }
                            roomSelect.prop('disabled', false);
                            loading.hide();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading rooms:', error);
                            roomSelect.html('<option value="">Error loading rooms</option>');
                            loading.hide();
                            alert('Failed to load rooms. Please try again.');
                        }
                    });
                } else {
                    roomSelect.prop('disabled', true).html('<option value="">Select Property First</option>');
                    loading.hide();
                }
            }

            // Load rooms on property change
            $('#property_id').change(function() {
                const propertyId = $(this).val();
                loadRooms(propertyId);
            });

            // Load initial rooms for current property
            if (currentPropertyId) {
                loadRooms(currentPropertyId, currentRoomId);
            }

            // Form validation
            $('#maintenanceForm').submit(function(e) {
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('<div class="spinner"></div> Updating...');
            });
        });
    </script>
</body>
</html>
