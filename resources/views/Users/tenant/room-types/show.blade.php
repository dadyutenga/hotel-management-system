<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Type Details - HotelPro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same styles as create view but optimized for show page */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background-color: #f8f9fa; color: #333; line-height: 1.6; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { margin-left: 280px; flex: 1; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .page-title { font-size: 2rem; font-weight: 600; color: #2c3e50; margin: 0; }
        .breadcrumb { display: flex; align-items: center; gap: 10px; color: #666; font-size: 0.9rem; margin-top: 10px; }
        .breadcrumb a { color: #007bff; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px; overflow: hidden; }
        .card-header { padding: 20px 30px; background: #f8f9fa; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.3rem; font-weight: 600; color: #2c3e50; margin: 0; }
        .card-body { padding: 30px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; margin-right: 10px; margin-bottom: 10px; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; transform: translateY(-1px); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; transform: translateY(-1px); }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; transform: translateY(-1px); }
        .float-right { float: right; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; border: 1px solid #dee2e6; }
        .table th { background-color: #f8f9fa; font-weight: 600; }
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 500; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .badge-lg { font-size: 0.9em; padding: 0.5em 0.7em; }
        .text-success { color: #28a745 !important; }
        .text-info { color: #17a2b8 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-muted { color: #6c757d !important; }
        .font-weight-bold { font-weight: bold !important; }
        .custom-control { position: relative; display: block; min-height: 1.5rem; padding-left: 2.25rem; }
        .custom-control-input { position: absolute; z-index: -1; opacity: 0; }
        .custom-control-label { position: relative; margin-bottom: 0; cursor: pointer; }
        .custom-switch .custom-control-label::before { left: -2.25rem; width: 1.75rem; pointer-events: all; border-radius: 0.5rem; position: absolute; top: 0.25rem; display: block; height: 1rem; content: ""; background-color: #fff; border: 1px solid #adb5bd; }
        .custom-switch .custom-control-label::after { top: calc(0.25rem + 2px); left: calc(-2.25rem + 2px); width: calc(1rem - 4px); height: calc(1rem - 4px); background-color: #adb5bd; border-radius: 0.5rem; transition: all 0.15s ease-in-out; position: absolute; display: block; content: ""; }
        .custom-switch .custom-control-input:checked ~ .custom-control-label::after { background-color: #fff; transform: translateX(0.75rem); }
        .custom-switch .custom-control-input:checked ~ .custom-control-label::before { color: #fff; border-color: #007bff; background-color: #007bff; }
        .info-box { display: flex; align-items: center; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .info-box-icon { width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-size: 1.5rem; }
        .bg-info { background-color: #17a2b8; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
        .bg-danger { background-color: #dc3545; }
        .info-box-content { flex: 1; }
        .info-box-text { font-size: 0.9rem; color: #666; margin-bottom: 5px; }
        .info-box-number { font-size: 1.5rem; font-weight: 600; color: #333; }
        .row { display: flex; flex-wrap: wrap; margin: -15px; }
        .col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding: 15px; }
        .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 15px; }
        .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 15px; }
        .col-12 { flex: 0 0 100%; max-width: 100%; padding: 15px; }
        .mt-4 { margin-top: 1.5rem !important; }
        .table-responsive { overflow-x: auto; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.05); }
        .table-bordered { border: 1px solid #dee2e6; }
        .table-bordered th, .table-bordered td { border: 1px solid #dee2e6; }
        .btn-sm { padding: 6px 12px; font-size: 0.875rem; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .modal { position: fixed; top: 0; left: 0; z-index: 1050; display: none; width: 100%; height: 100%; overflow: hidden; outline: 0; background: rgba(0,0,0,0.5); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-dialog { position: relative; width: auto; margin: 0.5rem; pointer-events: none; }
        .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; background-color: #fff; border: 1px solid rgba(0,0,0,.2); border-radius: 0.3rem; outline: 0; max-width: 500px; }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem; border-bottom: 1px solid #dee2e6; }
        .modal-title { margin-bottom: 0; line-height: 1.5; }
        .modal-body { position: relative; flex: 1 1 auto; padding: 1rem; }
        .modal-footer { display: flex; align-items: center; justify-content: flex-end; padding: 0.75rem; border-top: 1px solid #dee2e6; }
        .close { float: right; font-size: 1.5rem; font-weight: 700; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .5; background: none; border: none; cursor: pointer; }
        .close:hover { opacity: .75; }
        .alert { padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeaa7; }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .container { padding: 15px; }
            .header { padding: 20px; }
            .card-body { padding: 20px; }
            .row { margin: 0; }
            .col-md-8, .col-md-4, .col-md-6, .col-12 { flex: 0 0 100%; max-width: 100%; padding: 10px; }
            .float-right { float: none; }
            .btn { margin: 5px 5px 5px 0; }
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
                    <h1 class="m-0">Room Type Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tenant.room-types.index') }}">Room Types</a></li>
                        <li class="breadcrumb-item active">{{ $roomType->name }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('tenant.room-types.edit', $roomType->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Room Type
                    </a>
                    <a href="{{ route('tenant.room-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="button" class="btn btn-danger float-right" 
                            onclick="deleteRoomType('{{ $roomType->id }}', '{{ $roomType->name }}')">
                        <i class="fas fa-trash"></i> Delete Room Type
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Room Type Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bed"></i> {{ $roomType->name }}
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-{{ $roomType->is_active ? 'success' : 'secondary' }} badge-lg">
                                    {{ $roomType->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Property</th>
                                            <td>{{ $roomType->property->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Base Rate</th>
                                            <td class="text-success font-weight-bold">${{ number_format($roomType->base_rate, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Maximum Occupancy</th>
                                            <td><i class="fas fa-users text-info"></i> {{ $roomType->max_occupancy }} guests</td>
                                        </tr>
                                        @if($roomType->size_sqm)
                                        <tr>
                                            <th>Size</th>
                                            <td><i class="fas fa-expand-arrows-alt text-warning"></i> {{ $roomType->size_sqm }} sq.m</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input status-toggle" 
                                                           id="status-toggle" 
                                                           data-room-type-id="{{ $roomType->id }}"
                                                           {{ $roomType->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="status-toggle">
                                                        <span id="status-text">{{ $roomType->is_active ? 'Active' : 'Inactive' }}</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    @if($roomType->description)
                                        <h5>Description</h5>
                                        <p class="text-muted">{{ $roomType->description }}</p>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                                            <p>No description provided</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics & Quick Info -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i> Statistics
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-door-open"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Rooms</span>
                                    <span class="info-box-number">{{ $roomType->rooms->count() }}</span>
                                </div>
                            </div>

                            @if($roomType->rooms->count() > 0)
                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Available Rooms</span>
                                        <span class="info-box-number">{{ $roomType->rooms->where('status', 'available')->count() }}</span>
                                    </div>
                                </div>

                                <div class="info-box mb-3">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-tools"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Under Maintenance</span>
                                        <span class="info-box-number">{{ $roomType->rooms->where('status', 'maintenance')->count() }}</span>
                                    </div>
                                </div>

                                <div class="info-box">
                                    <span class="info-box-icon bg-danger">
                                        <i class="fas fa-ban"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Out of Order</span>
                                        <span class="info-box-number">{{ $roomType->rooms->where('status', 'out_of_order')->count() }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Audit Information -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i> Audit Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <strong>Created:</strong><br>
                                {{ $roomType->created_at->format('M j, Y \a\t g:i A') }}
                            </small>
                            
                            @if($roomType->updated_at && $roomType->updated_at != $roomType->created_at)
                                <br><br>
                                <small class="text-muted">
                                    <strong>Last Updated:</strong><br>
                                    {{ $roomType->updated_at->format('M j, Y \a\t g:i A') }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Using This Type -->
            @if($roomType->rooms->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list"></i> Rooms Using This Type ({{ $roomType->rooms->count() }})
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Room Number</th>
                                                <th>Building</th>
                                                <th>Floor</th>
                                                <th>Status</th>
                                                <th>Last Cleaned</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roomType->rooms as $room)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $room->room_number }}</strong>
                                                    </td>
                                                    <td>{{ $room->floor->building->name }}</td>
                                                    <td>{{ $room->floor->name }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ 
                                                            $room->status == 'available' ? 'success' : 
                                                            ($room->status == 'occupied' ? 'primary' : 
                                                            ($room->status == 'maintenance' ? 'warning' : 'danger')) 
                                                        }}">
                                                            {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($room->last_cleaned)
                                                            {{ \Carbon\Carbon::parse($room->last_cleaned)->format('M j, Y') }}
                                                        @else
                                                            <span class="text-muted">Never</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('tenant.rooms.show', $room->id) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>
</div>

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
                @if($roomType->rooms->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This room type has {{ $roomType->rooms->count() }} associated rooms. 
                        You cannot delete it until all rooms are reassigned to other room types.
                    </div>
                @else
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                @if($roomType->rooms->count() == 0)
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                @endif
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
                    @if($roomType->rooms->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This room type has {{ $roomType->rooms->count() }} associated rooms. 
                            You cannot delete it until all rooms are reassigned to other room types.
                        </div>
                    @else
                        <p style="color: #dc3545;"><small>This action cannot be undone.</small></p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                    @if($roomType->rooms->count() == 0)
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functionality
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Status toggle functionality
        const statusToggle = document.querySelector('.status-toggle');
        if (statusToggle) {
            statusToggle.addEventListener('change', function() {
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
                        // Update the status text and badge
                        const statusText = document.getElementById('status-text');
                        const badge = document.querySelector('.badge-lg');
                        if (statusText) {
                            statusText.textContent = isActive ? 'Active' : 'Inactive';
                        }
                        if (badge) {
                            badge.className = isActive ? 'badge badge-success badge-lg' : 'badge badge-secondary badge-lg';
                            badge.textContent = isActive ? 'Active' : 'Inactive';
                        }
                        console.log(data.message);
                    } else {
                        console.error(data.message);
                        this.checked = !isActive;
                    }
                })
                .catch(error => {
                    console.error('Failed to update status');
                    this.checked = !isActive;
                });
            });
        }

        // Delete room type functionality
        let roomTypeToDelete = null;

        function deleteRoomType(roomTypeId, roomTypeName) {
            roomTypeToDelete = roomTypeId;
            document.getElementById('deleteRoomTypeName').textContent = roomTypeName;
            document.getElementById('deleteModal').classList.add('show');
        }

        const confirmDelete = document.getElementById('confirmDelete');
        if (confirmDelete) {
            confirmDelete.addEventListener('click', function() {
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
                            console.log(data.message);
                            // Redirect to index page
                            setTimeout(function() {
                                window.location.href = '{{ route("tenant.room-types.index") }}';
                            }, 1500);
                        } else {
                            console.error(data.message);
                        }
                        closeModal('deleteModal');
                        roomTypeToDelete = null;
                    })
                    .catch(error => {
                        console.error('Failed to delete room type');
                        closeModal('deleteModal');
                        roomTypeToDelete = null;
                    });
                }
            });
        }
    </script>
</body>
</html>