<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MRK Hotel Management System - Complete Hotel Management Software</title>
    <link rel="icon" type="image/png" href="../assets/header.png">
    <meta name="description" content="Cloud-based hotel management system for East African hotels. Manage reservations, billing, housekeeping, inventory, and payments in one powerful platform.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        '3xl': '1.5rem',
                        '4xl': '1.875rem',
                        '5xl': '2.25rem',
                        '6xl': '2.75rem',
                    }
                },
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white font-sans antialiased scroll-smooth">
<?php include __DIR__ . '/../partials/header.php'; ?>

<!-- Main Content -->
<main class="overflow-hidden">
    <!-- Hero Section -->
    <section class="relative py-20 md:py-28 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="container mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-6 border border-primary/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path></svg>
                        Cloud-Based Hotel Management System
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                        Manage Your Hotel<br/>
                        <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">All in One Platform</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-8 max-w-2xl mx-auto lg:mx-0">
                        Streamline reservations, billing, housekeeping, inventory, and payments with our comprehensive cloud platform trusted by hotels across East Africa.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8">
                        <a href="register.php" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg">
                            Start Free 30-Day Trial
                            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="request-demo.php" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl border-2 border-primary text-primary bg-white">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Request a Demo
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center gap-6 justify-center lg:justify-start text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">No credit card required</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Free for 30 days</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Cancel anytime</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Hero Image -->
                <div class="relative lg:block">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary/20 via-blue-600/10 to-transparent rounded-3xl blur-2xl"></div>
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1455587734955-081b22074882?w=900&h=700&fit=crop" 
                             alt="Modern Hotel Lobby" 
                             class="rounded-2xl shadow-2xl w-full h-auto object-cover ring-1 ring-gray-200">
                        
                        <!-- Stats Overlay -->
                        <div class="absolute -bottom-3 left-6 right-6 bg-white/50 backdrop-blur-sm rounded-lg py-2 px-4 shadow-sm border border-white/60">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-base font-bold text-primary">500+</div>
                                    <div class="text-[10px] text-gray-600 font-medium">Hotels</div>
                                </div>
                                <div class="text-center border-x border-gray-200/50">
                                    <div class="text-base font-bold text-primary">99.9%</div>
                                    <div class="text-[10px] text-gray-600 font-medium">Uptime</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-base font-bold text-primary">24/7</div>
                                    <div class="text-[10px] text-gray-600 font-medium">Support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Trusted By Section -->
    <section class="py-16 bg-white border-y border-gray-100">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Trusted by Leading Hotels</p>
                <h3 class="text-2xl font-bold text-secondary">Join 500+ Hotels Worldwide</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="bg-gray-50 rounded-xl border border-gray-200 h-28 flex items-center justify-center overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=250&h=120&fit=crop" 
                         alt="Partner Hotel" 
                         class="w-full h-full object-cover opacity-70">
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 h-28 flex items-center justify-center overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=250&h=120&fit=crop" 
                         alt="Partner Hotel" 
                         class="w-full h-full object-cover opacity-70">
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 h-28 flex items-center justify-center overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=250&h=120&fit=crop" 
                         alt="Partner Hotel" 
                         class="w-full h-full object-cover opacity-70">
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 h-28 flex items-center justify-center overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=250&h=120&fit=crop" 
                         alt="Partner Hotel" 
                         class="w-full h-full object-cover opacity-70">
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview Section -->
    <section class="py-20 md:py-28 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    See It In Action
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-secondary mb-6">
                    Intuitive Dashboard for Modern Hotels
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Experience the power of our user-friendly interface designed specifically for hotel management
                </p>
            </div>
            
            <div class="relative max-w-6xl mx-auto">
                <div class="absolute -inset-1 bg-gradient-to-r from-primary via-blue-600 to-primary rounded-3xl blur-2xl opacity-20"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-gray-200 overflow-hidden">
                    <!-- Browser Header -->
                    <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-4 flex items-center gap-3">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400 shadow"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400 shadow"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400 shadow"></div>
                        </div>
                        <div class="flex-1 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="text-white text-sm font-medium">dashboard.mrkhotels.com</span>
                        </div>
                    </div>
                    <!-- Dashboard Image -->
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=1400&h=800&fit=crop" 
                             alt="MRK Hotels Dashboard Interface" 
                             class="w-full h-auto">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/20 via-transparent to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 md:py-28 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Complete Solution
                </span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-secondary mb-6">
                    Everything You Need to Run Your Hotel
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Comprehensive features designed to streamline every aspect of your hospitality operations
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&h=400&fit=crop" 
                             alt="Reservation Management" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">
                            Reservation Management
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Manage bookings, check-ins, check-outs, and room assignments with ease. Real-time availability tracking and automated confirmations.
                        </p>
                        <a href="features.php" class="inline-flex items-center text-sm font-semibold text-primary">
                            Learn more
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Feature Card 2 -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=600&h=400&fit=crop" 
                             alt="Billing & Payments" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">
                            Billing & Payments
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Seamless payment processing via ClickPesa - supporting M-Pesa, Tigo Pesa, Airtel Money, Visa, Mastercard, and more payment methods.
                        </p>
                        <a href="features.php" class="inline-flex items-center text-sm font-semibold text-primary">
                            Learn more
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Feature Card 3 -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop" 
                             alt="Inventory Management" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">
                            Inventory Management
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Track stock levels, manage procurement, get low-stock alerts, and streamline ordering. Complete visibility of your hotel inventory.
                        </p>
                        <a href="features.php" class="inline-flex items-center text-sm font-semibold text-primary">
                            Learn more
                            <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6">
                    Ready to Transform Your Hotel Operations?
                </h2>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    Join 500+ hotels already using MRK Hotel Management System. Start your free 30-day trial today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-white text-primary shadow-lg">
                        Start Free Trial Now
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="request-demo.php" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl border-2 border-white text-white">
                        Request a Demo
                    </a>
                </div>
                <p class="mt-6 text-white/80 text-sm">
                    No credit card required · Free for 30 days · Cancel anytime
                </p>
            </div>
        </div>
    </section>

    <!-- Testimonials with Images -->
    <section class="py-24 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-secondary">What Our Clients Say</h2>
                <p class="mt-4 text-base text-gray-600 max-w-2xl mx-auto">Hear from hotel managers who transformed their operations with MRK Hotels</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" 
                             alt="Hotel Manager" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">James Mwangi</div>
                            <div class="text-sm text-gray-500">Manager, Serena Hotels</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic">"MRK Hotels has revolutionized how we manage our 120-room property. The real-time dashboard and automated alerts have saved us countless hours."</p>
                    <div class="flex items-center mt-4 text-yellow-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop" 
                             alt="Hotel Owner" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">Sarah Kimani</div>
                            <div class="text-sm text-gray-500">Owner, Kilimanjaro Lodge</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic">"The billing integration with mobile money has been a game-changer. Our guests love the convenience and we've reduced payment processing time by 80%."</p>
                    <div class="flex items-center mt-4 text-yellow-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" 
                             alt="Operations Director" 
                             class="w-16 h-16 rounded-full object-cover border-4 border-primary/20">
                        <div>
                            <div class="font-bold text-gray-900">David Omondi</div>
                            <div class="text-sm text-gray-500">Director, Coastal Resorts</div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed italic">"Managing our 3 properties from one platform has never been easier. The reporting features give us insights we never had before. Highly recommended!"</p>
                    <div class="flex items-center mt-4 text-yellow-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>


