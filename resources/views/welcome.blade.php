<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to HotelPro - Hotel Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .brand {
            margin-bottom: 30px;
        }
        
        .brand-logo {
            font-size: 48px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .brand-logo i {
            font-size: 42px;
        }
        
        .brand-subtitle {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .hero-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero-description {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(244, 67, 54, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-3px);
        }
        
        /* Features Section */
        .features {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            font-size: 32px;
            font-weight: 700;
            color: #1a237e;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .feature-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            transition: all 0.3s ease;
            border-top: 5px solid #1a237e;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: rgba(26, 35, 126, 0.1);
            color: #1a237e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 25px;
        }
        
        .feature-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1a237e;
        }
        
        .feature-description {
            color: #666;
            font-size: 16px;
        }
        
        /* Footer */
        .footer {
            background: #1a237e;
            color: rgba(255, 255, 255, 0.8);
            padding: 40px 20px;
            text-align: center;
        }
        
        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .footer-links {
            margin-bottom: 20px;
        }
        
        .footer-link {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            transition: opacity 0.3s ease;
        }
        
        .footer-link:hover {
            opacity: 0.8;
        }
        
        .copyright {
            font-size: 14px;
            opacity: 0.7;
        }
        
        .admin-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .admin-link:hover {
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero {
                padding: 60px 20px;
            }
            
            .hero-title {
                font-size: 28px;
            }
            
            .brand-logo {
                font-size: 36px;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-pattern"></div>
        <div class="hero-content">
            <div class="brand">
                <div class="brand-logo">
                    <i class="fas fa-hotel"></i> HotelPro
                </div>
                <div class="brand-subtitle">Hotel Management System</div>
            </div>
            
            <h1 class="hero-title">Streamline Your Hotel Operations</h1>
            <p class="hero-description">
                HotelPro provides a comprehensive solution for managing your hotel properties, 
                reservations, staff, and more - all in one powerful platform.
            </p>
            
            <div class="cta-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i> Register Your Business
                </a>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features">
        <h2 class="section-title">Why Choose HotelPro?</h2>
        
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="feature-title">Property Management</h3>
                <p class="feature-description">
                    Efficiently manage multiple properties, track room status, and optimize occupancy rates with our powerful management tools.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Reservation System</h3>
                <p class="feature-description">
                    Handle bookings seamlessly with our intuitive reservation system. Track availability and manage guest information all in one place.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Reporting & Analytics</h3>
                <p class="feature-description">
                    Make data-driven decisions with comprehensive reports and analytics on occupancy rates, revenue, and other key metrics.
                </p>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="#" class="footer-link">About Us</a>
                <a href="#" class="footer-link">Features</a>
                <a href="#" class="footer-link">Contact</a>
                <a href="#" class="footer-link">Support</a>
            </div>
            
            <p class="copyright">&copy; {{ date('Y') }} HotelPro. All rights reserved.</p>
            
            <a href="{{ route('superadmin.login') }}" class="admin-link">
                <i class="fas fa-user-shield"></i> System Administration
            </a>
        </div>
    </footer>
</body>
</html>
