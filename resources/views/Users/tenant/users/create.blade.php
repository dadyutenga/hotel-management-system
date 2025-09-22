<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - HotelPro</title>
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
        
        .content {
            padding: 30px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .form-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .form-content {
            padding: 40px 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-label .required {
            color: #f44336;
            margin-left: 4px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Figtree', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            font-family: 'Figtree', sans-serif;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1a237e;
        }
        
        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
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
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e9ecef;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .password-requirements {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .password-requirements ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .content {
                padding: 20px;
            }
            
            .form-content {
                padding: 30px 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
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
                    <h1>Create User</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.users.index') }}">Users</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Create</span>
                    </div>
                </div>
            </div>
            
            <div class="content">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-container">
                    <div class="form-header">
                        <h2><i class="fas fa-user-plus"></i> Create New User</h2>
                        <p>Add a new user to your property management system</p>
                    </div>
                    
                    <form action="{{ route('tenant.users.store') }}" method="POST" class="form-content">
                        @csrf
                        
                        <div class="form-grid">
                            <!-- Username -->
                            <div class="form-group">
                                <label class="form-label" for="username">
                                    <i class="fas fa-user"></i>
                                    Username <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       class="form-control" 
                                       value="{{ old('username') }}" 
                                       required 
                                       maxlength="50"
                                       placeholder="Enter unique username">
                                @error('username')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="form-label" for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email Address <span class="required">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       class="form-control" 
                                       value="{{ old('email') }}" 
                                       required 
                                       maxlength="255"
                                       placeholder="Enter email address">
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Full Name -->
                            <div class="form-group full-width">
                                <label class="form-label" for="full_name">
                                    <i class="fas fa-id-card"></i>
                                    Full Name <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="full_name" 
                                       name="full_name" 
                                       class="form-control" 
                                       value="{{ old('full_name') }}" 
                                       required 
                                       maxlength="255"
                                       placeholder="Enter full name">
                                @error('full_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label class="form-label" for="phone">
                                    <i class="fas fa-phone"></i>
                                    Phone Number <span class="required">*</span>
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       class="form-control" 
                                       value="{{ old('phone') }}" 
                                       required 
                                       maxlength="20"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Property -->
                            <div class="form-group">
                                <label class="form-label" for="property_id">
                                    <i class="fas fa-building"></i>
                                    Property <span class="required">*</span>
                                </label>
                                <select id="property_id" name="property_id" class="form-select" required>
                                    <option value="">Select Property</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" 
                                            {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                            {{ $property->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="form-group">
                                <label class="form-label" for="role_id">
                                    <i class="fas fa-user-tag"></i>
                                    Role <span class="required">*</span>
                                </label>
                                <select id="role_id" name="role_id" class="form-select" required>
                                    <option value="">Select Role</option>
                                    @foreach($availableRoles as $role)
                                        <option value="{{ $role->id }}" 
                                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label class="form-label" for="password">
                                    <i class="fas fa-lock"></i>
                                    Password <span class="required">*</span>
                                </label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control" 
                                       required 
                                       minlength="8"
                                       placeholder="Enter password">
                                <div class="password-requirements">
                                    <strong>Password requirements:</strong>
                                    <ul>
                                        <li>At least 8 characters long</li>
                                        <li>Mix of letters and numbers recommended</li>
                                        <li>Special characters recommended</li>
                                    </ul>
                                </div>
                                @error('password')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password <span class="required">*</span>
                                </label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       class="form-control" 
                                       required 
                                       minlength="8"
                                       placeholder="Confirm password">
                            </div>

                            <!-- Active Status -->
                            <div class="form-group full-width">
                                <div class="checkbox-group">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label for="is_active" class="form-label" style="margin-bottom: 0;">
                                        <i class="fas fa-user-check"></i>
                                        User is Active
                                    </label>
                                </div>
                                <small style="color: #666; margin-left: 28px;">
                                    Active users can log in and access the system
                                </small>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>