<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Business - MRK Hotel Management App</title>
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
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen flex items-center justify-center px-4 py-8" 
      x-data="{ 
          currentStep: 1, 
          totalSteps: 3, 
          businessType: '', 
          formData: {} 
      }">
    <div class="w-full max-w-5xl">
        <!-- Back to Home -->
        <div class="mb-6">
            <a href="index.php" class="inline-flex items-center text-xs text-gray-600 hover:text-primary transition">
                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary to-blue-600 p-8 text-white">
                <h1 class="text-2xl font-bold">Create Your MRK Hotels Account</h1>
                <p class="mt-2 text-sm opacity-90">Join thousands of hospitality businesses managing their operations efficiently</p>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-50 px-8 py-5 border-b">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-700" x-text="'Step ' + currentStep + ' of ' + totalSteps"></span>
                    <span class="text-xs text-gray-600" x-text="Math.round((currentStep / totalSteps) * 100) + '% Complete'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-primary to-blue-500 h-2.5 rounded-full transition-all duration-500" :style="'width: ' + ((currentStep / totalSteps) * 100) + '%'"></div>
                </div>
                <div class="flex justify-between mt-4 text-xs">
                    <div class="flex items-center" :class="currentStep >= 1 ? 'text-primary font-semibold' : 'text-gray-400'">
                        <span class="h-7 w-7 rounded-full flex items-center justify-center mr-2 font-bold" 
                              :class="currentStep >= 1 ? 'bg-primary text-white' : 'bg-gray-200'">
                            <span x-show="currentStep > 1">✓</span>
                            <span x-show="currentStep === 1">1</span>
                            <span x-show="currentStep < 1">1</span>
                        </span>
                        <span class="hidden sm:inline">Business Details</span>
                    </div>
                    <div class="flex items-center" :class="currentStep >= 2 ? 'text-primary font-semibold' : 'text-gray-400'">
                        <span class="h-7 w-7 rounded-full flex items-center justify-center mr-2 font-bold" 
                              :class="currentStep >= 2 ? 'bg-primary text-white' : 'bg-gray-200'">
                            <span x-show="currentStep > 2">✓</span>
                            <span x-show="currentStep === 2">2</span>
                            <span x-show="currentStep < 2">2</span>
                        </span>
                        <span class="hidden sm:inline">Configuration</span>
                    </div>
                    <div class="flex items-center" :class="currentStep >= 3 ? 'text-primary font-semibold' : 'text-gray-400'">
                        <span class="h-7 w-7 rounded-full flex items-center justify-center mr-2 font-bold" 
                              :class="currentStep >= 3 ? 'bg-primary text-white' : 'bg-gray-200'">3</span>
                        <span class="hidden sm:inline">Review & Submit</span>
                    </div>
                </div>
            </div>

            <form action="/MRK%20Hotel/auth/register-process.php" method="POST" enctype="multipart/form-data" class="p-8">
                <!-- Step 1: Business Details -->
                <div x-show="currentStep === 1" x-transition>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-secondary">Business Information</h2>
                            <p class="text-xs text-gray-600">Tell us about your hospitality business</p>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="business_name" class="block text-xs font-semibold text-gray-700 mb-2">Business Name <span class="text-red-500">*</span></label>
                            <input type="text" id="business_name" name="business_name" required
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                   placeholder="e.g., Serena Hotel">
                        </div>
                        <div>
                            <label for="business_type" class="block text-xs font-semibold text-gray-700 mb-2">Business Type <span class="text-red-500">*</span></label>
                            <select id="business_type" name="business_type" x-model="businessType" required
                                    class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                <option value="">Select business type</option>
                                <option value="hotel">Hotel & Lodge</option>
                                <option value="restaurant">Restaurant</option>
                                <option value="bar">Bar & Pub</option>
                                <option value="combined">Hotel with Restaurant/Bar</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="business_address" class="block text-xs font-semibold text-gray-700 mb-2">Business Address <span class="text-red-500">*</span></label>
                        <textarea id="business_address" name="business_address" rows="3" required
                                  class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                  placeholder="Street address, City, Region, Country"></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="tin_number" class="block text-xs font-semibold text-gray-700 mb-2">TIN/VAT Number</label>
                            <input type="text" id="tin_number" name="tin_number"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                   placeholder="123-456-789">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-2">Business Phone <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                   placeholder="+255 687 413 290">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-700 mb-2">Business Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" required
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                   placeholder="info@yourbusiness.com">
                        </div>
                        <div>
                            <label for="website" class="block text-xs font-semibold text-gray-700 mb-2">Website (Optional)</label>
                            <input type="url" id="website" name="website"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"
                                   placeholder="https://www.yourbusiness.com">
                        </div>
                    </div>

                    <!-- Amenities (Show only for hotels) -->
                    <div x-show="businessType === 'hotel' || businessType === 'combined'" x-transition>
                        <h3 class="text-sm font-bold text-secondary mb-4 mt-6 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            Property Amenities
                        </h3>
                        <div class="grid md:grid-cols-3 gap-4 mb-6">
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="restaurant" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Restaurant</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="bar" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Bar/Lounge</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="pool" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Swimming Pool</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="gym" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Gym/Fitness Center</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="conference" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Conference Rooms</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="amenities[]" value="spa" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Spa/Wellness Center</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Configuration & Documents -->
                <div x-show="currentStep === 2" x-transition style="display: none;">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-secondary">Property Configuration</h2>
                            <p class="text-xs text-gray-600">Set up your property details and upload verification documents</p>
                        </div>
                    </div>

                    <!-- Room Configuration (Hotels Only) -->
                    <div x-show="businessType === 'hotel' || businessType === 'combined'" x-transition>
                        <h3 class="text-sm font-bold text-secondary mb-4">Room Configuration</h3>
                        <div class="grid md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="total_rooms" class="block text-xs font-semibold text-gray-700 mb-2">Total Rooms <span class="text-red-500">*</span></label>
                                <input type="number" id="total_rooms" name="total_rooms" min="1"
                                       class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            </div>
                            <div>
                                <label for="total_floors" class="block text-xs font-semibold text-gray-700 mb-2">Number of Floors</label>
                                <input type="number" id="total_floors" name="total_floors" min="1"
                                       class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            </div>
                            <div>
                                <label for="room_categories" class="block text-xs font-semibold text-gray-700 mb-2">Room Categories</label>
                                <select id="room_categories" name="room_categories[]" multiple
                                        class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                    <option value="standard">Standard</option>
                                    <option value="deluxe">Deluxe</option>
                                    <option value="executive">Executive</option>
                                    <option value="suite">Suite</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Check-in/Check-out Times -->
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="checkin_time" class="block text-xs font-semibold text-gray-700 mb-2">Check-in Time</label>
                            <input type="time" id="checkin_time" name="checkin_time" value="14:00"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                        <div>
                            <label for="checkout_time" class="block text-xs font-semibold text-gray-700 mb-2">Check-out Time</label>
                            <input type="time" id="checkout_time" name="checkout_time" value="11:00"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-700 mb-3">Accepted Payment Methods <span class="text-red-500">*</span></label>
                        <div class="grid md:grid-cols-4 gap-4">
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="payments[]" value="cash" class="h-5 w-5 text-primary rounded border-gray-300" checked>
                                <span class="text-sm font-medium text-gray-700">Cash</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="payments[]" value="mobile_money" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Mobile Money</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="payments[]" value="bank_transfer" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Bank Transfer</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition">
                                <input type="checkbox" name="payments[]" value="credit_card" class="h-5 w-5 text-primary rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-700">Credit/Debit Card</span>
                            </label>
                        </div>
                    </div>

                    <!-- Document Uploads -->
                    <h3 class="text-sm font-bold text-secondary mb-4 mt-8">Verification Documents</h3>
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="business_license" class="block text-xs font-semibold text-gray-700 mb-2">Business License <span class="text-red-500">*</span></label>
                            <input type="file" id="business_license" name="business_license" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-sm rounded-lg border-2 border-dashed border-gray-300 px-4 py-6 focus:border-primary transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-700">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                        <div>
                            <label for="owner_id" class="block text-xs font-semibold text-gray-700 mb-2">Owner's ID Document <span class="text-red-500">*</span></label>
                            <input type="file" id="owner_id" name="owner_id" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-sm rounded-lg border-2 border-dashed border-gray-300 px-4 py-6 focus:border-primary transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-700">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="mb-6">
                        <label for="business_logo" class="block text-xs font-semibold text-gray-700 mb-2">Business Logo (Optional)</label>
                        <input type="file" id="business_logo" name="business_logo" accept=".jpg,.jpeg,.png,.svg"
                               class="w-full text-sm rounded-lg border-2 border-dashed border-gray-300 px-4 py-6 focus:border-primary transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-blue-700">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, SVG (Max 2MB, Square format recommended)</p>
                    </div>
                </div>

                <!-- Step 3: Review & Submit -->
                <div x-show="currentStep === 3" x-transition style="display: none;">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-secondary">Review & Submit</h2>
                            <p class="text-xs text-gray-600">Please review your information before submitting</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl border-2 border-blue-100 p-6 mb-6">
                        <h3 class="text-sm font-bold text-secondary mb-4">📋 Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start">
                                <span class="font-semibold text-gray-700 w-40">Business Name:</span>
                                <span class="text-gray-600">Will be displayed from form</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-semibold text-gray-700 w-40">Business Type:</span>
                                <span class="text-gray-600">Will be displayed from form</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-semibold text-gray-700 w-40">Email:</span>
                                <span class="text-gray-600">Will be displayed from form</span>
                            </div>
                            <div class="flex items-start">
                                <span class="font-semibold text-gray-700 w-40">Phone:</span>
                                <span class="text-gray-600">Will be displayed from form</span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Credentials -->
                    <h3 class="text-sm font-bold text-secondary mb-4">Account Credentials</h3>
                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" required minlength="8"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-xs font-semibold text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                                   class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox" id="terms" name="terms" required class="mt-1 h-5 w-5 text-primary rounded border-gray-300">
                            <span class="text-sm text-gray-700">
                                I agree to the <a href="/legal/terms.php" class="text-primary hover:underline font-semibold">Terms of Service</a> and 
                                <a href="/legal/privacy.php" class="text-primary hover:underline font-semibold">Privacy Policy</a>. 
                                I confirm that all information provided is accurate and I have the authority to register this business.
                            </span>
                        </label>
                    </div>

                    <!-- CAPTCHA Placeholder -->
                    <div class="bg-gray-100 rounded-xl p-6 mb-6 text-center">
                        <p class="text-sm text-gray-600">CAPTCHA verification would appear here</p>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t">
                    <button type="button" @click="currentStep > 1 ? currentStep-- : null" 
                            :class="currentStep === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                            :disabled="currentStep === 1"
                            class="px-6 py-3 text-sm font-semibold text-gray-700 rounded-lg border-2 border-gray-300 transition">
                        <span class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </span>
                    </button>

                    <button type="button" @click="currentStep < totalSteps ? currentStep++ : null" 
                            x-show="currentStep < totalSteps"
                            class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg transition">
                        <span class="flex items-center">
                            Next Step
                            <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </button>

                    <button type="submit" x-show="currentStep === totalSteps" style="display: none;"
                            class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 rounded-lg hover:shadow-lg transition">
                        <span class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Complete Registration
                        </span>
                    </button>
                </div>
            </form>

            <!-- Already Have Account -->
            <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-600 border-t">
                Already have an account? 
                <a href="login.php" class="text-primary font-semibold hover:underline">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
