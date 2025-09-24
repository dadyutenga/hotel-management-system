@extends('layouts.app')

@section('title', 'Room Type Details')

@section('content')
@include('Users.shared.sidebars.manager')

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

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Status toggle functionality
    $('.status-toggle').change(function() {
        const roomTypeId = $(this).data('room-type-id');
        const isActive = $(this).is(':checked');
        
        $.ajax({
            url: `/room-types/${roomTypeId}/status`,
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Update the status text and badge
                    $('#status-text').text(isActive ? 'Active' : 'Inactive');
                    const badge = $('.badge-lg');
                    badge.removeClass(isActive ? 'badge-secondary' : 'badge-success')
                         .addClass(isActive ? 'badge-success' : 'badge-secondary')
                         .text(isActive ? 'Active' : 'Inactive');
                } else {
                    toastr.error(response.message);
                    // Revert the toggle
                    $(this).prop('checked', !isActive);
                }
            },
            error: function() {
                toastr.error('Failed to update status');
                // Revert the toggle
                $(this).prop('checked', !isActive);
            }
        });
    });
});

// Delete room type functionality
let roomTypeToDelete = null;

function deleteRoomType(roomTypeId, roomTypeName) {
    roomTypeToDelete = roomTypeId;
    $('#deleteRoomTypeName').text(roomTypeName);
    $('#deleteModal').modal('show');
}

$('#confirmDelete').click(function() {
    if (roomTypeToDelete) {
        $.ajax({
            url: `/room-types/${roomTypeToDelete}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    // Redirect to index page
                    setTimeout(function() {
                        window.location.href = '{{ route("tenant.room-types.index") }}';
                    }, 1500);
                } else {
                    toastr.error(response.message);
                }
                $('#deleteModal').modal('hide');
                roomTypeToDelete = null;
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response ? response.message : 'Failed to delete room type');
                $('#deleteModal').modal('hide');
                roomTypeToDelete = null;
            }
        });
    }
});
</script>
@endsection

@section('styles')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.7em;
}

.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.status-toggle {
    cursor: pointer;
}

.custom-control-label {
    cursor: pointer;
}
</style>
@endsection