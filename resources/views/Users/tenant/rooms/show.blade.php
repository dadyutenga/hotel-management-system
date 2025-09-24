<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room {{ $room->room_number }} - HotelPro</title>
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
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .header-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .room-status {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
            display: inline-block;
        }
        
        .status-occupied {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-vacant {
            background: #d4edda;
            color: #155724;
        }
        
        .status-dirty {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-clean {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-out_of_order {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .status-cleaning_in_progress {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-inspected {
            background: #e7f3ff;
            color: #084298;
        }
        
        .header-meta {
            color: #666;
            font-size: 16px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
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
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .main-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .sidebar-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
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
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }
        
        .features-section {
            margin-bottom: 30px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .feature-tag {
            background: #e9ecef;
            color: #495057;
            padding: 10px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .notes-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .notes-section h4 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .notes-section p {
            color: #666;
            line-height: 1.6;
        }
        
        .quick-actions {
            margin-bottom: 30px;
        }
        
        .action-button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .status-updater {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        
        .status-updater h4 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .status-btn {
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: white;
            color: #333;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-btn:hover {
            border-color: #4caf50;
            background: #f8fff8;
        }
        
        .status-btn.active {
            border-color: #4caf50;
            background: #4caf50;
            color: white;
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
            
            .header {
                flex-direction: column;
                gap: 20px;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .status-grid {
                grid-template-columns: 1fr;
            }
            
            .header-actions {
                width: 100%;
                justify-content: center;
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
                <h1><i class="fas fa-door-open"></i> Room {{ $room->room_number }}</h1>
                <div class="room-status status-{{ strtolower($room->status) }}">
                    {{ ucwords(str_replace('_', ' ', strtolower($room->status))) }}
                </div>
                <div class="header-meta">
                    {{ $room->property->name }} • {{ $room->floor->building->name }} • Floor {{ $room->floor->number }}
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('tenant.rooms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Rooms
                </a>
                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                    <a href="{{ route('tenant.rooms.edit', $room->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Room
                    </a>
                @endif
            </div>
        </div>

        <!-- Flash Messages -->
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

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Main Content -->
            <div class="main-card">
                <!-- Room Information -->
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Room Information
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Room Number</div>
                        <div class="info-value">{{ $room->room_number }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Room Type</div>
                        <div class="info-value">{{ $room->roomType->name ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Current Rate</div>
                        <div class="info-value">${{ number_format($room->current_rate, 2) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Capacity</div>
                        <div class="info-value">{{ $room->roomType->capacity ?? 'N/A' }} guests</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Base Rate</div>
                        <div class="info-value">{{ number_format($room->roomType->base_rate ?? 0, 2) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="room-status status-{{ strtolower($room->status) }}">
                                {{ ucwords(str_replace('_', ' ', strtolower($room->status))) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Location Details
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Property</div>
                        <div class="info-value">{{ $room->property->name }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Building</div>
                        <div class="info-value">{{ $room->floor->building->name }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Floor</div>
                        <div class="info-value">Floor {{ $room->floor->number }}</div>
                    </div>
                    
                    @if($room->floor->description)
                        <div class="info-item">
                            <div class="info-label">Floor Description</div>
                            <div class="info-value">{{ $room->floor->description }}</div>
                        </div>
                    @endif
                </div>

                <!-- Room Features -->
                @if($room->features->count() > 0)
                    <div class="features-section">
                        <div class="section-title">
                            <i class="fas fa-star"></i>
                            Room Features
                        </div>
                        
                        <div class="features-grid">
                            @foreach($room->features as $feature)
                                <div class="feature-tag">
                                    <i class="fas fa-check"></i>
                                    {{ $feature->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Room Notes -->
                @if($room->notes)
                    <div class="notes-section">
                        <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                        <p>{{ $room->notes }}</p>
                    </div>
                @endif

                <!-- Room Type Details -->
                @if($room->roomType && $room->roomType->description)
                    <div class="notes-section">
                        <h4><i class="fas fa-info-circle"></i> Room Type Description</h4>
                        <p>{{ $room->roomType->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="sidebar-card">
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="section-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </div>
                    
                    @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                        <a href="{{ route('tenant.rooms.edit', $room->id) }}" class="btn btn-primary action-button">
                            <i class="fas fa-edit"></i>
                            Edit Room
                        </a>
                        
                        <button onclick="deleteRoom('{{ $room->id }}')" class="btn btn-danger action-button">
                            <i class="fas fa-trash"></i>
                            Delete Room
                        </button>
                    @endif
                    
                    <a href="{{ route('tenant.rooms.create') }}" class="btn btn-secondary action-button">
                        <i class="fas fa-plus"></i>
                        Add New Room
                    </a>
                </div>

                <!-- Status Updater -->
                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                    <div class="status-updater">
                        <h4><i class="fas fa-sync-alt"></i> Update Status</h4>
                        <div class="status-grid">
                            @foreach(\App\Models\Room::STATUSES as $status)
                                <button class="status-btn {{ $room->status === $status ? 'active' : '' }}" 
                                        onclick="updateStatus('{{ $status }}')">
                                    {{ ucwords(str_replace('_', ' ', strtolower($status))) }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateStatus(status) {
            if (confirm(`Are you sure you want to change the room status to "${status.replace('_', ' ')}"?`)) {
                $.ajax({
                    url: `/rooms/{{ $room->id }}/update-status`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to update status');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    }
                });
            }
        }

        function deleteRoom(roomId) {
            if (confirm('Are you sure you want to delete this room? This action cannot be undone.')) {
                $.ajax({
                    url: `/rooms/${roomId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("tenant.rooms.index") }}';
                        } else {
                            alert(response.message || 'Failed to delete room');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to delete room';
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