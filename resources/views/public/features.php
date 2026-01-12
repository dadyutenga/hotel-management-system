<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - MRK Hotel Management App</title>
    <link rel="icon" type="image/png" href="../assets/header.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005eb8',
                        secondary: '#000000',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    fontSize: {
                        'xs': '0.65rem',
                        'sm': '0.75rem',
                        'base': '0.8125rem',
                        'lg': '0.9375rem',
                        'xl': '1.0625rem',
                        '2xl': '1.25rem',
                    }
                },
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="pt-20 pb-16">
    <section class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-sm font-bold text-secondary mb-4">Powerful Features for Your Hotel</h1>
            <p class="text-lg text-gray-600">Everything you need to manage your hotel efficiently</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Booking & Reservations</h2>
                <p class="text-gray-600">Streamline your reservation process with our intuitive booking system and real-time availability.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Guest Profiles</h2>
                <p class="text-gray-600">Manage comprehensive guest information and preferences for personalized service.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Billing & Payments</h2>
                <p class="text-gray-600">Centralized billing system with multiple payment methods and automatic invoicing.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Inventory Management</h2>
                <p class="text-gray-600">Track stock levels, manage suppliers, and automate procurement processes.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Restaurant POS</h2>
                <p class="text-gray-600">Integrated point-of-sale system for restaurant and bar operations.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Housekeeping</h2>
                <p class="text-gray-600">Coordinate cleaning schedules, track room status, and manage housekeeping staff.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Reports & Analytics</h2>
                <p class="text-gray-600">Comprehensive reporting with real-time analytics and business intelligence.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Role-Based Access</h2>
                <p class="text-gray-600">Secure access control with customizable roles and permissions.</p>
            </div>
            
            <div class="bg-white rounded-md p-6 shadow-sm border">
                <svg class="h-10 w-10 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="text-base font-semibold mb-2">Audit Logs</h2>
                <p class="text-gray-600">Complete audit trail of all system activities for security and compliance.</p>
            </div>
        </div>
        
        <div class="text-center mt-16">
            <h2 class="text-xl font-bold text-secondary mb-4">Ready to Get Started?</h2>
            <p class="text-lg text-gray-600 mb-6">Join hundreds of hotels already using MRK Hotel Management App</p>
            <div class="flex justify-center space-x-4">
                <a href="register.php" class="inline-block rounded-md bg-primary px-4 py-2 text-white hover:bg-blue-700 font-medium">Start Free Trial</a>
                <a href="pricing.php" class="inline-block rounded-md border border-primary px-4 py-2 text-primary hover:bg-blue-50 font-medium">View Pricing</a>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>


