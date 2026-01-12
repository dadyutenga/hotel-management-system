<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay - MRK Hotels</title>
    <link rel="icon" type="image/png" href="../assets/header.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    <?php 
    require_once __DIR__ . '/../config/database.php';
    include __DIR__ . '/../partials/header.php'; 
    
    // Fetch active hotels from database
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("
        SELECT tenant_id, hotel_name, city, email, phone 
        FROM tenants 
        WHERE status = 'active' 
        ORDER BY hotel_name ASC
    ");
    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <main class="pt-20 pb-12">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-blue-700 text-white py-16 mb-12">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Stay</h1>
                <p class="text-lg opacity-90 max-w-2xl mx-auto">Book directly with our partner hotels across Tanzania. Best rates guaranteed.</p>
            </div>
        </section>

        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8" x-data="{ 
                checkIn: '', 
                checkOut: '', 
                guests: 2,
                selectedHotel: null,
                step: 1,
                selectedRoom: null,
                guestDetails: {
                    fullName: '',
                    email: '',
                    phone: '',
                    idType: 'passport',
                    idNumber: '',
                    specialRequests: ''
                },
                paymentMethod: 'mobile_money'
            }">
                <!-- Search & Filter Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border-2 p-6 sticky top-4">
                        <h2 class="text-lg font-bold text-secondary mb-6">Search Hotels</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Check-in Date</label>
                                <input type="date" x-model="checkIn" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Check-out Date</label>
                                <input type="date" x-model="checkOut" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" :min="checkIn">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Number of Guests</label>
                                <select x-model="guests" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5+ Guests</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Location</label>
                                <select class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                    <option>All Locations</option>
                                    <option>Dar es Salaam</option>
                                    <option>Arusha</option>
                                    <option>Zanzibar</option>
                                    <option>Mwanza</option>
                                    <option>Dodoma</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Property Type</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" checked>
                                        <span class="ml-2 text-sm text-gray-700">Hotels</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="ml-2 text-sm text-gray-700">Lodges</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="ml-2 text-sm text-gray-700">Resorts</span>
                                    </label>
                                </div>
                            </div>
                            
                            <button class="w-full bg-gradient-to-r from-primary to-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:shadow-xl transition">
                                Search Available Rooms
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results & Booking Panel -->
                <div class="lg:col-span-2">
                    <!-- Step 1: Hotel & Room Selection -->
                    <div x-show="step === 1">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-secondary mb-2">Available Hotels</h2>
                            <p class="text-sm text-gray-600">Found <?php echo count($hotels); ?> properties for your dates</p>
                        </div>
                        
                        <div class="space-y-6">
                            <?php 
                            $hotelImages = [
                                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&h=400&fit=crop',
                                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&h=400&fit=crop',
                                'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&h=400&fit=crop',
                                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop',
                                'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&h=400&fit=crop',
                                'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=600&h=400&fit=crop',
                            ];
                            
                            foreach ($hotels as $index => $hotel): 
                                $rating = 4.5 + (rand(0, 8) / 10);
                                $reviews = rand(150, 350);
                                $basePrice = rand(120000, 280000);
                                $image = $hotelImages[$index % count($hotelImages)];
                            ?>
                                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-100 overflow-hidden hover:shadow-2xl hover:border-primary/30 transition">
                                    <div class="md:flex">
                                        <div class="md:w-1/3">
                                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" class="w-full h-64 md:h-full object-cover">
                                        </div>
                                        <div class="p-6 md:w-2/3">
                                            <div class="flex items-start justify-between mb-3">
                                                <div>
                                                    <h3 class="text-xl font-bold text-secondary"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h3>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <svg class="h-4 w-4 inline text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <?php echo htmlspecialchars($hotel['city']); ?>
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                        <span class="font-bold text-gray-900"><?php echo number_format($rating, 1); ?></span>
                                                        <span class="text-xs text-gray-500">(<?php echo $reviews; ?>)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                <span class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">Free WiFi</span>
                                                <span class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-full font-semibold">Free Breakfast</span>
                                                <span class="text-xs px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-semibold">Swimming Pool</span>
                                                <span class="text-xs px-3 py-1 bg-orange-100 text-orange-700 rounded-full font-semibold">Restaurant</span>
                                            </div>
                                            
                                            <p class="text-sm text-gray-600 mb-4">Luxurious accommodations with stunning views. Perfect for both business and leisure travelers.</p>
                                            
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs text-gray-500">Starting from</div>
                                                    <div class="text-2xl font-bold text-primary">$<?php echo number_format($basePrice, 2); ?></div>
                                                    <div class="text-xs text-gray-500">per night</div>
                                                </div>
                                                <a href="hotel-details.php?id=<?php echo $hotel['tenant_id']; ?>" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg hover:shadow-xl transition">
                                                    View Rooms
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 2: Guest Details -->
                    <div x-show="step === 2" style="display: none;">
                        <div class="mb-6 flex items-center space-x-4">
                            <button @click="step = 1" class="text-primary hover:underline font-semibold">
                                ← Back to hotels
                            </button>
                            <h2 class="text-2xl font-bold text-secondary">Guest Information</h2>
                        </div>
                        
                        <div class="bg-white rounded-xl shadow-lg border-2 p-8">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" x-model="guestDetails.fullName" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="John Doe">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Email Address *</label>
                                    <input type="email" x-model="guestDetails.email" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="john@example.com">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Phone Number *</label>
                                    <input type="tel" x-model="guestDetails.phone" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="+255 XXX XXX XXX">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">ID Type *</label>
                                    <select x-model="guestDetails.idType" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                        <option value="passport">Passport</option>
                                        <option value="national_id">National ID</option>
                                        <option value="drivers_license">Driver's License</option>
                                    </select>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">ID Number *</label>
                                    <input type="text" x-model="guestDetails.idNumber" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="Enter your ID number">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Special Requests (Optional)</label>
                                    <textarea x-model="guestDetails.specialRequests" rows="3" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="Early check-in, extra pillows, etc."></textarea>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex justify-end space-x-4">
                                <button @click="step = 1" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                                    Back
                                </button>
                                <button @click="step = 3" class="px-6 py-3 bg-primary text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg hover:shadow-xl transition">
                                    Continue to Payment
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Payment via ClickPesa -->
                    <div x-show="step === 3" style="display: none;">
                        <div class="mb-6 flex items-center space-x-4">
                            <button @click="step = 2" class="text-primary hover:underline font-semibold">
                                ← Back to guest details
                            </button>
                            <h2 class="text-2xl font-bold text-secondary">Payment via ClickPesa</h2>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-lg border-2 p-8">
                                <h3 class="text-lg font-bold text-secondary mb-6">Booking Summary</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Hotel:</span>
                                        <span class="font-semibold" x-text="selectedHotel"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Check-in:</span>
                                        <span class="font-semibold" x-text="checkIn"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Check-out:</span>
                                        <span class="font-semibold" x-text="checkOut"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Guests:</span>
                                        <span class="font-semibold" x-text="guests"></span>
                                    </div>
                                    <div class="border-t pt-4">
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-gray-600">Room Rate (2 nights):</span>
                                            <span class="font-semibold">TZS 360,000</span>
                                        </div>
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-gray-600">Service Fee:</span>
                                            <span class="font-semibold">TZS 18,000</span>
                                        </div>
                                        <div class="flex justify-between text-lg font-bold text-primary">
                                            <span>Total Amount:</span>
                                            <span>TZS 378,000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-lg border-2 p-8">
                                <div class="text-center mb-6">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 60'%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='24' font-weight='bold' fill='%23005eb8'%3EClickPesa%3C/text%3E%3C/svg%3E" alt="ClickPesa" class="h-12 mx-auto mb-2">
                                    <p class="text-xs text-gray-600">Secure payment powered by ClickPesa</p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Payment Method</label>
                                        <div class="space-y-2">
                                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer" :class="paymentMethod === 'mobile_money' ? 'border-primary bg-primary/5' : 'border-gray-200'">
                                                <input type="radio" x-model="paymentMethod" value="mobile_money" class="text-primary focus:ring-primary">
                                                <span class="ml-3 text-sm font-semibold">Mobile Money (M-Pesa, Tigo Pesa, Airtel Money)</span>
                                            </label>
                                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer" :class="paymentMethod === 'card' ? 'border-primary bg-primary/5' : 'border-gray-200'">
                                                <input type="radio" x-model="paymentMethod" value="card" class="text-primary focus:ring-primary">
                                                <span class="ml-3 text-sm font-semibold">Debit/Credit Card (Visa, Mastercard)</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div x-show="paymentMethod === 'mobile_money'">
                                        <label class="block text-xs font-semibold text-gray-700 mb-2">Mobile Number</label>
                                        <input type="tel" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition" placeholder="+255 XXX XXX XXX">
                                        <p class="text-xs text-gray-500 mt-2">You will receive a payment prompt on your phone</p>
                                    </div>
                                    
                                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mt-6">
                                        <div class="flex items-start space-x-3">
                                            <svg class="h-5 w-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="text-xs text-blue-800">
                                                <p class="font-semibold mb-1">Secure Payment</p>
                                                <p>Your payment is processed securely through ClickPesa. You will receive confirmation via SMS and email.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition mt-6">
                                        Complete Booking (TZS 378,000)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
