<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Rejected - HotelPro</title>
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
            background: linear-gradient(135deg, #a73939 0%, #9e453f 100%);
            min-height: 100vh;
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
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        .brand-logo {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .brand-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .status-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .status-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #f44336;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-rejected {
            background: #ffebee;
            color: #d32f2f;
            border: 2px solid #f44336;
        }
        
        .rejection-notice {
            background: #ffebee;
            border: 2px solid #f44336;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .rejection-title {
            font-size: 18px;
            font-weight: 600;
            color: #d32f2f;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .rejection-reason {
            background: white;
            padding: 15px;
            border-radius: 8px;
            color: #333;
            line-height: 1.6;
            font-style: italic;
            border-left: 4px solid #f44336;
        }
        
        .help-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .help-title {
            font-size: 16px;
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .help-steps {
            list-style: none;
            counter-reset: step-counter;
        }
        
        .help-steps li {
            counter-increment: step-counter;
            margin-bottom: 12px;
            padding-left: 35px;
            position: relative;
            color: #333;
        }
        
        .help-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #1565c0;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .contact-info {
            background: #fff3e0;
            border: 2px solid #ff9800;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
        }
        
        .contact-title {
            font-weight: 600;
            color: #ef6c00;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .contact-details {
            color: #333;
            line-height: 1.8;
        }
        
        .contact-method {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #ff9800;
        }
        
        .contact-method strong {
            color: #ef6c00;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay-pattern"></div>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="brand-logo">
                <i class="fas fa-hotel"></i> HotelPro
            </div>
            <div class="brand-subtitle">Management System</div>
        </div>
        
        <!-- Main Status Card -->
        <div class="main-card">
            <div class="card-header">
                <div class="status-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="status-title">Account Registration Rejected</div>
                <div class="status-subtitle">Your business registration has been declined</div>
            </div>
            
            <div class="card-body">
                <!-- Business Information -->
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-building"></i>
                        Business Information
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Business Name</div>
                            <div class="info-value">{{ $tenant->name }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Business Type</div>
                            <div class="info-value">{{ ucfirst(strtolower($tenant->business_type)) }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Contact Email</div>
                            <div class="info-value">{{ $tenant->contact_email }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">{{ $tenant->contact_phone }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Registration Date</div>
                            <div class="info-value">{{ $tenant->created_at->format('M d, Y') }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Current Status</div>
                            <div class="info-value">
                                <span class="status-badge status-rejected">
                                    <i class="fas fa-times-circle"></i>
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Rejection Notice -->
                <div class="rejection-notice">
                    <div class="rejection-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Rejection Details
                    </div>
                    
                    @if($rejectionNotification && $rejectionNotification->message)
                        <div class="rejection-reason">
                            {{ $rejectionNotification->message }}
                        </div>
                    @else
                        <div class="rejection-reason">
                            Your business registration has been rejected. Please contact our support team for detailed information about the rejection reasons.
                        </div>
                    @endif
                </div>
                
                <!-- Next Steps -->
                <div class="help-section">
                    <div class="help-title">
                        <i class="fas fa-lightbulb"></i>
                        Next Steps
                    </div>
                    
                    <ol class="help-steps">
                        <li>Review the rejection reasons mentioned above carefully</li>
                        <li>Contact our support team using the information provided below</li>
                        <li>Gather the necessary documentation or corrections needed</li>
                        <li>Submit a new registration application with corrected information</li>
                        <li>Wait for the verification process to complete</li>
                    </ol>
                </div>
                
                <!-- Contact Information -->
                <div class="contact-info">
                    <div class="contact-title">
                        <i class="fas fa-headset"></i> 
                        Contact Support for Assistance
                    </div>
                    
                    <div class="contact-method">
                        <strong>Email Support:</strong> support@hotelpro.com<br>
                        <small>Response time: Within 24 hours</small>
                    </div>
                    
                    <div class="contact-method">
                        <strong>Phone Support:</strong> +255 123 456 789<br>
                        <small>Available: Monday - Friday, 8:00 AM - 6:00 PM EAT</small>
                    </div>
                    
                    <div class="contact-method">
                        <strong>WhatsApp Support:</strong> +255 987 654 321<br>
                        <small>Available: Monday - Saturday, 9:00 AM - 8:00 PM EAT</small>
                    </div>
                    
                    <p style="margin-top: 15px; color: #666; font-size: 14px;">
                        <strong>Please mention your business name when contacting support for faster assistance.</strong>
                    </p>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <form action="/logout" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>