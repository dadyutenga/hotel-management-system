<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room Type - HotelPro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy the same styles from index view */
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
        
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }
        
        .input-group-prepend {
            display: flex;
        }
        
        .input-group-append {
            display: flex;
        }
        
        .input-group-text {
            display: flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            text-align: center;
            white-space: nowrap;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        
        .input-group-prepend .input-group-text {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .input-group-append .input-group-text {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .input-group-prepend + .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .form-control + .input-group-append .input-group-text {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .custom-control {
            position: relative;
            display: block;
            min-height: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .custom-switch {
            padding-left: 2.25rem;
        }
        
        .custom-control-input {
            position: absolute;
            z-index: -1;
            opacity: 0;
        }
        
        .custom-control-label {
            position: relative;
            margin-bottom: 0;
            cursor: pointer;
        }
        
        .custom-control-label::before {
            position: absolute;
            top: 0.25rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            pointer-events: none;
            content: "";
            background-color: #fff;
            border: 1px solid #adb5bd;
        }
        
        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 1.75rem;
            pointer-events: all;
            border-radius: 0.5rem;
        }
        
        .custom-switch .custom-control-label::after {
            top: calc(0.25rem + 2px);
            left: calc(-2.25rem + 2px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
            background-color: #adb5bd;
            border-radius: 0.5rem;
            transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .custom-control-label::after {
            position: absolute;
            top: 0.25rem;
            left: -1.5rem;
            display: block;
            width: 1rem;
            height: 1rem;
            content: "";
            background: no-repeat 50% / 50% 50%;
        }
        
        .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
            background-color: #fff;
            transform: translateX(0.75rem);
        }
        
        .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
            color: #fff;
            border-color: #007bff;
            background-color: #007bff;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .container {
                padding: 15px;
            }
            
            .header {
                padding: 20px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .row {
                margin: 0;
            }
            
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 0;
                margin-bottom: 20px;
            }
            
            .card-footer .row {
                flex-direction: column;
                gap: 15px;
            }
            
            .text-right {
                text-align: left !important;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('Users.shared.sidebars.manager')
        
        <div class="main-content">
            <div class="container">

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Room Type</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tenant.room-types.index') }}">Room Types</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bed"></i> Create New Room Type
                            </h3>
                        </div>
                        
                        <form action="{{ route('tenant.room-types.store') }}" method="POST" id="roomTypeForm">
                            @csrf
                            
                            <div class="card-body">
                                <!-- Property Selection -->
                                <div class="form-group">
                                    <label for="property_id" class="required">Property</label>
                                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" 
                                                    {{ (old('property_id') == $property->id || ($selectedProperty && $selectedProperty->id == $property->id)) ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <!-- Room Type Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="required">Room Type Name</label>
                                            <input type="text" name="name" id="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="e.g., Standard Single, Deluxe Double, Suite" 
                                                   maxlength="100" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Base Rate -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="base_rate" class="required">Base Rate (Tzs)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Tzs</span>
                                                </div>
                                                <input type="number" name="base_rate" id="base_rate" 
                                                       class="form-control @error('base_rate') is-invalid @enderror" 
                                                       value="{{ old('base_rate') }}" 
                                                       step="0.01" min="0" max="999999.99" 
                                                       placeholder="0.00" required>
                                                @error('base_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="3" maxlength="500" 
                                              placeholder="Describe the room type features, amenities, etc.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="descriptionCount">0</span>/500 characters
                                    </small>
                                </div>

                                <div class="row">
                                    <!-- Max Occupancy -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_occupancy" class="required">Maximum Occupancy</label>
                                            <input type="number" name="max_occupancy" id="max_occupancy" 
                                                   class="form-control @error('max_occupancy') is-invalid @enderror" 
                                                   value="{{ old('max_occupancy', 2) }}" 
                                                   min="1" max="20" required>
                                            @error('max_occupancy')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Number of guests this room type can accommodate</small>
                                        </div>
                                    </div>

                                    <!-- Size -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="size_sqm">Size (sq.m)</label>
                                            <div class="input-group">
                                                <input type="number" name="size_sqm" id="size_sqm" 
                                                       class="form-control @error('size_sqm') is-invalid @enderror" 
                                                       value="{{ old('size_sqm') }}" 
                                                       step="0.01" min="0" max="9999.99" 
                                                       placeholder="0.00">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">sq.m</span>
                                                </div>
                                                @error('size_sqm')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Active Room Type
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Active room types are available for room assignments and bookings
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Create Room Type
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{ route('tenant.room-types.index') }}" class="btn btn-outline-secondary">
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
    </section>
</div>
            </div>
        </div>
    </div>

    <script>
        // Character count for description
        const descriptionField = document.getElementById('description');
        const descriptionCount = document.getElementById('descriptionCount');
        
        if (descriptionField && descriptionCount) {
            descriptionField.addEventListener('input', function() {
                const count = this.value.length;
                descriptionCount.textContent = count;
                
                if (count > 450) {
                    descriptionCount.style.color = '#ffc107';
                } else {
                    descriptionCount.style.color = '#6c757d';
                }
            });

            // Trigger initial count
            descriptionField.dispatchEvent(new Event('input'));
        }

        // Form validation
        const roomTypeForm = document.getElementById('roomTypeForm');
        if (roomTypeForm) {
            roomTypeForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validate required fields
                const requiredFields = ['property_id', 'name', 'base_rate', 'max_occupancy'];
                requiredFields.forEach(function(field) {
                    const input = document.getElementById(field);
                    if (!input.value) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                // Validate base rate
                const baseRate = parseFloat(document.getElementById('base_rate').value);
                if (baseRate < 0) {
                    document.getElementById('base_rate').classList.add('is-invalid');
                    alert('Base rate must be a positive number');
                    isValid = false;
                }

                // Validate max occupancy
                const maxOccupancy = parseInt(document.getElementById('max_occupancy').value);
                if (maxOccupancy < 1 || maxOccupancy > 20) {
                    document.getElementById('max_occupancy').classList.add('is-invalid');
                    alert('Maximum occupancy must be between 1 and 20');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fix the errors and try again');
                }
            });
        }

        // Remove validation classes on input
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
            input.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        });

        function resetForm() {
            if (roomTypeForm) {
                roomTypeForm.reset();
                document.querySelectorAll('.form-control').forEach(function(input) {
                    input.classList.remove('is-invalid');
                });
                if (descriptionCount) {
                    descriptionCount.textContent = '0';
                }
                const isActiveCheckbox = document.getElementById('is_active');
                if (isActiveCheckbox) {
                    isActiveCheckbox.checked = true;
                }
            }
        }
    </script>
</body>
</html>