<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Floor {{ $floor->number }} - HotelPro</title>
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
            max-width: 800px;
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
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
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
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .current-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .current-info h4 {
            color: #0056b3;
            margin-bottom: 10px;
        }
        
        .current-info p {
            color: #0056b3;
            margin: 0;
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
            
            .main-content {
                margin-left: 0;
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
    <div class="dashboard-container">
        <!-- Include Manager-Specific Sidebar -->
        @include('Users.shared.sidebars.manager')

        <!-- Main Content -->
        <div class="main-content">
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <h1><i class="fas fa-edit"></i> Edit Floor {{ $floor->number }}</h1>
                    <p>Update floor details and configuration</p>
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

                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <div class="form-container">
                    <!-- Current Info -->
                    <div class="current-info">
                        <h4><i class="fas fa-info-circle"></i> Current Floor Information</h4>
                        <p><strong>Property:</strong> {{ $floor->building->property->name }}</p>
                        <p><strong>Building:</strong> {{ $floor->building->name }}</p>
                        <p><strong>Total Rooms:</strong> {{ $floor->rooms->count() }}</p>
                    </div>

                    <form method="POST" action="{{ route('tenant.floors.update', $floor->id) }}" id="floorForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Location Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Location Information
                            </h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="property_id">Property <span class="required">*</span></label>
                                    <select name="property_id" id="property_id" class="form-control" required>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ $floor->building->property_id == $property->id ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Choose the property where this floor is located</div>
                                    @error('property_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="building_id">Building <span class="required">*</span></label>
                                    <select name="building_id" id="building_id" class="form-control" required>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}" {{ old('building_id', $floor->building_id) == $building->id ? 'selected' : '' }}>
                                                {{ $building->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Select the building for this floor</div>
                                    @error('building_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Floor Details Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-layer-group"></i>
                                Floor Details
                            </h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="number">Floor Number <span class="required">*</span></label>
                                    <input type="number" name="number" id="number" class="form-control" 
                                           value="{{ old('number', $floor->number) }}" required min="1" max="999">
                                    <div class="form-text">Floor number (e.g., 1, 2, 3...)</div>
                                    @error('number')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-grid single">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" maxlength="255">{{ old('description', $floor->description) }}</textarea>
                                    <div class="form-text">Optional description for this floor (e.g., "Executive Floor", "Standard Rooms")</div>
                                    @error('description')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="{{ route('tenant.floors.show', $floor->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                            @if($floor->rooms->count() == 0)
                                <button type="button" onclick="deleteFloor('{{ $floor->id }}')" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Delete Floor
                                </button>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Floor
                            </button>
                        </div>
                    </form>
                    
                    <!-- Loading State -->
                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p>Updating floor...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle property change to load buildings
            $('#property_id').on('change', function() {
                loadBuildings($(this).val());
            });
            
            // Form submission
            $('#floorForm').on('submit', function(e) {
                $('#loading').show();
                $(this).hide();
            });
        });
        
        function loadBuildings(propertyId) {
            if (!propertyId) {
                $('#building_id').prop('disabled', true).html('<option value="">Select Building</option>');
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
                        let selected = building.id === '{{ $floor->building_id }}' ? 'selected' : '';
                        options += `<option value="${building.id}" ${selected}>${building.name}</option>`;
                    });
                    $('#building_id').prop('disabled', false).html(options);
                },
                error: function() {
                    alert('Failed to load buildings');
                }
            });
        }

        function deleteFloor(floorId) {
            if (confirm('Are you sure you want to delete this floor? This action cannot be undone.')) {
                $.ajax({
                    url: `/floors/${floorId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("tenant.floors.index") }}';
                        } else {
                            alert(response.message || 'Failed to delete floor');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to delete floor';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    }
                });
            }
        }
    </script>
</body>
</html>