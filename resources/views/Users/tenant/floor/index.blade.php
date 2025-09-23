<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floor Management - HotelPro</title>
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
        
        .btn-outline {
            background: transparent;
            color: #4caf50;
            border: 2px solid #4caf50;
        }
        
        .btn-outline:hover {
            background: #4caf50;
            color: white;
        }
        
        .floors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .floor-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .floor-card:hover {
            transform: translateY(-5px);
            border-color: #4caf50;
        }
        
        .floor-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .floor-number {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .floor-info {
            margin: 15px 0;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-item i {
            color: #4caf50;
            width: 16px;
        }
        
        .floor-description {
            color: #666;
            font-size: 14px;
            margin: 15px 0;
            line-height: 1.5;
        }
        
        .floor-actions {
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
        
        .btn-info {
            background: #17a2b8;
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
        
        .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: center;
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
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .floors-grid {
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
                            <h1><i class="fas fa-layer-group"></i> Floor Management</h1>
                            <p>Manage building floors and their configurations</p>
                        </div>
                        <a href="{{ route('tenant.floors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Floor
                        </a>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ $floors->total() }}</div>
                            <div class="stat-label">Total Floors</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $floors->sum(fn($floor) => $floor->rooms->count()) }}</div>
                            <div class="stat-label">Total Rooms</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $properties->count() }}</div>
                            <div class="stat-label">Properties</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ $floors->groupBy('building_id')->count() }}</div>
                            <div class="stat-label">Buildings</div>
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
                        <a href="{{ route('tenant.floors.index') }}" class="btn btn-outline">
                            <i class="fas fa-undo"></i>
                            Clear Filters
                        </a>
                    </div>
                    
                    <form method="GET" action="{{ route('tenant.floors.index') }}">
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
                                <label for="building_id">Building</label>
                                <select name="building_id" id="building_id" class="form-control">
                                    <option value="">All Buildings</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}" {{ $buildingId == $building->id ? 'selected' : '' }}>
                                            {{ $building->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ $search }}" placeholder="Floor number or description...">
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

                <!-- Floors Grid -->
                @if($floors->count() > 0)
                    <div class="floors-grid">
                        @foreach($floors as $floor)
                            <div class="floor-card">
                                <div class="floor-header">
                                    <div>
                                        <h3 class="floor-number">Floor {{ $floor->number }}</h3>
                                        <p style="color: #666; font-size: 14px;">{{ $floor->building->property->name }}</p>
                                    </div>
                                </div>
                                
                                <div class="floor-info">
                                    <div class="info-item">
                                        <i class="fas fa-building"></i>
                                        <span>{{ $floor->building->name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-door-open"></i>
                                        <span>{{ $floor->rooms->count() }} rooms</span>
                                    </div>
                                    @if($floor->rooms->count() > 0)
                                        <div class="info-item">
                                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                            <span>{{ $floor->rooms->where('status', 'AVAILABLE')->count() }} available</span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($floor->description)
                                    <div class="floor-description">
                                        {{ Str::limit($floor->description, 100) }}
                                    </div>
                                @endif
                                
                                <div class="floor-actions">
                                    <a href="{{ route('tenant.floors.show', $floor->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                    <a href="{{ route('tenant.floors.edit', $floor->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    @if($floor->rooms->count() == 0)
                                        <button onclick="deleteFloor('{{ $floor->id }}')" class="btn btn-danger btn-sm">
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
                        {{ $floors->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="no-results">
                        <i class="fas fa-layer-group"></i>
                        <h3>No Floors Found</h3>
                        <p>There are no floors matching your current filters.</p>
                        <a href="{{ route('tenant.floors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create First Floor
                        </a>
                    </div>
                @endif
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
        });
        
        function loadBuildings(propertyId) {
            if (!propertyId) {
                $('#building_id').prop('disabled', true).html('<option value="">All Buildings</option>');
                return;
            }
            
            $.ajax({
                url: `/rooms/property/${propertyId}/buildings`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(buildings) {
                    let options = '<option value="">All Buildings</option>';
                    buildings.forEach(function(building) {
                        options += `<option value="${building.id}">${building.name}</option>`;
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
                            location.reload();
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