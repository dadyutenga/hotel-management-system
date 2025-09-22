<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $property->name }} - HotelPro</title>
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
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #666;
            margin-top: 5px;
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
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
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
            font-size: 14px;
        }
        
        .form-content {
            padding: 40px 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .required {
            color: #f44336;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }
        
        .form-input.error {
            border-color: #f44336;
        }
        
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .form-check label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }
        
        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 30px;
            border-top: 1px solid #f0f0f0;
        }
        
        .btn {
            padding: 12px 25px;
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
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .input-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .property-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #1a237e;
        }
        
        .property-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a237e;
            margin-bottom: 5px;
        }
        
        .property-created {
            font-size: 14px;
            color: #666;
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
                <div>
                    <h1 class="header-title">Edit Property</h1>
                    <div class="breadcrumb">
                        <a href="{{ route('tenant.properties.index') }}">Properties</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="{{ route('tenant.properties.show', $property->id) }}">{{ $property->name }}</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Edit</span>
                    </div>
                </div>
            </div>
            
            <div class="content">
                <!-- Property Info -->
                <div class="property-info">
                    <div class="property-name">{{ $property->name }}</div>
                    <div class="property-created">
                        Created on {{ $property->created_at->format('F j, Y') }} 
                        • Last updated {{ $property->updated_at->diffForHumans() }}
                    </div>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Please correct the following errors:</strong>
                            <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.properties.update', $property->id) }}" id="propertyForm">
                    @csrf
                    @method('PUT')
                    <div class="form-container">
                        <div class="form-header">
                            <h2><i class="fas fa-edit"></i> Edit Property</h2>
                            <p>Update property information and settings</p>
                        </div>
                        
                        <div class="form-content">
                            <!-- Basic Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Basic Information
                                </h3>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="name" class="form-label">
                                            Property Name <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               class="form-input {{ $errors->has('name') ? 'error' : '' }}" 
                                               value="{{ old('name', $property->name) }}" 
                                               placeholder="Enter property name"
                                               required>
                                        @if($errors->has('name'))
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('name') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="timezone" class="form-label">
                                            Timezone <span class="required">*</span>
                                        </label>
                                        <select id="timezone" 
                                                name="timezone" 
                                                class="form-select {{ $errors->has('timezone') ? 'error' : '' }}" 
                                                required>
                                            <option value="">Select timezone</option>
                                            @foreach($timezones as $value => $label)
                                                <option value="{{ $value }}" {{ old('timezone', $property->timezone) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('timezone'))
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('timezone') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="address" class="form-label">
                                        Address <span class="required">*</span>
                                    </label>
                                    <textarea id="address" 
                                              name="address" 
                                              class="form-input form-textarea {{ $errors->has('address') ? 'error' : '' }}" 
                                              placeholder="Enter complete property address"
                                              required>{{ old('address', $property->address) }}</textarea>
                                    @if($errors->has('address'))
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $errors->first('address') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-phone"></i>
                                    Contact Information
                                </h3>
                                
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="contact_phone" class="form-label">
                                            Contact Phone <span class="required">*</span>
                                        </label>
                                        <input type="tel" 
                                               id="contact_phone" 
                                               name="contact_phone" 
                                               class="form-input {{ $errors->has('contact_phone') ? 'error' : '' }}" 
                                               value="{{ old('contact_phone', $property->contact_phone) }}" 
                                               placeholder="+255 123 456 789"
                                               required>
                                        @if($errors->has('contact_phone'))
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('contact_phone') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            Email Address <span class="required">*</span>
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               class="form-input {{ $errors->has('email') ? 'error' : '' }}" 
                                               value="{{ old('email', $property->email) }}" 
                                               placeholder="property@example.com"
                                               required>
                                        @if($errors->has('email'))
                                            <div class="error-message">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="website" class="form-label">
                                        Website URL
                                    </label>
                                    <input type="url" 
                                           id="website" 
                                           name="website" 
                                           class="form-input {{ $errors->has('website') ? 'error' : '' }}" 
                                           value="{{ old('website', $property->website) }}" 
                                           placeholder="https://www.example.com">
                                    <div class="input-help">Optional: Property website URL</div>
                                    @if($errors->has('website'))
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $errors->first('website') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-toggle-on"></i>
                                    Property Status
                                </h3>
                                
                                <div class="form-check">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $property->is_active) ? 'checked' : '' }}>
                                    <label for="is_active">
                                        Property is active
                                    </label>
                                </div>
                                <div class="input-help">Active properties can accept reservations and manage operations</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <a href="{{ route('tenant.properties.show', $property->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                                
                                @if(Auth::user()->role->name === 'DIRECTOR')
                                    <button type="button" class="btn btn-danger" onclick="deleteProperty()">
                                        <i class="fas fa-trash"></i>
                                        Delete Property
                                    </button>
                                @endif
                                
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    Update Property
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Delete Form (Hidden) -->
                <form method="POST" action="{{ route('tenant.properties.destroy', $property->id) }}" id="deleteForm" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('propertyForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Reset previous errors
                document.querySelectorAll('.form-input.error').forEach(input => {
                    input.classList.remove('error');
                });
                
                document.querySelectorAll('.error-message').forEach(msg => {
                    if (!msg.querySelector('.fas')) {
                        msg.remove();
                    }
                });
                
                // Validate required fields
                const requiredFields = [
                    { id: 'name', message: 'Property name is required' },
                    { id: 'address', message: 'Address is required' },
                    { id: 'contact_phone', message: 'Contact phone is required' },
                    { id: 'email', message: 'Email address is required' },
                    { id: 'timezone', message: 'Timezone is required' }
                ];
                
                requiredFields.forEach(field => {
                    const input = document.getElementById(field.id);
                    if (!input.value.trim()) {
                        showFieldError(input, field.message);
                        isValid = false;
                    }
                });
                
                // Validate email format
                const emailInput = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailInput.value && !emailRegex.test(emailInput.value)) {
                    showFieldError(emailInput, 'Please enter a valid email address');
                    isValid = false;
                }
                
                // Validate website URL format if provided
                const websiteInput = document.getElementById('website');
                if (websiteInput.value) {
                    try {
                        new URL(websiteInput.value);
                    } catch {
                        showFieldError(websiteInput, 'Please enter a valid website URL');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.form-input.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                } else {
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Property...';
                    submitBtn.disabled = true;
                }
            });
            
            function showFieldError(input, message) {
                input.classList.add('error');
                
                // Remove existing error message
                const existingError = input.parentNode.querySelector('.error-message:not(:has(.fas))');
                if (existingError) {
                    existingError.remove();
                }
                
                // Add new error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
                input.parentNode.appendChild(errorDiv);
            }
            
            // Real-time validation
            document.getElementById('email').addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    showFieldError(this, 'Please enter a valid email address');
                } else {
                    this.classList.remove('error');
                    const errorMsg = this.parentNode.querySelector('.error-message:not(:has(.fas))');
                    if (errorMsg) errorMsg.remove();
                }
            });
            
            document.getElementById('website').addEventListener('blur', function() {
                if (this.value) {
                    try {
                        new URL(this.value);
                        this.classList.remove('error');
                        const errorMsg = this.parentNode.querySelector('.error-message:not(:has(.fas))');
                        if (errorMsg) errorMsg.remove();
                    } catch {
                        showFieldError(this, 'Please enter a valid website URL');
                    }
                }
            });
            
            // Phone number formatting
            document.getElementById('contact_phone').addEventListener('input', function() {
                // Allow only numbers, spaces, +, -, (, )
                this.value = this.value.replace(/[^0-9\s\+\-\(\)]/g, '');
            });
        });

        // Delete property function
        function deleteProperty() {
            if (confirm('Are you sure you want to delete this property? This action cannot be undone and will remove all associated data including buildings, rooms, and reservations.')) {
                if (confirm('This is a permanent action. Are you absolutely sure?')) {
                    document.getElementById('deleteForm').submit();
                }
            }
        }

        // Show success/error messages
        @if(session('success'))
            showAlert('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showAlert('{{ session('error') }}', 'error');
        @endif

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
    </script>
</body>
</html>