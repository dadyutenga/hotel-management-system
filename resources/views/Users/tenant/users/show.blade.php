<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->full_name }} - User Details - HotelPro</title>
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
        
        .user-overview {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .user-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .user-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .user-username {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .user-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-active {
            background: rgba(76, 175, 80, 0.2);
            color: #2e7d32;
            border: 2px solid rgba(76, 175, 80, 0.5);
        }
        
        .status-inactive {
            background: rgba(244, 67, 54, 0.2);
            color: #c62828;
            border: 2px solid rgba(244, 67, 54, 0.5);
        }
        
        .user-details {
            padding: 30px;
            background: #f8f9fa;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .detail-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a237e;
            color: white;
            font-size: 20px;
        }
        
        .detail-info h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .detail-info p {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-content {
            padding: 25px;
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
        
        .badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
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
        
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .stat-row:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        
        .stat-value {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
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
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
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
                    <h1>User Details</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.users.index') }}">Users</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>{{ $user->username }}</span>
                    </div>
                </div>
                <div class="header-actions">
                    @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                        <a href="{{ route('tenant.users.edit', $user->id) }}" class="btn btn-secondary">
                            <i class="fas fa-edit"></i>
                            Edit User
                        </a>
                    @endif
                    <a href="{{ route('tenant.users.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Users
                    </a>
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

                <!-- User Overview -->
                <div class="user-overview">
                    <div class="user-header">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-name">{{ $user->full_name }}</div>
                        <div class="user-username">@{{ $user->username }}</div>
                        <div style="margin-top: 15px;">
                            <span class="user-status {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="user-details">
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Email</h4>
                                    <p>{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Phone</h4>
                                    <p>{{ $user->phone }}</p>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Role</h4>
                                    <p><span class="badge badge-info">{{ $user->role->name }}</span></p>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Property</h4>
                                    <p>{{ $user->property ? $user->property->name : 'No Property Assigned' }}</p>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Member Since</h4>
                                    <p>{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="detail-info">
                                    <h4>Last Login</h4>
                                    <p>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="info-card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        User Information
                    </div>
                    
                    <div class="card-content">
                        <div class="stat-row">
                            <div class="stat-label">User ID</div>
                            <div class="stat-value">{{ $user->id }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Username</div>
                            <div class="stat-value">{{ $user->username }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Full Name</div>
                            <div class="stat-value">{{ $user->full_name }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Email Address</div>
                            <div class="stat-value">{{ $user->email }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Phone Number</div>
                            <div class="stat-value">{{ $user->phone }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Role</div>
                            <div class="stat-value">
                                <span class="badge badge-info">{{ $user->role->name }}</span>
                            </div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Property</div>
                            <div class="stat-value">
                                {{ $user->property ? $user->property->name : 'No Property Assigned' }}
                            </div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Account Status</div>
                            <div class="stat-value">
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-warning' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Multi-Factor Authentication</div>
                            <div class="stat-value">
                                <span class="badge {{ $user->mfa_enabled ? 'badge-success' : 'badge-warning' }}">
                                    {{ $user->mfa_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Created</div>
                            <div class="stat-value">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Last Updated</div>
                            <div class="stat-value">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                        
                        <div class="stat-row">
                            <div class="stat-label">Last Login</div>
                            <div class="stat-value">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t g:i A') : 'Never logged in' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if(in_array(Auth::user()->role->name, ['DIRECTOR', 'MANAGER']))
                    <div class="info-card">
                        <div class="card-header">
                            <i class="fas fa-cogs"></i>
                            Actions
                        </div>
                        
                        <div class="card-content">
                            <div class="action-buttons">
                                <a href="{{ route('tenant.users.edit', $user->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i>
                                    Edit User
                                </a>
                                
                                @if($user->id !== Auth::id())
                                    <button class="btn btn-primary" onclick="toggleUserStatus('{{ $user->id }}')">
                                        <i class="fas fa-{{ $user->is_active ? 'user-slash' : 'user-check' }}"></i>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                                    </button>
                                @endif
                                
                                @if(Auth::user()->role->name === 'DIRECTOR' && $user->id !== Auth::id())
                                    <button class="btn btn-danger" onclick="deleteUser('{{ $user->id }}', '{{ $user->full_name }}')">
                                        <i class="fas fa-trash"></i>
                                        Delete User
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Toggle user status
        function toggleUserStatus(userId) {
            const user = @json($user);
            const action = user.is_active ? 'deactivate' : 'activate';
            
            if (confirm(`Are you sure you want to ${action} this user?`)) {
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
                        showAlert(data.message, 'success');
                        // Reload page to update status
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while updating user status.', 'error');
                });
            }
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
                        // Redirect to users list
                        setTimeout(() => {
                            window.location.href = '{{ route("tenant.users.index") }}';
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