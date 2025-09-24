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
        
        .filters {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
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
            border-color: #007bff;
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
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
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
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
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
            border-color: #007bff;
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
            color: #007bff;
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
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
        @include('Users.shared.sidebars.manager')

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
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Filters -->
                <div class="filters">
                    <h3 class="filters-title"><i class="fas fa-filter"></i> Filters</h3>
                    
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
                                       value="{{ $search }}" placeholder="Search room types...">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    Filter
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
                                        <div class="room-type-name">{{ $roomType->name }}</div>
                                        <div class="room-type-property">
                                            <i class="fas fa-building"></i> {{ $roomType->property->name }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if($roomType->description)
                                    <div class="room-type-description">
                                        {{ Str::limit($roomType->description, 100) }}
                                    </div>
                                @endif
                                
                                <div class="room-type-info">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <span class="info-value">Tzs {{ number_format($roomType->base_rate, 2) }}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-users"></i>
                                            <span class="info-value">{{ $roomType->capacity }} {{ $roomType->capacity == 1 ? 'guest' : 'guests' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="room-type-actions">
                                    <a href="{{ route('tenant.room-types.show', $roomType->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                    <a href="{{ route('tenant.room-types.edit', $roomType->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <button onclick="deleteRoomType('{{ $roomType->id }}', '{{ $roomType->name }}')" 
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($roomTypes->hasPages())
                        <div style="margin-top: 30px; display: flex; justify-content: center;">
                            {{ $roomTypes->appends(request()->query())->links() }}
                        </div>
                    @endif
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Confirm Delete</h5>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the room type "<span id="deleteRoomTypeName"></span>"?</p>
                <p style="color: #dc3545; font-size: 0.9rem;">This action cannot be undone.</p>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-primary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Delete room type functionality
        let roomTypeToDelete = null;

        function deleteRoomType(roomTypeId, roomTypeName) {
            roomTypeToDelete = roomTypeId;
            document.getElementById('deleteRoomTypeName').textContent = roomTypeName;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            roomTypeToDelete = null;
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (roomTypeToDelete) {
                fetch(`/room-types/${roomTypeToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the card from the grid
                        const card = document.querySelector(`[onclick*="${roomTypeToDelete}"]`).closest('.room-type-card');
                        if (card) {
                            card.style.transition = 'all 0.3s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.8)';
                            setTimeout(() => card.remove(), 300);
                        }
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message || 'Failed to delete room type', 'error');
                    }
                    closeDeleteModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to delete room type', 'error');
                    closeDeleteModal();
                });
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        });

        // Show message function
        function showMessage(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alert = document.createElement('div');
            alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
            alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
            
            // Insert after header
            const header = document.querySelector('.header');
            header.insertAdjacentElement('afterend', alert);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>