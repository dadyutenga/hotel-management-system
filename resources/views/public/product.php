<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product - MRK Hotel Management App</title>
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
</head>
<body class="bg-gray-50 font-sans antialiased">
<?php include __DIR__ . '/../partials/header.php'; ?>
<main class="pt-20 pb-16">
    <section class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-sm font-bold text-secondary mb-4">What is MRK Hotel Management App?</h1>
            <p class="text-lg text-gray-600">A comprehensive cloud-based solution for managing all aspects of your hospitality business</p>
        </div>
        
        <div class="mt-12 grid md:grid-cols-3 gap-4">
            <div class="rounded-md bg-white p-6 shadow-sm border">
                <svg class="h-12 w-12 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h2 class="text-sm font-semibold mb-3">Hotels & Lodges</h2>
                <p class="text-gray-700 mb-4">Complete property management system tailored for hotels of all sizes.</p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>• Room booking & reservations</li>
                    <li>• Housekeeping management</li>
                    <li>• Guest profile management</li>
                    <li>• Front desk operations</li>
                </ul>
            </div>
            <div class="rounded-md bg-white p-6 shadow-sm border">
                <svg class="h-12 w-12 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-sm font-semibold mb-3">Restaurants</h2>
                <p class="text-gray-700 mb-4">Streamlined restaurant operations with integrated POS and kitchen management.</p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>• Table management</li>
                    <li>• Order processing</li>
                    <li>• Menu management</li>
                    <li>• Kitchen display system</li>
                </ul>
            </div>
            <div class="rounded-md bg-white p-6 shadow-sm border">
                <svg class="h-12 w-12 text-primary mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-sm font-semibold mb-3">Bars & Pubs</h2>
                <p class="text-gray-700 mb-4">Efficient bar management with inventory tracking and sales analytics.</p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>• Beverage inventory</li>
                    <li>• Quick order entry</li>
                    <li>• Tab management</li>
                    <li>• Sales reporting</li>
                </ul>
            </div>
        </div>
        
        <div class="mt-16">
            <h2 class="text-xl font-bold text-secondary text-center mb-6">Core Features</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Booking Management</h3>
                    <p class="text-gray-600 text-sm">Manage reservations, availability, and room assignments in real-time.</p>
                </div>
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Centralized Billing</h3>
                    <p class="text-gray-600 text-sm">Unified billing system for all departments with multiple payment options.</p>
                </div>
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Guest Profiles</h3>
                    <p class="text-gray-600 text-sm">Store and manage comprehensive guest information and preferences.</p>
                </div>
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Inventory & Procurement</h3>
                    <p class="text-gray-600 text-sm">Track stock levels and automate procurement processes.</p>
                </div>
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Departmental Operations</h3>
                    <p class="text-gray-600 text-sm">Coordinate operations across all departments seamlessly.</p>
                </div>
                <div class="rounded-md bg-white p-6 shadow-sm border">
                    <h3 class="text-sm font-semibold mb-2">Reporting & Analytics</h3>
                    <p class="text-gray-600 text-sm">Comprehensive reports and real-time business intelligence.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-16">
            <h2 class="text-xl font-bold text-secondary mb-4">See It In Action</h2>
            <p class="text-lg text-gray-600 mb-6">Experience the power of MRK Hotel Management App</p>
            <div class="flex justify-center space-x-4">
                <a href="register.php" class="inline-block rounded-md bg-primary px-4 py-2 text-white hover:bg-blue-700 font-medium">Start Free Trial</a>
                <a href="features.php" class="inline-block rounded-md border border-primary px-4 py-2 text-primary hover:bg-blue-50 font-medium">Explore Features</a>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>


