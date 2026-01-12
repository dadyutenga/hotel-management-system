<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$hotelId = $_GET['id'] ?? null;

if (!$hotelId) {
    header('Location: hotels.php');
    exit;
}

// Fetch hotel details
$stmt = $conn->prepare("
    SELECT t.*, 
           COUNT(DISTINCT u.user_id) as staff_count
    FROM tenants t
    LEFT JOIN users u ON t.tenant_id = u.tenant_id
    WHERE t.tenant_id = :tenant_id AND t.status = 'active'
    GROUP BY t.tenant_id
");
$stmt->execute(['tenant_id' => $hotelId]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    header('Location: hotels.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['hotel_name']); ?> - MRK Hotels</title>
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
                    }
                },
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <main class="pt-20 pb-12">
        <div class="container mx-auto px-4">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="hotels.php" class="inline-flex items-center text-primary hover:text-blue-700 font-semibold">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Hotels
                </a>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Hotel Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Hero Image -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl h-96 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="h-32 w-32 mx-auto mb-4 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-3xl font-bold"><?php echo htmlspecialchars($hotel['hotel_name']); ?></p>
                        </div>
                    </div>

                    <!-- About -->
                    <div class="bg-white rounded-xl shadow-lg border p-6">
                        <h2 class="text-2xl font-bold text-secondary mb-4">About <?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>
                        <p class="text-gray-700 leading-relaxed mb-4">
                            Welcome to <?php echo htmlspecialchars($hotel['hotel_name']); ?>, a premier hospitality destination in <?php echo htmlspecialchars($hotel['city']); ?>. 
                            We offer world-class accommodation and services to make your stay memorable.
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            Our property features modern amenities, exceptional service, and a commitment to guest satisfaction. 
                            Whether you're traveling for business or leisure, we have everything you need for a comfortable stay.
                        </p>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-xl shadow-lg border p-6">
                        <h3 class="text-xl font-bold text-secondary mb-4">Contact Information</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 mt-1 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">Address</p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($hotel['address']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($hotel['city'] . ', ' . $hotel['country']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 mt-1 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">Phone</p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($hotel['phone']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 mt-1 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">Email</p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($hotel['email']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 mt-1 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">Member Since</p>
                                    <p class="text-sm text-gray-600"><?php echo date('F Y', strtotime($hotel['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="bg-white rounded-xl shadow-lg border p-6">
                        <h3 class="text-xl font-bold text-secondary mb-4">Amenities & Services</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Free Wi-Fi</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Parking Available</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Restaurant & Bar</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">24/7 Reception</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Room Service</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Laundry Service</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Requisition Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border-2 border-primary p-6 sticky top-24">
                        <h3 class="text-xl font-bold text-secondary mb-2">Request Booking</h3>
                        <p class="text-sm text-gray-600 mb-6">Submit your booking request and we'll get back to you shortly</p>

                        <form action="submit-requisition.php" method="POST" class="space-y-4" x-data="{
                            checkIn: '',
                            checkOut: '',
                            get minCheckOut() {
                                if (!this.checkIn) return '';
                                const date = new Date(this.checkIn);
                                date.setDate(date.getDate() + 1);
                                return date.toISOString().split('T')[0];
                            },
                            validateForm(e) {
                                if (this.checkIn && this.checkOut && this.checkOut <= this.checkIn) {
                                    e.preventDefault();
                                    alert('Check-out date must be after check-in date');
                                    return false;
                                }
                                return true;
                            }
                        }" @submit="validateForm($event)">
                            <input type="hidden" name="hotel_id" value="<?php echo $hotel['tenant_id']; ?>">
                            <input type="hidden" name="hotel_name" value="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="full_name" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Phone *</label>
                                <input type="tel" name="phone" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Check-in *</label>
                                    <input type="date" name="check_in" x-model="checkIn" required min="<?php echo date('Y-m-d'); ?>" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Check-out *</label>
                                    <input type="date" name="check_out" x-model="checkOut" required :min="minCheckOut || '<?php echo date('Y-m-d', strtotime('+1 day')); ?>'" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Adults *</label>
                                    <select name="adults" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5+">5+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Children</label>
                                    <select name="children" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4+">4+</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Number of Rooms *</label>
                                <select name="rooms" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                    <option value="1">1 Room</option>
                                    <option value="2">2 Rooms</option>
                                    <option value="3">3 Rooms</option>
                                    <option value="4">4 Rooms</option>
                                    <option value="5+">5+ Rooms</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Booking Type *</label>
                                <select name="booking_type" required class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                                    <option value="leisure">Leisure/Vacation</option>
                                    <option value="business">Business Trip</option>
                                    <option value="event">Event/Conference</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="group">Group Booking</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Special Requests</label>
                                <textarea name="special_requests" rows="3" placeholder="Any special requirements or requests..." class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:shadow-xl transition">
                                Submit Request
                            </button>

                            <p class="text-xs text-gray-500 text-center">
                                We'll review your request and respond within 24 hours
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
