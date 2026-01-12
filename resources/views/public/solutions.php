<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solutions - MRK Hotel Management App</title>
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
    <section class="container mx-auto px-4" x-data="{ tab: 'hotels' }">
        <div class="text-center mb-12">
            <h1 class="text-sm font-bold text-secondary mb-4">Solutions Tailored for Your Business</h1>
            <p class="text-lg text-gray-600">Discover how MRK Hotel Management App solves your specific challenges</p>
        </div>
        
        <div class="flex justify-center space-x-4 mb-12">
            <button @click="tab = 'hotels'" :class="{ 'bg-primary text-white': tab === 'hotels', 'bg-gray-200 text-gray-700': tab !== 'hotels' }" class="rounded-md px-6 py-2 font-medium">Hotels & Lodges</button>
            <button @click="tab = 'restaurants'" :class="{ 'bg-primary text-white': tab === 'restaurants', 'bg-gray-200 text-gray-700': tab !== 'restaurants' }" class="rounded-md px-6 py-2 font-medium">Restaurants</button>
            <button @click="tab = 'bars'" :class="{ 'bg-primary text-white': tab === 'bars', 'bg-gray-200 text-gray-700': tab !== 'bars' }" class="rounded-md px-6 py-2 font-medium">Bars & Pubs</button>
        </div>
        
        <div x-show="tab === 'hotels'">
            <h2 class="text-xl font-bold text-secondary mb-6">Solutions for Hotels & Lodges</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-red-600">Problem</h3>
                    <p class="text-gray-700">Managing reservations across multiple channels, coordinating housekeeping, and tracking guest preferences manually is time-consuming and error-prone.</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-green-600">MRK Solution</h3>
                    <p class="text-gray-700">Centralized reservation system with real-time availability, automated housekeeping schedules, and comprehensive guest profile management—all in one platform.</p>
                </div>
            </div>
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Channel Management</h4>
                    <p class="text-sm text-gray-600">Sync bookings from all channels automatically</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Housekeeping Automation</h4>
                    <p class="text-sm text-gray-600">Smart room assignment and cleaning schedules</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Guest Intelligence</h4>
                    <p class="text-sm text-gray-600">Track preferences for personalized service</p>
                </div>
            </div>
        </div>
        
        <div x-show="tab === 'restaurants'" style="display: none;">
            <h2 class="text-xl font-bold text-secondary mb-6">Solutions for Restaurants</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-red-600">Problem</h3>
                    <p class="text-gray-700">Slow order processing, kitchen miscommunication, and inventory waste lead to lost revenue and customer dissatisfaction.</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-green-600">MRK Solution</h3>
                    <p class="text-gray-700">Integrated POS with kitchen display system, real-time inventory tracking, and automated reordering to streamline operations and reduce waste.</p>
                </div>
            </div>
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Fast Order Entry</h4>
                    <p class="text-sm text-gray-600">Quick POS interface for rapid service</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Kitchen Integration</h4>
                    <p class="text-sm text-gray-600">Direct communication with kitchen staff</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Smart Inventory</h4>
                    <p class="text-sm text-gray-600">Track ingredients and reduce waste</p>
                </div>
            </div>
        </div>
        
        <div x-show="tab === 'bars'" style="display: none;">
            <h2 class="text-xl font-bold text-secondary mb-6">Solutions for Bars & Pubs</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-red-600">Problem</h3>
                    <p class="text-gray-700">Managing tabs, tracking beverage inventory, and analyzing sales patterns manually is inefficient and leads to stock discrepancies.</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h3 class="text-base font-semibold mb-3 text-green-600">MRK Solution</h3>
                    <p class="text-gray-700">Digital tab management, automated inventory tracking with alerts, and detailed sales analytics to optimize stock and maximize profits.</p>
                </div>
            </div>
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Tab Management</h4>
                    <p class="text-sm text-gray-600">Easy open, track, and close customer tabs</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Beverage Control</h4>
                    <p class="text-sm text-gray-600">Monitor stock levels with automated alerts</p>
                </div>
                <div class="bg-white rounded-md p-6 shadow-sm border">
                    <h4 class="font-semibold mb-2">Sales Analytics</h4>
                    <p class="text-sm text-gray-600">Identify top sellers and optimize menu</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-16">
            <h2 class="text-xl font-bold text-secondary mb-4">Ready to Transform Your Business?</h2>
            <p class="text-lg text-gray-600 mb-6">See how MRK can solve your specific challenges</p>
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


