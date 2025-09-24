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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
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
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #1e7e34;
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
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
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .room-type-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .room-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-toggle {
            background: none;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 160px;
            z-index: 1000;
            display: none;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: block;
            width: 100%;
            padding: 8px 15px;
            text-decoration: none;
            color: #333;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-divider {
            height: 1px;
            background: #e9ecef;
            margin: 5px 0;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
        
        .font-weight-bold {
            font-weight: bold !important;
        }
        
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        
        .d-flex {
            display: flex !important;
        }
        
        .justify-content-between {
            justify-content: space-between !important;
        }
        
        .align-items-center {
            align-items: center !important;
        }
        
        .text-center {
            text-align: center !important;
        }
        
        .custom-control {
            position: relative;
            display: block;
            min-height: 1.5rem;
            padding-left: 1.5rem;
        }
        
        .custom-control-input {
            position: absolute;
            left: 0;
            z-index: -1;
            width: 1rem;
            height: 1.25rem;
            opacity: 0;
        }
        
        .custom-switch .custom-control-label::before {
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
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: none;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            background: rgba(0,0,0,0.5);
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 0.5rem;
            pointer-events: none;
        }
        
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 0.3rem;
            outline: 0;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
        
        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
        }
        
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }
        
        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .close:hover {
            opacity: .75;
        }
        
        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .empty-state h4 {
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #6c757d;
            margin-bottom: 1.5rem;
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
            
            .card-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 13px;
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
                    <div>
                        <h1 class="page-title">Room Types Management</h1>
                        <div class="breadcrumb">
                            <a href="{{ route('user.dashboard') }}">Home</a>
                            <span>/</span>
                            <span>Room Types</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Filter Room Types</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('tenant.room-types.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
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
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by name or description..." value="{{ $search }}">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <a href="{{ route('tenant.room-types.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mb-4">
                    <a href="{{ route('tenant.room-types.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Room Type
                    </a>
                </div>

                <!-- Room Types Grid -->
                @if($roomTypes->count() > 0)
                    <div class="card-grid">
                        @foreach($roomTypes as $roomType)
                            <div class="room-type-card card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 style="margin: 0;">{{ $roomType->name }}</h5>
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" type="button">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('tenant.room-types.show', $roomType->id) }}">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            <a class="dropdown-item" href="{{ route('tenant.room-types.edit', $roomType->id) }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger" 
                                                    onclick="deleteRoomType('{{ $roomType->id }}', '{{ $roomType->name }}')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Property:</strong>
                                        <span class="text-muted">{{ $roomType->property->name }}</span>
                                    </div>
                                    
                                    @if($roomType->description)
                                        <div class="mb-3">
                                            <strong>Description:</strong>
                                            <p class="text-muted" style="margin-bottom: 0;">{{ Str::limit($roomType->description, 100) }}</p>
                                        </div>
                                    @endif
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                        <div>
                                            <strong>Base Rate:</strong>
                                            <div class="text-success font-weight-bold">${{ number_format($roomType->base_rate, 2) }}</div>
                                        </div>
                                        <div>
                                            <strong>Max Occupancy:</strong>
                                            <div class="text-info">{{ $roomType->max_occupancy }} guests</div>
                                        </div>
                                    </div>
                                    
                                    @if($roomType->size_sqm)
                                        <div class="mb-3">
                                            <strong>Size:</strong>
                                            <span class="text-muted">{{ $roomType->size_sqm }} sq.m</span>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge badge-{{ $roomType->is_active ? 'success' : 'secondary' }}">
                                                {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input status-toggle" 
                                                       id="status-{{ $roomType->id }}" 
                                                       data-room-type-id="{{ $roomType->id }}"
                                                       {{ $roomType->is_active ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="status-{{ $roomType->id }}"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #e9ecef; text-align: center;">
                                    <a href="{{ route('tenant.room-types.show', $roomType->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('tenant.room-types.edit', $roomType->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($roomTypes->hasPages())
                        <div style="margin-top: 30px; text-align: center;">
                            {{ $roomTypes->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="card">
                        <div class="card-body empty-state">
                            <i class="fas fa-bed"></i>
                            <h4>No Room Types Found</h4>
                            <p>You haven't created any room types yet.</p>
                            <a href="{{ route('tenant.room-types.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Your First Room Type
                            </a>
                        </div>
                    </div>
                @endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the room type "<span id="deleteRoomTypeName"></span>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" onclick="closeModal('deleteModal')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the room type "<span id="deleteRoomTypeName"></span>"?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <script>
        // Dropdown functionality
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== dropdown) menu.classList.remove('show');
                });
                dropdown.classList.toggle('show');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        });

        // Modal functionality
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Status toggle functionality
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const roomTypeId = this.getAttribute('data-room-type-id');
                const isActive = this.checked;
                
                fetch(`/room-types/${roomTypeId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        is_active: isActive
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Update the badge
                        const badge = this.closest('.room-type-card').querySelector('.badge');
                        if (badge) {
                            badge.className = isActive ? 'badge badge-success' : 'badge badge-secondary';
                            badge.textContent = isActive ? 'Active' : 'Inactive';
                        }
                    } else {
                        toastr.error(data.message);
                        this.checked = !isActive;
                    }
                })
                .catch(error => {
                    toastr.error('Failed to update status');
                    this.checked = !isActive;
                });
            });
        });

        // Delete room type functionality
        let roomTypeToDelete = null;

        function deleteRoomType(roomTypeId, roomTypeName) {
            roomTypeToDelete = roomTypeId;
            document.getElementById('deleteRoomTypeName').textContent = roomTypeName;
            document.getElementById('deleteModal').classList.add('show');
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (roomTypeToDelete) {
                fetch(`/room-types/${roomTypeToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        // Remove the card from the grid
                        const card = document.querySelector(`[data-room-type-id="${roomTypeToDelete}"]`).closest('.room-type-card').parentElement;
                        card.style.transition = 'opacity 0.3s ease';
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                    } else {
                        toastr.error(data.message);
                    }
                    closeModal('deleteModal');
                    roomTypeToDelete = null;
                })
                .catch(error => {
                    toastr.error('Failed to delete room type');
                    closeModal('deleteModal');
                    roomTypeToDelete = null;
                });
            }
        });
    </script>
</body>
</html>
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