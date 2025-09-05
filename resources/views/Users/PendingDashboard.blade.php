<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification Pending - HotelPro</title>
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
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
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
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
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
            border-left: 4px solid #1a237e;
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
        
        .status-pending {
            background: #fff3e0;
            color: #f57c00;
            border: 2px solid #ffb74d;
        }
        
        .verification-steps {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .steps-title {
            font-size: 16px;
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .steps-list {
            list-style: none;
            counter-reset: step-counter;
        }
        
        .steps-list li {
            counter-increment: step-counter;
            margin-bottom: 12px;
            padding-left: 35px;
            position: relative;
            color: #333;
        }
        
        .steps-list li::before {
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
            transform: translateY(-2px);
        }
        
        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .contact-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .contact-details {
            color: #666;
            line-height: 1.6;
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
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="status-title">Account Verification Pending</div>
                <div class="status-subtitle">Your business registration is under review</div>
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
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i>
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Verification Process -->
                <div class="verification-steps">
                    <div class="steps-title">
                        <i class="fas fa-list-check"></i>
                        Verification Process
                    </div>
                    
                    <ol class="steps-list">
                        <li>Our team reviews your submitted business documents</li>
                        <li>We verify your business registration and tax certificates</li>
                        <li>Your contact information is validated</li>
                        <li>Account approval notification is sent to your email</li>
                        <li>Access to full system features is granted</li>
                    </ol>
                </div>
                
                <!-- Contact Information -->
                <div class="contact-info">
                    <div class="contact-title">
                        <i class="fas fa-headset"></i> Need Help?
                    </div>
                    <div class="contact-details">
                        If you have any questions about your verification status, please contact our support team:<br>
                        <strong>Email:</strong> support@hotelpro.com<br>
                        <strong>Phone:</strong> +255 123 456 789
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <form action="/logout" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    
                    <a href="/dashboard/pending" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> Refresh Status
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds to check for status updates
        setInterval(function() {
            // Only refresh if still on pending status
            if (window.location.pathname === '/dashboard/pending') {
                window.location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
