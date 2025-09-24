@extends('layouts.app')

@section('title', 'Create Room Type')

@section('content')
@include('Users.shared.sidebars.manager')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Room Type</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tenant.room-types.index') }}">Room Types</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bed"></i> Create New Room Type
                            </h3>
                        </div>
                        
                        <form action="{{ route('tenant.room-types.store') }}" method="POST" id="roomTypeForm">
                            @csrf
                            
                            <div class="card-body">
                                <!-- Property Selection -->
                                <div class="form-group">
                                    <label for="property_id" class="required">Property</label>
                                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" 
                                                    {{ (old('property_id') == $property->id || ($selectedProperty && $selectedProperty->id == $property->id)) ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <!-- Room Type Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="required">Room Type Name</label>
                                            <input type="text" name="name" id="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="e.g., Standard Single, Deluxe Double, Suite" 
                                                   maxlength="100" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Base Rate -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="base_rate" class="required">Base Rate ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" name="base_rate" id="base_rate" 
                                                       class="form-control @error('base_rate') is-invalid @enderror" 
                                                       value="{{ old('base_rate') }}" 
                                                       step="0.01" min="0" max="999999.99" 
                                                       placeholder="0.00" required>
                                                @error('base_rate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="3" maxlength="500" 
                                              placeholder="Describe the room type features, amenities, etc.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <span id="descriptionCount">0</span>/500 characters
                                    </small>
                                </div>

                                <div class="row">
                                    <!-- Max Occupancy -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_occupancy" class="required">Maximum Occupancy</label>
                                            <input type="number" name="max_occupancy" id="max_occupancy" 
                                                   class="form-control @error('max_occupancy') is-invalid @enderror" 
                                                   value="{{ old('max_occupancy', 2) }}" 
                                                   min="1" max="20" required>
                                            @error('max_occupancy')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Number of guests this room type can accommodate</small>
                                        </div>
                                    </div>

                                    <!-- Size -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="size_sqm">Size (sq.m)</label>
                                            <div class="input-group">
                                                <input type="number" name="size_sqm" id="size_sqm" 
                                                       class="form-control @error('size_sqm') is-invalid @enderror" 
                                                       value="{{ old('size_sqm') }}" 
                                                       step="0.01" min="0" max="9999.99" 
                                                       placeholder="0.00">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">sq.m</span>
                                                </div>
                                                @error('size_sqm')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Active Room Type
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Active room types are available for room assignments and bookings
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Create Room Type
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{ route('tenant.room-types.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Character count for description
    $('#description').on('input', function() {
        const count = $(this).val().length;
        $('#descriptionCount').text(count);
        
        if (count > 450) {
            $('#descriptionCount').addClass('text-warning');
        } else {
            $('#descriptionCount').removeClass('text-warning');
        }
    });

    // Trigger initial count
    $('#description').trigger('input');

    // Form validation
    $('#roomTypeForm').on('submit', function(e) {
        let isValid = true;
        
        // Validate required fields
        const requiredFields = ['property_id', 'name', 'base_rate', 'max_occupancy'];
        requiredFields.forEach(function(field) {
            const input = $(`#${field}`);
            if (!input.val()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });

        // Validate base rate
        const baseRate = parseFloat($('#base_rate').val());
        if (baseRate < 0) {
            $('#base_rate').addClass('is-invalid');
            toastr.error('Base rate must be a positive number');
            isValid = false;
        }

        // Validate max occupancy
        const maxOccupancy = parseInt($('#max_occupancy').val());
        if (maxOccupancy < 1 || maxOccupancy > 20) {
            $('#max_occupancy').addClass('is-invalid');
            toastr.error('Maximum occupancy must be between 1 and 20');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            toastr.error('Please fix the errors and try again');
        }
    });

    // Remove validation classes on input
    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});

function resetForm() {
    $('#roomTypeForm')[0].reset();
    $('.form-control').removeClass('is-invalid');
    $('#descriptionCount').text('0');
    $('#is_active').prop('checked', true);
}
</script>
@endsection

@section('styles')
<style>
.required::after {
    content: " *";
    color: red;
}

.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.custom-control-label {
    cursor: pointer;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}
</style>
@endsection