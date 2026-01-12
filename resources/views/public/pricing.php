<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Calculator - MRK Hotel Management App</title>
    <link rel="icon" type="image/png" href="../assets/header.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<main class="pt-20 pb-12 relative z-0">
    <section class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h1 class="text-xl font-bold text-secondary mb-3">Custom Pricing Calculator</h1>
            <p class="text-sm text-gray-600">Start with 1 month FREE, then customize based on your hotel's needs. Pricing starts at $103/month.</p>
        </div>

        <!-- Free Trial Banner -->
        <div class="max-w-5xl mx-auto mb-8">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white text-center">
                <div class="flex items-center justify-center mb-3">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">🎉 Start with 1 Month FREE!</h2>
                <p class="text-lg opacity-90 mb-4">No credit card required. Full access to all features.</p>
                <div class="flex items-center justify-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Unlimited Users
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        All Departments
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Cancel Anytime
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pricing Calculator -->
        <div class="max-w-5xl mx-auto" x-data="{
            rooms: 10,
            departments: 1,
            basePrice: 103,
            pricePerRoom: 2,
            pricePerDepartment: 15,
            get totalPrice() {
                return this.basePrice + (this.rooms * this.pricePerRoom) + (this.departments * this.pricePerDepartment);
            },
            get annualPrice() {
                return this.totalPrice * 12;
            },
            get savings() {
                return (this.totalPrice * 12) - (this.totalPrice * 10);
            }
        }">
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Calculator Card -->
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">Configure Your Plan</h2>
                        <p class="text-xs text-gray-600">Customize based on your hotel's size and needs</p>
                    </div>

                    <!-- Number of Rooms -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Number of Rooms
                            <span class="font-normal text-gray-500">(Minimum: 1)</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <button @click="if(rooms > 1) rooms--" class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" x-model="rooms" min="1" max="1000"
                                   class="flex-1 text-center text-xl font-bold rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <button @click="rooms++" class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">$<span x-text="pricePerRoom"></span> per room/month</p>
                    </div>

                    <!-- Number of Departments -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Number of Departments
                            <span class="font-normal text-gray-500">(Reception, Kitchen, etc.)</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                                <input type="radio" x-model="departments" value="1" name="departments" class="w-4 h-4 text-primary">
                                <span class="ml-3 text-sm font-medium text-gray-700">1 Department</span>
                                <span class="ml-auto text-xs text-gray-500">+$15/mo</span>
                            </label>
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                                <input type="radio" x-model="departments" value="3" name="departments" class="w-4 h-4 text-primary">
                                <span class="ml-3 text-sm font-medium text-gray-700">3 Departments</span>
                                <span class="ml-auto text-xs text-gray-500">+$45/mo</span>
                            </label>
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                                <input type="radio" x-model="departments" value="5" name="departments" class="w-4 h-4 text-primary">
                                <span class="ml-3 text-sm font-medium text-gray-700">5 Departments</span>
                                <span class="ml-auto text-xs text-gray-500">+$75/mo</span>
                            </label>
                            <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                                <input type="radio" x-model="departments" value="7" name="departments" class="w-4 h-4 text-primary">
                                <span class="ml-3 text-sm font-medium text-gray-700">7+ Departments (Full Suite)</span>
                                <span class="ml-auto text-xs text-gray-500">+$105/mo</span>
                            </label>
                        </div>
                    </div>

                    <!-- Features Included -->
                    <div class="border-t pt-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-3">All Plans Include:</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start text-xs">
                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Unlimited users & staff accounts</span>
                            </li>
                            <li class="flex items-start text-xs">
                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Real-time reservations & billing</span>
                            </li>
                            <li class="flex items-start text-xs">
                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Cloud backup & security</span>
                            </li>
                            <li class="flex items-start text-xs">
                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">24/7 customer support</span>
                            </li>
                            <li class="flex items-start text-xs">
                                <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">Mobile access & notifications</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Pricing Summary Card -->
                <div class="bg-gradient-to-br from-primary to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold mb-2">Your Custom Quote</h2>
                        <p class="text-xs opacity-90">Pay monthly or save with annual billing</p>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm pb-2 border-b border-white/20">
                            <span class="opacity-90">Starter Package</span>
                            <span class="font-bold">$<span x-text="basePrice"></span></span>
                        </div>
                        <div class="flex justify-between text-sm pb-2 border-b border-white/20">
                            <span class="opacity-90"><span x-text="rooms"></span> Rooms × $<span x-text="pricePerRoom"></span></span>
                            <span class="font-bold">$<span x-text="rooms * pricePerRoom"></span></span>
                        </div>
                        <div class="flex justify-between text-sm pb-2 border-b border-white/20">
                            <span class="opacity-90"><span x-text="departments"></span> Departments × $<span x-text="pricePerDepartment"></span></span>
                            <span class="font-bold">$<span x-text="departments * pricePerDepartment"></span></span>
                        </div>
                    </div>

                    <!-- Monthly Total -->
                    <div class="bg-white/10 rounded-lg p-4 mb-2">
                        <div class="text-xs opacity-90 mb-1">After Free Month</div>
                        <div class="text-3xl font-bold">$<span x-text="totalPrice.toFixed(0)"></span><span class="text-lg">/mo</span></div>
                    </div>

                    <!-- Free Month Highlight -->
                    <div class="bg-green-500/20 border-2 border-green-400 rounded-lg p-3 mb-4">
                        <div class="flex items-center justify-center text-sm font-bold">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            First Month: $0 (FREE)
                        </div>
                    </div>

                    <!-- Annual Pricing -->
                    <div class="bg-white/10 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-medium">Annual Billing</span>
                            <span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">Save 2 months!</span>
                        </div>
                        <div class="text-2xl font-bold">$<span x-text="(totalPrice * 10).toFixed(0)"></span><span class="text-sm">/year</span></div>
                        <div class="text-xs opacity-75 mt-1">Normally $<span x-text="annualPrice.toFixed(0)"></span> - Save $<span x-text="savings.toFixed(0)"></span></div>
                        <div class="text-xs opacity-90 mt-2">💡 First month free applies before annual billing starts</div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        <a href="register.php" class="block text-center bg-white text-primary px-6 py-3 rounded-lg font-bold hover:shadow-lg transition-all transform hover:scale-105">
                            Start 1 Month FREE Trial
                        </a>
                        <a href="request-demo.php" class="block text-center border-2 border-white text-white px-6 py-3 rounded-lg font-bold hover:bg-white/10 transition-all">
                            Schedule a Demo
                        </a>
                        <p class="text-xs text-center opacity-75">No credit card required for free trial</p>
                    </div>
                </div>
            </div>

            <!-- Department Options Explanation -->
            <div class="mt-8 bg-blue-50 rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">What's Included in Each Department?</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">🏨 Reception</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Check-in/Check-out</li>
                            <li>• Guest Management</li>
                            <li>• Reservations</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">🧹 Housekeeping</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Task Management</li>
                            <li>• Room Inspections</li>
                            <li>• Hardware Tracking</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">🍽️ Restaurant & Bar</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• POS System</li>
                            <li>• Menu Management</li>
                            <li>• Order Tracking</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">👨‍🍳 Kitchen</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Order Dashboard</li>
                            <li>• Status Updates</li>
                            <li>• Preparation Tracking</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">📦 Store Management</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Inventory Control</li>
                            <li>• Procurement</li>
                            <li>• Supplier Management</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">💰 Billing & Finance</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Payment Processing</li>
                            <li>• Financial Reports</li>
                            <li>• Analytics</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-primary mb-2">👥 Staff Management</h4>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• Employee Profiles</li>
                            <li>• Access Control</li>
                            <li>• Audit Logs</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Starter Pack Benefits -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">💎 Why Choose MRK Hotel Management?</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Lightning Fast</h4>
                        <p class="text-xs text-gray-600">Cloud-based system ensures instant updates across all devices</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Secure & Reliable</h4>
                        <p class="text-xs text-gray-600">Bank-level encryption with automated daily backups</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 mb-2">24/7 Support</h4>
                        <p class="text-xs text-gray-600">Expert assistance whenever you need it, day or night</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Old Static Plans (kept as reference)
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Up to 15 users</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Up to 5 outlets</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">All modules included</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Priority email & chat</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">50GB storage</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Advanced analytics</span>
                    </li>
                </ul>
                <a href="register.php" class="block text-center text-sm rounded-md bg-primary px-4 py-2 text-white hover:bg-blue-700 font-medium">Get Started</a>
            </div>
            
            <!-- Enterprise Plan -->
            <div class="bg-white rounded-md border p-4 hover:shadow-md transition-shadow">
                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-secondary mb-1">Enterprise</h2>
                    <p class="text-xs text-gray-600">For large organizations</p>
                </div>
                <div class="mb-6">
                    <span class="text-xl font-bold text-secondary">Custom</span>
                </div>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Unlimited users</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Unlimited outlets</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Custom integrations</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">24/7 phone support</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Unlimited storage</span>
                    </li>
                    <li class="flex items-start text-sm">
                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-xs">Dedicated account manager</span>
                    </li>
                </ul>
                <a href="#" class="block text-center text-sm rounded-md bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200 font-medium">Contact Sales</a>
            </div>
        </div>
        
        <!-- Features Comparison -->
        <div class="mt-16 max-w-5xl mx-auto">
            <h2 class="text-sm font-bold text-secondary text-center mb-6">Compare Plans</h2>
            <div class="bg-white rounded-md border overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Feature</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Starter</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Professional</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr>
                            <td class="px-4 py-3 text-sm">Booking & Reservations</td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm">Guest Management</td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm">Billing & Payments</td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm">Inventory Management</td>
                            <td class="px-4 py-3 text-center text-gray-300"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm">Restaurant/Bar POS</td>
                            <td class="px-4 py-3 text-center text-gray-300"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm">Advanced Analytics</td>
                            <td class="px-4 py-3 text-center text-gray-300"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="px-4 py-3 text-center text-green-500"><svg class="h-5 w-5 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="mt-16 max-w-3xl mx-auto">
            <h2 class="text-sm font-bold text-secondary text-center mb-6">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-md border p-4">
                    <h3 class="text-sm font-semibold text-secondary mb-2">Are there any setup fees?</h3>
                    <p class="text-xs text-gray-600">No, there are no setup fees. You only pay the monthly subscription based on your chosen plan.</p>
                </div>
                <div class="bg-white rounded-md border p-4">
                    <h3 class="text-sm font-semibold text-secondary mb-2">Can I change plans later?</h3>
                    <p class="text-xs text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes will be reflected in your next billing cycle.</p>
                </div>
                <div class="bg-white rounded-md border p-4">
                    <h3 class="text-sm font-semibold text-secondary mb-2">Do you charge transaction fees?</h3>
                    <p class="text-xs text-gray-600">No, we don't charge any transaction fees. You keep 100% of your revenue and can use your preferred payment gateway.</p>
                </div>
                <div class="bg-white rounded-md border p-4">
                    <h3 class="text-sm font-semibold text-secondary mb-2">Is there a free trial?</h3>
                    <p class="text-xs text-gray-600">Yes, all plans come with a 14-day free trial. No credit card required to start.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <p class="text-xs text-gray-600 mb-4">Need a custom solution? Contact our sales team for enterprise pricing.</p>
            <a href="register.php" class="inline-block text-sm rounded-md bg-primary px-6 py-2 text-white hover:bg-blue-700 font-medium">Start Your Free Trial</a>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>


