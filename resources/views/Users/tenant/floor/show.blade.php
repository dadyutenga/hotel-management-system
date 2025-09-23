<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floor {{ $floor->number }} - {{ $floor->building->name }} - HotelPro</title>
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
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        
        .header-info h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .header-info p {
            color: #666;
            font-size: 16px;
        }
        
        .floor-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 20px;
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
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-left: 5px solid #4caf50;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .description-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .rooms-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .room-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .room-card:hover {
            border-color: #4caf50;
            transform: translateY(-2px);
        }
        
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .room-number {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-available {
            background: #d4edda;
            color: #155724;
        }
        
        .status-occupied {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-maintenance {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cleaning {
            background: #cce7ff;
            color: #004085;
        }
        
        .room-info {
            font-size: 14px;
            color: #666;
        }
        
        .room-type {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .room-rate {
            color: #4caf50;
            font-weight: 600;
        }
        
        .no-rooms {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
        
        .no-rooms i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
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
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .floor-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
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
                    <div class="header-content">
                        <div class="header-info">
                            <h1><i class="fas fa-layer-group"></i> Floor {{ $floor->number }}</h1>
                            <p>{{ $floor->building->name }} - {{ $floor->building->property->name }}</p>
                        </div>
                        <div class="floor-actions">
                            <a href="{{ route('tenant.floors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Floors
                            </a>
                            <a href="{{ route('tenant.floors.edit', $floor->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                                Edit Floor
                            </a>
                            <a href="{{ route('tenant.rooms.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Add Room
                            </a>
                            @if($floor->rooms->count() == 0)
                                <button onclick="deleteFloor('{{ $floor->id }}')" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Delete Floor
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Floor Information Cards -->
                <div class="info-cards">
                    <!-- Basic Information -->
                    <div class="info-card">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Floor Information
                        </h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Floor Number</div>
                                <div class="info-value">{{ $floor->number }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Building</div>
                                <div class="info-value">{{ $floor->building->name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Property</div>
                                <div class="info-value">{{ $floor->building->property->name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Total Rooms</div>
                                <div class="info-value">{{ $floor->rooms->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Statistics -->
                    <div class="info-card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Room Statistics
                        </h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Available</div>
                                <div class="info-value" style="color: #28a745;">{{ $floor->rooms->where('status', 'AVAILABLE')->count() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Occupied</div>
                                <div class="info-value" style="color: #dc3545;">{{ $floor->rooms->where('status', 'OCCUPIED')->count() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Maintenance</div>
                                <div class="info-value" style="color: #ffc107;">{{ $floor->rooms->where('status', 'OUT_OF_ORDER')->count() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Cleaning</div>
                                <div class="info-value" style="color: #17a2b8;">{{ $floor->rooms->where('status', 'CLEANING')->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($floor->description)
                    <div class="description-card">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i>
                            Description
                        </h3>
                        <p>{{ $floor->description }}</p>
                    </div>
                @endif

                <!-- Rooms Section -->
                <div class="rooms-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-door-open"></i>
                            Rooms on This Floor ({{ $floor->rooms->count() }})
                        </h3>
                        <a href="{{ route('tenant.rooms.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Room
                        </a>
                    </div>

                    @if($floor->rooms->count() > 0)
                        <div class="rooms-grid">
                            @foreach($floor->rooms->sortBy('room_number') as $room)
                                <div class="room-card">
                                    <div class="room-header">
                                        <div class="room-number">Room {{ $room->room_number }}</div>
                                        <span class="status-badge status-{{ strtolower(str_replace('_', '-', $room->status)) }}">
                                            {{ ucwords(str_replace('_', ' ', strtolower($room->status))) }}
                                        </span>
                                    </div>
                                    <div class="room-info">
                                        <div class="room-type">{{ $room->roomType->name }}</div>
                                        <div class="room-rate">${{ number_format($room->current_rate, 2) }}/night</div>
                                        <div style="margin-top: 10px;">
                                            <a href="{{ route('tenant.rooms.show', $room->id) }}" style="color: #4caf50; text-decoration: none; font-weight: 600;">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-rooms">
                            <i class="fas fa-door-open"></i>
                            <h4>No Rooms Yet</h4>
                            <p>This floor doesn't have any rooms yet. Add the first room to get started.</p>
                            <a href="{{ route('tenant.rooms.create', ['building_id' => $floor->building_id, 'floor_id' => $floor->id]) }}" class="btn btn-primary" style="margin-top: 15px;">
                                <i class="fas fa-plus"></i>
                                Add First Room
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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