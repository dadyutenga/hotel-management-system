<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Registration - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            min-height: 100vh;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }
        
        .overlay-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 10;
        }
        
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }
        
        .brand-logo {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .brand-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        
        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 8px;
        }
        
        .step.active .step-number {
            background: #f44336;
            color: white;
        }
        
        .step.completed .step-number {
            background: #4caf50;
            color: white;
        }
        
        .step-title {
            font-weight: 500;
            color: #666;
        }
        
        .step.active .step-title {
            color: #f44336;
        }
        
        .form-section {
            display: none;
            margin-bottom: 30px;
        }
        
        .form-section.active {
            display: block;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            width: 100%;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #f44336;
            box-shadow: 0 0 0 3px rgba(244, 67, 54, 0.1);
            outline: none;
        }
        
        .form-control.is-invalid {
            border-color: #f44336;
        }
        
        .invalid-feedback {
            color: #f44336;
            font-weight: 500;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .file-upload {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload:hover {
            border-color: #f44336;
            background-color: #fafafa;
        }
        
        .file-upload input {
            display: none;
        }
        
        .file-upload-icon {
            font-size: 24px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .file-upload-text {
            color: #666;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .form-navigation {
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }
        
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-home a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-to-home a:hover {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .password-requirements {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .step-indicator {
                flex-direction: column;
                align-items: center;
            }
            
            .step {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="overlay-pattern"></div>
    
    <div class="container">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fas fa-hotel"></i> HotelPro
            </div>
            <div class="brand-subtitle">Business Registration</div>
        </div>
        
        <!-- Registration Form -->
        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">Register Your Business</h1>
            </div>
            
            <div class="form-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-title">Business Details</div>
                    </div>
                    <div class="step" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-title">Documents</div>
                    </div>
                    <div class="step" id="step3-indicator">
                        <div class="step-number">3</div>
                        <div class="step-title">Admin Account</div>
                    </div>
                </div>

                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="/register" enctype="multipart/form-data" id="registration-form">
                    @csrf
                    
                    <!-- Step 1: Business Details -->
                    <div class="form-section active" id="step1">
                        <h3 class="section-title">Business Information</h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="business_name" class="form-label">Business Name *</label>
                                <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                       id="business_name" name="business_name" value="{{ old('business_name') }}" required>
                                @error('business_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="business_type" class="form-label">Business Type *</label>
                                <select class="form-control @error('business_type') is-invalid @enderror" 
                                        id="business_type" name="business_type" required>
                                    <option value="">Select Business Type</option>
                                    <option value="HOTEL" {{ old('business_type') == 'HOTEL' ? 'selected' : '' }}>Hotel</option>
                                    <option value="LODGE" {{ old('business_type') == 'LODGE' ? 'selected' : '' }}>Lodge</option>
                                    <option value="RESTAURANT" {{ old('business_type') == 'RESTAURANT' ? 'selected' : '' }}>Restaurant</option>
                                    <option value="BAR" {{ old('business_type') == 'BAR' ? 'selected' : '' }}>Bar</option>
                                    <option value="PUB" {{ old('business_type') == 'PUB' ? 'selected' : '' }}>Pub</option>
                                </select>
                                @error('business_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="business_address" class="form-label">Business Address *</label>
                                <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                          id="business_address" name="business_address" rows="3" required>{{ old('business_address') }}</textarea>
                                @error('business_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="country" class="form-label">Country *</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country', 'Tanzania') }}" required>
                                @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="business_email" class="form-label">Official Email *</label>
                                <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                                       id="business_email" name="business_email" value="{{ old('business_email') }}" required>
                                @error('business_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="business_phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control @error('business_phone') is-invalid @enderror" 
                                       id="business_phone" name="business_phone" value="{{ old('business_phone') }}" required>
                                @error('business_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="certification_type" class="form-label">Tax Registration Type *</label>
                                <select class="form-control @error('certification_type') is-invalid @enderror" 
                                        id="certification_type" name="certification_type" required>
                                    <option value="">Select Type</option>
                                    <option value="TIN" {{ old('certification_type') == 'TIN' ? 'selected' : '' }}>TIN</option>
                                    <option value="VAT" {{ old('certification_type') == 'VAT' ? 'selected' : '' }}>VAT</option>
                                    <option value="BRELA" {{ old('certification_type') == 'BRELA' ? 'selected' : '' }}>BRELA</option>
                                </select>
                                @error('certification_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="tin_vat_number" class="form-label">TIN/VAT Number *</label>
                                <input type="text" class="form-control @error('tin_vat_number') is-invalid @enderror" 
                                       id="tin_vat_number" name="tin_vat_number" value="{{ old('tin_vat_number') }}" required>
                                @error('tin_vat_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Documents -->
                    <div class="form-section" id="step2">
                        <h3 class="section-title">Required Documents</h3>
                        <p style="color: #666; margin-bottom: 20px;">Please upload the following documents (PDF, JPG, PNG - Max 5MB each):</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Business License *</label>
                                <div class="file-upload" onclick="document.getElementById('business_license').click()">
                                    <div class="file-upload-icon"><i class="fas fa-file-upload"></i></div>
                                    <div class="file-upload-text">Click to upload business license</div>
                                    <input type="file" id="business_license" name="business_license" 
                                           accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                @error('business_license')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Tax Certificate *</label>
                                <div class="file-upload" onclick="document.getElementById('tax_certificate').click()">
                                    <div class="file-upload-icon"><i class="fas fa-file-upload"></i></div>
                                    <div class="file-upload-text">Click to upload tax certificate</div>
                                    <input type="file" id="tax_certificate" name="tax_certificate" 
                                           accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                @error('tax_certificate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Owner ID Document *</label>
                                <div class="file-upload" onclick="document.getElementById('owner_id').click()">
                                    <div class="file-upload-icon"><i class="fas fa-file-upload"></i></div>
                                    <div class="file-upload-text">Click to upload owner ID</div>
                                    <input type="file" id="owner_id" name="owner_id" 
                                           accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                @error('owner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Registration Certificate *</label>
                                <div class="file-upload" onclick="document.getElementById('registration_certificate').click()">
                                    <div class="file-upload-icon"><i class="fas fa-file-upload"></i></div>
                                    <div class="file-upload-text">Click to upload registration certificate</div>
                                    <input type="file" id="registration_certificate" name="registration_certificate" 
                                           accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                                @error('registration_certificate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Admin Account -->
                    <div class="form-section" id="step3">
                        <h3 class="section-title">Admin Account Setup</h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="admin_username" class="form-label">Username *</label>
                                <input type="text" class="form-control @error('admin_username') is-invalid @enderror" 
                                       id="admin_username" name="admin_username" value="{{ old('admin_username') }}" required>
                                @error('admin_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                       id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                                @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="admin_full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('admin_full_name') is-invalid @enderror" 
                                       id="admin_full_name" name="admin_full_name" value="{{ old('admin_full_name') }}" required>
                                @error('admin_full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('admin_phone') is-invalid @enderror" 
                                       id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}">
                                @error('admin_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                       id="admin_password" name="admin_password" required>
                                <div class="password-requirements">
                                    Password must contain:
                                    <ul>
                                        <li>At least 8 characters</li>
                                        <li>Uppercase and lowercase letters</li>
                                        <li>Numbers and symbols</li>
                                    </ul>
                                </div>
                                @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="admin_password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control @error('admin_password_confirmation') is-invalid @enderror" 
                                       id="admin_password_confirmation" name="admin_password_confirmation" required>
                                @error('admin_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group full-width">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="mfa_enabled" value="1" {{ old('mfa_enabled') ? 'checked' : '' }}>
                                    Enable Two-Factor Authentication (Recommended)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary" id="prev-btn" onclick="previousStep()" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="next-btn" onclick="nextStep()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn" style="display: none;">
                            <i class="fas fa-check"></i> Submit Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Back to Home -->
        <div class="back-to-home">
            <a href="/">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>

    <script>
        // Initialize step based on validation errors
        let currentStep = 1;
        const totalSteps = 3;
        
        // Check for validation errors and set appropriate step
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we have validation errors
            const hasErrors = document.querySelectorAll('.invalid-feedback').length > 0;
            
            if (hasErrors) {
                // Determine which step has errors
                const step1Errors = document.querySelectorAll('#step1 .invalid-feedback').length > 0;
                const step2Errors = document.querySelectorAll('#step2 .invalid-feedback').length > 0;
                const step3Errors = document.querySelectorAll('#step3 .invalid-feedback').length > 0;
                
                if (step3Errors) {
                    currentStep = 3;
                } else if (step2Errors) {
                    currentStep = 2;
                } else {
                    currentStep = 1;
                }
                
                // Apply the correct step
                showStep(currentStep);
            }
        });

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show current step
            document.getElementById(`step${step}`).classList.add('active');
            
            // Update step indicators
            document.querySelectorAll('.step').forEach((stepEl, index) => {
                stepEl.classList.remove('active', 'completed');
                if (index + 1 < step) {
                    stepEl.classList.add('completed');
                } else if (index + 1 === step) {
                    stepEl.classList.add('active');
                }
            });
            
            // Update navigation buttons
            document.getElementById('prev-btn').style.display = step === 1 ? 'none' : 'inline-block';
            document.getElementById('next-btn').style.display = step === totalSteps ? 'none' : 'inline-block';
            document.getElementById('submit-btn').style.display = step === totalSteps ? 'inline-block' : 'none';
        }

        function nextStep() {
            // Basic validation before moving to next step
            const currentFields = document.querySelectorAll(`#step${currentStep} input[required], #step${currentStep} select[required], #step${currentStep} textarea[required]`);
            let isValid = true;
            
            currentFields.forEach(field => {
                if (!field.value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                return; // Don't proceed if validation fails
            }
            
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                // Scroll to top of form for better UX
                document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                // Scroll to top of form for better UX
                document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
            }
        }

        // File upload feedback
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const uploadDiv = this.parentElement;
                const textDiv = uploadDiv.querySelector('.file-upload-text');
                if (this.files.length > 0) {
                    textDiv.textContent = this.files[0].name;
                    uploadDiv.style.borderColor = '#4caf50';
                    uploadDiv.style.backgroundColor = '#f8fff8';
                }
            });
        });
        
        // Add field validation on blur for better UX
        document.querySelectorAll('.form-control').forEach(field => {
            field.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
</body>
</html>
