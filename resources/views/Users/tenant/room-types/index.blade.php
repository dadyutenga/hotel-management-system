<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Types Management - HotelPro</title>
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
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 600;
            color: #4caf50;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .filters {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
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
        
        .btn-outline {
            background: transparent;
            color: #4caf50;
            border: 2px solid #4caf50;
        }
        
        .btn-outline:hover {
            background: #4caf50;
            color: white;
        }
        
        .room-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .room-type-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .room-type-card:hover {
            transform: translateY(-5px);
            border-color: #4caf50;
        }
        
        .room-type-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .room-type-name {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .room-type-property {
            color: #666;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .room-type-info {
            margin: 20px 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .info-item i {
            color: #4caf50;
            width: 16px;
        }
        
        .info-value {
            font-weight: 600;
            color: #333;
        }
        
        .room-type-description {
            color: #666;
            font-size: 14px;
            margin: 15px 0;
            line-height: 1.5;
        }
        
        .room-type-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .btn-success {
            background: #28a745;
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
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .no-results i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        .no-results h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .no-results p {
            color: #999;
            margin-bottom: 20px;
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
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .room-types-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
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
                    <h1><i class="fas fa-bed"></i> Room Types Management</h1>
                    <p>Manage room types and their configurations</p>
                </div>
                <a href="{{ route('tenant.room-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Room Type
                </a>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $roomTypes->total() }}</div>
                    <div class="stat-label">Total Room Types</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $roomTypes->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Active Types</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $properties->count() }}</div>
                    <div class="stat-label">Properties</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${{ number_format($roomTypes->avg('base_rate'), 0) }}</div>
                    <div class="stat-label">Average Rate</div>
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

        <!-- Filters -->
        <div class="filters">
            <div class="filters-header">
                <h3 class="filters-title"><i class="fas fa-filter"></i> Filters</h3>
                <a href="{{ route('tenant.room-types.index') }}" class="btn btn-outline">
                    <i class="fas fa-undo"></i>
                    Clear Filters
                </a>
            </div>
            
            <form method="GET" action="{{ route('tenant.room-types.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label for="property_id">Property</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">All Properties</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ $propertyId == $property->id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ $search }}" placeholder="Name or description...">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Room Types Grid -->
        @if($roomTypes->count() > 0)
            <div class="room-types-grid">
                @foreach($roomTypes as $roomType)
                    <div class="room-type-card">
                        <div class="room-type-header">
                            <div>
                                <h3 class="room-type-name">{{ $roomType->name }}</h3>
                                <p class="room-type-property">{{ $roomType->property->name }}</p>
                            </div>
                            <span class="status-badge {{ $roomType->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        
                        <div class="room-type-info">
                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span class="info-value">${{ number_format($roomType->base_rate, 2) }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span class="info-value">{{ $roomType->max_occupancy }} guests</span>
                                </div>
                                @if($roomType->size_sqm)
                                    <div class="info-item">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                        <span class="info-value">{{ $roomType->size_sqm }} m²</span>
                                    </div>
                                @endif
                                <div class="info-item">
                                    <i class="fas fa-door-open"></i>
                                    <span class="info-value">{{ $roomType->rooms->count() }} rooms</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($roomType->description)
                            <div class="room-type-description">
                                {{ Str::limit($roomType->description, 120) }}
                            </div>
                        @endif
                        
                        <div class="room-type-actions">
                            <a href="{{ route('tenant.room-types.show', $roomType->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                            <a href="{{ route('tenant.room-types.edit', $roomType->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <button onclick="toggleStatus('{{ $roomType->id }}', {{ $roomType->is_active ? 'false' : 'true' }})" 
                                    class="btn {{ $roomType->is_active ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                <i class="fas fa-{{ $roomType->is_active ? 'pause' : 'play' }}"></i>
                                {{ $roomType->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            @if($roomType->rooms->count() == 0)
                                <button onclick="deleteRoomType('{{ $roomType->id }}')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $roomTypes->appends(request()->query())->links() }}
            </div>
        @else
            <div class="no-results">
                <i class="fas fa-bed"></i>
                <h3>No Room Types Found</h3>
                <p>There are no room types matching your current filters.</p>
                <a href="{{ route('tenant.room-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create First Room Type
                </a>
            </div>
        @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleStatus(roomTypeId, newStatus) {
            const isActive = newStatus === 'true';
            const action = isActive ? 'activate' : 'deactivate';
            
            if (confirm(`Are you sure you want to ${action} this room type?`)) {
                $.ajax({
                    url: `/room-types/${roomTypeId}/status`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        is_active: isActive
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

        function deleteRoomType(roomTypeId) {
            if (confirm('Are you sure you want to delete this room type? This action cannot be undone.')) {
                $.ajax({
                    url: `/room-types/${roomTypeId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to delete room type');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to delete room type';
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