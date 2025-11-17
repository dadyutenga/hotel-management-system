<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Group Booking - {{ config('app.name') }}</title>
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
            background: #f8f9fa;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
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

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        @include('Users.shared.sidebars.manager')

        <div class="main-content">
            <div class="container">
                <div class="header">
                    <h1><i class="fas fa-layer-group"></i> Create New Group Booking</h1>
                    <p>Complete the form below to create a new group booking</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin: 5px 0 0 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('tenant.group-bookings.store') }}" method="POST" class="form-container">
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">
                                    Group Name <span class="required">*</span>
                                </label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="{{ old('name') }}" required 
                                       placeholder="e.g., ABC Company Annual Conference">
                                @error('name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="property_id">
                                    Property <span class="required">*</span>
                                </label>
                                <select id="property_id" name="property_id" class="form-control" required>
                                    <option value="">Select Property</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                            {{ $property->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="total_rooms">
                                    Total Rooms <span class="required">*</span>
                                </label>
                                <input type="number" id="total_rooms" name="total_rooms" class="form-control" 
                                       value="{{ old('total_rooms', 1) }}" required min="1">
                                @error('total_rooms')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status">
                                    Status <span class="required">*</span>
                                </label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="PENDING" {{ old('status', 'PENDING') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="CONFIRMED" {{ old('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="CANCELLED" {{ old('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="COMPLETED" {{ old('status') === 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dates Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Stay Dates
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="arrival_date">
                                    Arrival Date <span class="required">*</span>
                                </label>
                                <input type="date" id="arrival_date" name="arrival_date" class="form-control" 
                                       value="{{ old('arrival_date') }}" required 
                                       min="{{ date('Y-m-d') }}">
                                @error('arrival_date')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="departure_date">
                                    Departure Date <span class="required">*</span>
                                </label>
                                <input type="date" id="departure_date" name="departure_date" class="form-control" 
                                       value="{{ old('departure_date') }}" required 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('departure_date')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                                <span class="form-text">Must be after arrival date</span>
                            </div>
                        </div>
                    </div>

                    <!-- Leader/Corporate Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-user-tie"></i>
                            Leader & Corporate Information
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="leader_guest_id">Group Leader Guest</label>
                                <select id="leader_guest_id" name="leader_guest_id" class="form-control">
                                    <option value="">Select Leader Guest (Optional)</option>
                                </select>
                                <span class="form-text">Start typing to search for existing guests</span>
                                @error('leader_guest_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="corporate_account_id">Corporate Account</label>
                                <select id="corporate_account_id" name="corporate_account_id" class="form-control">
                                    <option value="">Select Corporate Account (Optional)</option>
                                    @foreach($corporateAccounts as $corporate)
                                        <option value="{{ $corporate->id }}" {{ old('corporate_account_id') == $corporate->id ? 'selected' : '' }}>
                                            {{ $corporate->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('corporate_account_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Additional Notes
                        </div>
                        <div class="form-grid single">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control" 
                                          placeholder="Any special requests, requirements, or important information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('tenant.group-bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Group Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate minimum departure date based on arrival
        document.getElementById('arrival_date').addEventListener('change', function() {
            const arrivalDate = new Date(this.value);
            const departureInput = document.getElementById('departure_date');
            
            arrivalDate.setDate(arrivalDate.getDate() + 1);
            const minDeparture = arrivalDate.toISOString().split('T')[0];
            departureInput.min = minDeparture;
            
            if (departureInput.value && departureInput.value <= this.value) {
                departureInput.value = '';
            }
        });
    </script>
</body>
</html>
