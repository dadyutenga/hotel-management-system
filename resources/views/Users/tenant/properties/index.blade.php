<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties Management - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .brand-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .brand-subtitle {
            font-size: 14px;
            opacity: 0.8;
            font-weight: 500;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
            border-right: 4px solid #f44336;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a237e;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 35, 126, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .content {
            padding: 30px;
        }
        
        .data-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .table-content {
            padding: 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f8f9fa;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table td {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .badge-danger {
            background: #ffebee;
            color: #c62828;
        }
        
        .property-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
        }
        
        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .property-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .property-address {
            color: #666;
            font-size: 14px;
        }
        
        .property-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #1a237e;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .property-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #4caf50;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #ddd;
            color: #333;
        }
        
        .pagination .active span {
            background: #1a237e;
            color: white;
            border-color: #1a237e;
        }
        
        .pagination a:hover {
            background: #f8f9fa;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
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
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .content {
                padding: 20px;
            }
            
            .property-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .property-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">HotelPro</div>
                <div class="brand-subtitle">Property Management</div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tenant.properties.index') }}" class="nav-link active">
                        <i class="fas fa-building"></i>
                        Properties
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-door-open"></i>
                        Rooms
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        Reservations
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i>
                        Users
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Reports
                    </a>
                </div>
                
                <div class="logout-section">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="header-title">Properties Management</h1>
                <div class="header-actions">
                    @if(Auth::user()->role->name === 'DIRECTOR')
                        <a href="{{ route('tenant.properties.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add New Property
                        </a>
                    @endif
                    <div class="user-info">
                        <i class="fas fa-user"></i>
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        <span class="user-role">({{ Auth::user()->role->name }})</span>
                    </div>
                </div>
            </div>
            
            <div class="content">
                <!-- Alert Messages -->
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

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <!-- Properties List -->
                @if($properties->count() > 0)
                    <div class="data-table">
                        <div class="table-header">
                            <div class="table-title">
                                <i class="fas fa-building"></i>
                                Your Properties ({{ $properties->total() }})
                            </div>
                        </div>
                        
                        <div class="table-content">
                            @foreach($properties as $property)
                                <div class="property-card">
                                    <div class="property-header">
                                        <div>
                                            <div class="property-name">{{ $property->name }}</div>
                                            <div class="property-address">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $property->address }}
                                            </div>
                                            <div style="margin-top: 8px;">
                                                <span class="badge {{ $property->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $property->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                                <label class="toggle-switch">
                                                    <input type="checkbox" 
                                                           {{ $property->is_active ? 'checked' : '' }}
                                                           onchange="togglePropertyStatus('{{ $property->id }}', this)">
                                                    <span class="slider"></span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="property-stats">
                                        <div class="stat-item">
                                            <div class="stat-value">{{ $property->buildings_count }}</div>
                                            <div class="stat-label">Buildings</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">{{ $property->rooms_count }}</div>
                                            <div class="stat-label">Rooms</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">{{ $property->users_count }}</div>
                                            <div class="stat-label">Staff</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">{{ $property->timezone }}</div>
                                            <div class="stat-label">Timezone</div>
                                        </div>
                                    </div>
                                    
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px; color: #666;">
                                            <div>
                                                <i class="fas fa-envelope"></i>
                                                {{ $property->email }}
                                            </div>
                                            <div>
                                                <i class="fas fa-phone"></i>
                                                {{ $property->contact_phone }}
                                            </div>
                                            @if($property->website)
                                                <div>
                                                    <i class="fas fa-globe"></i>
                                                    <a href="{{ $property->website }}" target="_blank" style="color: #1a237e;">Website</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="property-actions">
                                        <a href="{{ route('tenant.properties.show', $property->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                            View Details
                                        </a>
                                        
                                        @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                            <a href="{{ route('tenant.properties.edit', $property->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>
                                        @endif
                                        
                                        @if(Auth::user()->role->name === 'DIRECTOR')
                                            <button class="btn btn-danger btn-sm" onclick="deleteProperty('{{ $property->id }}', '{{ $property->name }}')">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    @if($properties->hasPages())
                        <div class="pagination-wrapper">
                            {{ $properties->links() }}
                        </div>
                    @endif
                @else
                    <div class="data-table">
                        <div class="table-header">
                            <div class="table-title">
                                <i class="fas fa-building"></i>
                                Properties
                            </div>
                        </div>
                        
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <h3>No Properties Found</h3>
                            <p>You haven't created any properties yet.</p>
                            @if(Auth::user()->role->name === 'DIRECTOR')
                                <div style="margin-top: 20px;">
                                    <a href="{{ route('tenant.properties.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Create Your First Property
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Toggle property status
        function togglePropertyStatus(propertyId, toggle) {
            fetch(`/properties/${propertyId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge
                    const propertyCard = toggle.closest('.property-card');
                    const badge = propertyCard.querySelector('.badge');
                    
                    if (data.is_active) {
                        badge.textContent = 'Active';
                        badge.className = 'badge badge-success';
                    } else {
                        badge.textContent = 'Inactive';
                        badge.className = 'badge badge-danger';
                    }
                    
                    showAlert(data.message, 'success');
                } else {
                    // Revert toggle
                    toggle.checked = !toggle.checked;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggle.checked = !toggle.checked;
                showAlert('An error occurred while updating property status.', 'error');
            });
        }

        // Delete property
        function deleteProperty(propertyId, propertyName) {
            if (confirm(`Are you sure you want to delete "${propertyName}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/properties/${propertyId}`;
                form.style.display = 'none';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                form.appendChild(methodField);
                form.appendChild(csrfField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Show alert messages
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            const content = document.querySelector('.content');
            content.insertBefore(alertDiv, content.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
