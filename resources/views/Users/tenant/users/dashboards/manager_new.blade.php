@extends('Users.shared.layouts.dashboard')

@section('title', 'Manager Dashboard')
@section('theme-color', '#4caf50')
@section('page-title', 'Manager Dashboard')
@section('page-subtitle', 'Welcome back, {{ $user->full_name }}! Manage your property operations efficiently.')

@section('sidebar')
    @include('Users.shared.sidebars.manager')
@endsection

@section('welcome-gradient', 'linear-gradient(135deg, #4caf50 0%, #66bb6a 100%)')
@section('welcome-icon', '<i class="fas fa-user-tie"></i>')
@section('welcome-title', 'Property Manager Control Panel')
@section('welcome-description', 'Oversee daily operations, manage staff, and ensure excellent service delivery at your property.')

@push('styles')
<style>
    .stat-card {
        border-left: 5px solid #4caf50;
    }
    
    .stat-icon {
        background: rgba(76, 175, 80, 0.1);
        color: #4caf50;
    }
    
    .card-header {
        background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
    }
    
    .action-item:hover {
        border-color: #4caf50;
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
    }
    
    .action-icon {
        background: #4caf50;
    }
    
    .property-info {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .property-header {
        background: linear-gradient(135deg, #2196f3 0%, #42a5f5 100%);
        color: white;
        padding: 20px 25px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .property-content {
        padding: 25px;
    }
    
    .property-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .property-detail {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    
    .property-detail h4 {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    
    .property-detail p {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
</style>
@endpush

@section('content')
    <!-- Property Information -->
    @if($user->property)
        <div class="property-info">
            <div class="property-header">
                <i class="fas fa-building"></i>
                Property: {{ $user->property->name }}
            </div>
            
            <div class="property-content">
                <div class="property-details">
                    <div class="property-detail">
                        <h4>Address</h4>
                        <p>{{ $user->property->address }}</p>
                    </div>
                    
                    <div class="property-detail">
                        <h4>Phone</h4>
                        <p>{{ $user->property->contact_phone }}</p>
                    </div>
                    
                    <div class="property-detail">
                        <h4>Email</h4>
                        <p>{{ $user->property->email }}</p>
                    </div>
                    
                    <div class="property-detail">
                        <h4>Status</h4>
                        <p style="color: {{ $user->property->is_active ? '#4caf50' : '#f44336' }};">
                            {{ $user->property->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Property Staff</div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value">{{ $dashboardData['property_users'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Active Staff</div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-value">{{ $dashboardData['active_users'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Buildings</div>
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="stat-value">{{ $dashboardData['buildings'] ?? 0 }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Tasks Today</div>
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
            <div class="stat-value">0</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="card-header">
            <i class="fas fa-bolt"></i>
            Quick Actions
        </div>
        
        <div class="card-content">
            <div class="actions-grid">
                @if($user->property)
                    <a href="{{ route('tenant.properties.show', $user->property->id) }}" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="action-info">
                            <h4>Property Details</h4>
                            <p>View property information</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('tenant.properties.edit', $user->property->id) }}" class="action-item">
                        <div class="action-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="action-info">
                            <h4>Edit Property</h4>
                            <p>Update property details</p>
                        </div>
                    </a>
                @endif
                
                <a href="{{ route('tenant.users.index') }}" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-info">
                        <h4>Manage Staff</h4>
                        <p>View and manage staff</p>
                    </div>
                </a>
                
                <a href="{{ route('tenant.users.create') }}" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-info">
                        <h4>Add Staff</h4>
                        <p>Create new staff account</p>
                    </div>
                </a>
                
                <a href="#" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="action-info">
                        <h4>Room Management</h4>
                        <p>Manage rooms and availability</p>
                    </div>
                </a>
                
                <a href="#" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="action-info">
                        <h4>Reservations</h4>
                        <p>View and manage bookings</p>
                    </div>
                </a>
                
                <a href="#" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="action-info">
                        <h4>Property Reports</h4>
                        <p>View performance analytics</p>
                    </div>
                </a>
                
                <a href="#" class="action-item">
                    <div class="action-icon">
                        <i class="fas fa-broom"></i>
                    </div>
                    <div class="action-info">
                        <h4>Housekeeping</h4>
                        <p>Manage cleaning tasks</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Manager-specific dashboard functionality
    function updateDashboardStats() {
        // Fetch updated statistics for manager dashboard
        $.ajax({
            url: '{{ route("user.dashboard.stats") }}',
            method: 'GET',
            success: function(data) {
                if (data.property_users !== undefined) {
                    $('.stat-value').eq(0).text(data.property_users);
                }
                if (data.active_users !== undefined) {
                    $('.stat-value').eq(1).text(data.active_users);
                }
                if (data.buildings !== undefined) {
                    $('.stat-value').eq(2).text(data.buildings);
                }
            },
            error: function() {
                console.log('Failed to update dashboard stats');
            }
        });
    }
</script>
@endpush