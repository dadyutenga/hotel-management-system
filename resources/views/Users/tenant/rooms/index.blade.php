<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management - HotelPro</title>
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
            max-width: 1400px;
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
        
        .header-content h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .header-content p {
            color: #666;
            font-size: 16px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
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
        
        .filters {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
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
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4caf50;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .room-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .room-card.occupied {
            border-left-color: #dc3545;
        }
        
        .room-card.vacant {
            border-left-color: #28a745;
        }
        
        .room-card.dirty {
            border-left-color: #ffc107;
        }
        
        .room-card.clean {
            border-left-color: #17a2b8;
        }
        
        .room-card.out-of-order {
            border-left-color: #6c757d;
        }
        
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .room-number {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }
        
        .room-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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
        
        .room-details {
            margin-bottom: 20px;
        }
        
        .room-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .room-info strong {
            color: #333;
        }
        
        .room-info span {
            color: #666;
        }
        
        .room-features {
            margin-bottom: 20px;
        }
        
        .features-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        
        .feature-tag {
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .room-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-state p {
            color: #666;
            margin-bottom: 30px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
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
                text-align: center;
            }
            
            .filters-grid {
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
                <h1><i class="fas fa-door-open"></i> Room Management</h1>
                <p>Manage rooms, types, and features for your property</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('tenant.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New Room
                </a>
                <a href="{{ route('tenant.room-types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i>
                    Room Types
                </a>
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

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="{{ route('tenant.rooms.index') }}">
                <div class="filters-grid">
                    @if(Auth::user()->role->name === 'DIRECTOR')
                        <div class="form-group">
                            <label for="property_id">Property</label>
                            <select name="property_id" id="property_id" class="form-control">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach(\App\Models\Room::STATUSES as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', strtolower($status))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="room_number">Room Number</label>
                        <input type="text" name="room_number" id="room_number" class="form-control" 
                               placeholder="Search by room number" value="{{ request('room_number') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Rooms Grid -->
        @if($rooms->count() > 0)
            <div class="rooms-grid">
                @foreach($rooms as $room)
                    <div class="room-card {{ strtolower($room->status) }}">
                        <div class="room-header">
                            <div class="room-number">Room {{ $room->room_number }}</div>
                            <div class="room-status status-{{ strtolower($room->status) }}">
                                {{ ucwords(str_replace('_', ' ', strtolower($room->status))) }}
                            </div>
                        </div>
                        
                        <div class="room-details">
                            <div class="room-info">
                                <strong>Type:</strong>
                                <span>{{ $room->roomType->name ?? 'N/A' }}</span>
                            </div>
                            <div class="room-info">
                                <strong>Floor:</strong>
                                <span>{{ $room->floor->number ?? 'N/A' }} - {{ $room->floor->building->name ?? 'N/A' }}</span>
                            </div>
                            <div class="room-info">
                                <strong>Property:</strong>
                                <span>{{ $room->property->name ?? 'N/A' }}</span>
                            </div>
                            <div class="room-info">
                                <strong>Rate:</strong>
                                <span>${{ number_format($room->current_rate, 2) }}</span>
                            </div>
                        </div>
                        
                        @if($room->features->count() > 0)
                            <div class="room-features">
                                <div class="features-title">Features</div>
                                <div class="features-list">
                                    @foreach($room->features as $feature)
                                        <span class="feature-tag">{{ $feature->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="room-actions">
                            <a href="{{ route('tenant.rooms.show', $room->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                            <a href="{{ route('tenant.rooms.edit', $room->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                <button onclick="deleteRoom('{{ $room->id }}')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination">
                {{ $rooms->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-door-open"></i>
                <h3>No Rooms Found</h3>
                <p>Start by creating your first room to manage your property inventory.</p>
                <a href="{{ route('tenant.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create First Room
                </a>
            </div>
        @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
                            location.reload();
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