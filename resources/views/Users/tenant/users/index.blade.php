<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HotelPro</title>
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
        
        .header-left h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .breadcrumb a {
            color: #1a237e;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .content {
            padding: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border-left: 5px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.primary {
            border-left-color: #1a237e;
        }
        
        .stat-card.success {
            border-left-color: #4caf50;
        }
        
        .stat-card.warning {
            border-left-color: #ff9800;
        }
        
        .stat-card.info {
            border-left-color: #2196f3;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .stat-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .stat-icon.primary {
            background: rgba(26, 35, 126, 0.1);
            color: #1a237e;
        }
        
        .stat-icon.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }
        
        .stat-icon.warning {
            background: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }
        
        .stat-icon.info {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
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
        
        .table-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        .table-content {
            padding: 25px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
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
        
        .badge-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-danger {
            background: #ffebee;
            color: #c62828;
        }
        
        .badge-info {
            background: #e3f2fd;
            color: #1976d2;
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
        
        .filters {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .filter-select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Figtree', sans-serif;
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
        
        @media (max-width: 768px) {
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        @include('Users.shared.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>User Management</h1>
                    <div class="breadcrumb">
                        <span>Dashboard</span>
                        <i class="fas fa-chevron-right"></i>
                        <span>Users</span>
                    </div>
                </div>
                <div class="header-actions">
                    @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                        <a href="{{ route('tenant.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add User
                        </a>
                    @endif
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

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div class="stat-title">Total Users</div>
                            <div class="stat-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $users->total() }}</div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-title">Active Users</div>
                            <div class="stat-icon success">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $users->where('is_active', true)->count() }}</div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-title">Properties</div>
                            <div class="stat-icon warning">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                        <div class="stat-value">{{ $properties->count() }}</div>
                    </div>
                </div>

                <!-- Filters -->
                @if(Auth::user()->role->name === 'DIRECTOR' && $properties->count() > 0)
                    <div class="filters">
                        <form method="GET" action="{{ route('tenant.users.index') }}">
                            <div class="filter-row">
                                <div class="filter-group">
                                    <label class="filter-label">Filter by Property</label>
                                    <select name="property_id" class="filter-select">
                                        <option value="">All Properties</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" 
                                                {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label class="filter-label">Filter by Role</label>
                                    <select name="role_id" class="filter-select">
                                        <option value="">All Roles</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Users Table -->
                <div class="data-table">
                    <div class="table-header">
                        <h2>
                            <i class="fas fa-users"></i>
                            Users ({{ $users->total() }})
                        </h2>
                    </div>
                    
                    <div class="table-content">
                        @if($users->count() > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Property</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $user->full_name }}</strong>
                                                    <div style="font-size: 12px; color: #666;">
                                                        @{{ $user->username }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $user->role->name }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $user->property ? $user->property->name : 'No Property' }}
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']) && $user->id !== Auth::id())
                                                        <label class="toggle-switch">
                                                            <input type="checkbox" 
                                                                   {{ $user->is_active ? 'checked' : '' }}
                                                                   onchange="toggleUserStatus('{{ $user->id }}', this)">
                                                            <span class="slider"></span>
                                                        </label>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('tenant.users.show', $user->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                    View
                                                </a>
                                                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                                    <a href="{{ route('tenant.users.edit', $user->id) }}" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                        Edit
                                                    </a>
                                                @endif
                                                @if(Auth::user()->role->name === 'DIRECTOR' && $user->id !== Auth::id())
                                                    <button class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->id }}', '{{ $user->full_name }}')">
                                                        <i class="fas fa-trash"></i>
                                                        Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div style="margin-top: 20px;">
                                {{ $users->links() }}
                            </div>
                        @else
                            <div style="text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-users" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                                <h3>No Users Found</h3>
                                <p>No users have been created yet.</p>
                                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                                    <div style="margin-top: 20px;">
                                        <a href="{{ route('tenant.users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i>
                                            Add First User
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle user status
        function toggleUserStatus(userId, toggle) {
            fetch(`/users/${userId}/toggle-status`, {
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
                    // Update status badge
                    const row = toggle.closest('tr');
                    const statusBadge = row.querySelector('.badge');
                    
                    if (data.is_active) {
                        statusBadge.className = 'badge badge-success';
                        statusBadge.textContent = 'Active';
                    } else {
                        statusBadge.className = 'badge badge-danger';
                        statusBadge.textContent = 'Inactive';
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
                showAlert('An error occurred while updating user status.', 'error');
            });
        }

        // Delete user
        function deleteUser(userId, userName) {
            if (confirm(`Are you sure you want to delete "${userName}"?\n\nThis action cannot be undone.`)) {
                fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Reload page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while deleting the user.', 'error');
                });
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