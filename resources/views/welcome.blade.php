<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Hotel Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Default styles */
                body {
                    font-family: 'Figtree', sans-serif;
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                }
                .hero-section {
                    background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
                    color: white;
                    padding: 80px 0;
                    position: relative;
                    overflow: hidden;
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
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 20px;
                }
                .navbar {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px 0;
                }
                .logo {
                    font-size: 24px;
                    font-weight: 600;
                    color: #fff;
                }
                .nav-buttons {
                    display: flex;
                    gap: 15px;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    border-radius: 5px;
                    font-weight: 500;
                    text-align: center;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                .btn-primary {
                    background-color: #f44336;
                    color: white;
                }
                .btn-primary:hover {
                    background-color: #e53935;
                    transform: translateY(-2px);
                }
                .btn-secondary {
                    background-color: rgba(255, 255, 255, 0.2);
                    color: white;
                }
                .btn-secondary:hover {
                    background-color: rgba(255, 255, 255, 0.3);
                    transform: translateY(-2px);
                }
                .hero-content {
                    max-width: 600px;
                    margin: 50px 0;
                }
                .hero-title {
                    font-size: 48px;
                    font-weight: 700;
                    margin-bottom: 20px;
                    line-height: 1.2;
                }
                .hero-subtitle {
                    font-size: 18px;
                    margin-bottom: 30px;
                    opacity: 0.9;
                }
                .features {
                    padding: 80px 0;
                }
                .section-title {
                    text-align: center;
                    margin-bottom: 60px;
                    font-size: 36px;
                    font-weight: 600;
                    color: #333;
                }
                .features-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 30px;
                }
                .feature-card {
                    background-color: #fff;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }
                .feature-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                }
                .feature-icon {
                    font-size: 36px;
                    margin-bottom: 20px;
                    color: #1a237e;
                }
                .feature-title {
                    font-size: 20px;
                    font-weight: 600;
                    margin-bottom: 15px;
                }
                .cta-section {
                    background-color: #f5f5f5;
                    padding: 80px 0;
                    text-align: center;
                }
                .cta-title {
                    font-size: 36px;
                    font-weight: 600;
                    margin-bottom: 20px;
                }
                .cta-subtitle {
                    font-size: 18px;
                    max-width: 600px;
                    margin: 0 auto 30px;
                    opacity: 0.8;
                }
                .footer {
                    background-color: #333;
                    color: #fff;
                    padding: 20px 0;
                    text-align: center;
                }
                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .hero-title {
                        font-size: 36px;
                    }
                    .section-title {
                        font-size: 30px;
                    }
                    .feature-card {
                        padding: 20px;
                    }
                }
                @media (max-width: 480px) {
                    .navbar {
                        flex-direction: column;
                        gap: 20px;
                    }
                    .hero-title {
                        font-size: 30px;
                    }
                }
            </style>
        @endif
    </head>
    <body>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="overlay-pattern"></div>
            <div class="container">
                <div class="navbar">
                    <div class="logo">
                        <i class="fas fa-hotel"></i> HotelPro
                    </div>
                    @if (Route::has('login'))
                    <div class="nav-buttons">
                        <a href="{{ route('superadmin.login') }}" class="btn btn-secondary">
                            <i class="fas fa-user-shield"></i> Admin Login
                        </a>
                        <a href="{{ url('/register') }}" class="btn btn-primary">
                            <i class="fas fa-building"></i> Register Hotel
                        </a>
                    </div>
                    @endif
                </div>
                
                <div class="hero-content">
                    <h1 class="hero-title">Modern Hotel Management Solution</h1>
                    <p class="hero-subtitle">
                        Streamline your operations, boost efficiency, and enhance guest experience with our 
                        comprehensive hotel management platform. Perfect for hotels, resorts, and lodges of all sizes.
                    </p>
                    <a href="{{ url('/register') }}" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <h2 class="section-title">Powerful Features</h2>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Reservation Management</h3>
                        <p>
                            Effortlessly manage bookings, check-ins, and check-outs with our intuitive reservation system.
                            Avoid double bookings and maximize room availability.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Guest Management</h3>
                        <p>
                            Build guest profiles, track preferences, and deliver personalized experiences
                            that keep your customers coming back.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Revenue Analytics</h3>
                        <p>
                            Gain valuable insights into your business performance with detailed reports
                            and dashboards that help you make data-driven decisions.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <h3 class="feature-title">Billing & Invoicing</h3>
                        <p>
                            Simplify payment collection with automated billing, multiple payment methods,
                            and customizable invoices.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h3 class="feature-title">Inventory Management</h3>
                        <p>
                            Keep track of all hotel supplies and assets with our comprehensive inventory
                            system. Set alerts for low stock items.
                        </p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Mobile Friendly</h3>
                        <p>
                            Access your hotel management system from anywhere, on any device.
                            Stay connected with your business on the go.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2 class="cta-title">Ready to transform your hotel management?</h2>
                <p class="cta-subtitle">
                    Join thousands of successful businesses who have improved their operations with our system.
                    No credit card required to get started.
                </p>
                <a href="{{ url('/register') }}" class="btn btn-primary">
                    <i class="fas fa-sign-up"></i> Register Your Property Now
                </a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <p>
                    &copy; {{ date('Y') }} HotelPro. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>
