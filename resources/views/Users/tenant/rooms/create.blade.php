<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room - HotelPro</title>
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
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .feature-item:hover {
            border-color: #4caf50;
        }
        
        .feature-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .feature-item label {
            margin: 0;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
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
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4caf50;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Create New Room</h1>
            <p>Add a new room to your property inventory</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul style="margin: 5px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form -->
        <div class="form-container">
            <form method="POST" action="{{ route('tenant.rooms.store') }}" id="roomForm">
                @csrf
                
                <!-- Property & Location Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Property & Location
                    </h3>
                    
                    <div class="form-grid">
                        @if(Auth::user()->role->name === 'DIRECTOR')
                            <div class="form-group">
                                <label for="property_id">Property <span class="required">*</span></label>
                                <select name="property_id" id="property_id" class="form-control" required>
                                    <option value="">Select Property</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                            {{ $property->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="property_id" value="{{ Auth::user()->property_id }}">
                            <div class="form-group">
                                <label>Property</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->property->name ?? 'No Property Assigned' }}" disabled>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="building_id">Building <span class="required">*</span></label>
                            <select name="building_id" id="building_id" class="form-control" required disabled>
                                <option value="">Select Building</option>
                            </select>
                            @error('building_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="floor_id">Floor <span class="required">*</span></label>
                            <select name="floor_id" id="floor_id" class="form-control" required disabled>
                                <option value="">Select Floor</option>
                            </select>
                            @error('floor_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Details Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-door-open"></i>
                        Room Details
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="room_number">Room Number <span class="required">*</span></label>
                            <input type="text" name="room_number" id="room_number" class="form-control" 
                                   value="{{ old('room_number') }}" required maxlength="20">
                            <div class="form-text">Unique identifier for this room</div>
                            @error('room_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="room_type_id">Room Type <span class="required">*</span></label>
                            <select name="room_type_id" id="room_type_id" class="form-control" required disabled>
                                <option value="">Select Room Type</option>
                            </select>
                            @error('room_type_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status <span class="required">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach(\App\Models\Room::STATUSES as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', strtolower($status))) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="current_rate">Current Rate <span class="required">*</span></label>
                            <input type="number" name="current_rate" id="current_rate" class="form-control" 
                                   value="{{ old('current_rate') }}" required min="0" max="999999.99" step="0.01">
                            <div class="form-text">Daily rate for this room</div>
                            @error('current_rate')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-grid single">
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
                            <div class="form-text">Additional information about this room</div>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Features Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Room Features
                    </h3>
                    
                    <div id="features-container">
                        @if($roomFeatures->count() > 0)
                            <div class="features-grid">
                                @foreach($roomFeatures as $feature)
                                    <div class="feature-item">
                                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                               id="feature_{{ $feature->id }}"
                                               {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}>
                                        <label for="feature_{{ $feature->id }}">{{ $feature->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No room features available. Contact your administrator to add features.</p>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('tenant.rooms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Create Room
                    </button>
                </div>
            </form>
            
            <!-- Loading State -->
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Creating room...</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get the initial property ID
            let initialPropertyId = @if(Auth::user()->role->name === 'MANAGER') '{{ Auth::user()->property_id }}' @else $('#property_id').val() @endif;
            
            // Load buildings when property changes or on page load
            @if(Auth::user()->role->name === 'DIRECTOR')
                $('#property_id').on('change', function() {
                    loadBuildings($(this).val());
                });
            @else
                // For managers, load buildings immediately
                if (initialPropertyId) {
                    loadBuildings(initialPropertyId);
                }
            @endif
            
            // Load floors when building changes
            $('#building_id').on('change', function() {
                loadFloors($(this).val());
            });
            
            // Load room types when property changes
            @if(Auth::user()->role->name === 'DIRECTOR')
                $('#property_id').on('change', function() {
                    loadRoomTypes($(this).val());
                });
            @else
                // For managers, load room types immediately
                if (initialPropertyId) {
                    loadRoomTypes(initialPropertyId);
                }
            @endif
            
            // Form submission
            $('#roomForm').on('submit', function(e) {
                $('#loading').show();
                $(this).hide();
            });
        });
        
        function loadBuildings(propertyId) {
            if (!propertyId) {
                $('#building_id').prop('disabled', true).html('<option value="">Select Building</option>');
                $('#floor_id').prop('disabled', true).html('<option value="">Select Floor</option>');
                return;
            }
            
            $.ajax({
                url: `/rooms/property/${propertyId}/buildings`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(buildings) {
                    let options = '<option value="">Select Building</option>';
                    buildings.forEach(function(building) {
                        options += `<option value="${building.id}">${building.name}</option>`;
                    });
                    $('#building_id').prop('disabled', false).html(options);
                    $('#floor_id').prop('disabled', true).html('<option value="">Select Floor</option>');
                },
                error: function() {
                    alert('Failed to load buildings');
                }
            });
        }
        
        function loadFloors(buildingId) {
            if (!buildingId) {
                $('#floor_id').prop('disabled', true).html('<option value="">Select Floor</option>');
                return;
            }
            
            $.ajax({
                url: `/rooms/building/${buildingId}/floors`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(floors) {
                    let options = '<option value="">Select Floor</option>';
                    floors.forEach(function(floor) {
                        let description = floor.description ? ` - ${floor.description}` : '';
                        options += `<option value="${floor.id}">Floor ${floor.number}${description}</option>`;
                    });
                    $('#floor_id').prop('disabled', false).html(options);
                },
                error: function() {
                    alert('Failed to load floors');
                }
            });
        }
        
        function loadRoomTypes(propertyId) {
            if (!propertyId) {
                $('#room_type_id').prop('disabled', true).html('<option value="">Select Room Type</option>');
                return;
            }
            
            $.ajax({
                url: `/rooms/property/${propertyId}/room-types`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(roomTypes) {
                    let options = '<option value="">Select Room Type</option>';
                    roomTypes.forEach(function(roomType) {
                        options += `<option value="${roomType.id}">${roomType.name} ($${roomType.base_rate})</option>`;
                    });
                    $('#room_type_id').prop('disabled', false).html(options);
                },
                error: function() {
                    alert('Failed to load room types');
                }
            });
        }
    </script>
</body>
</html>