<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch all active registered hotels
$stmt = $conn->prepare("
    SELECT t.tenant_id, t.hotel_name, t.email, t.phone, 
           t.address, t.city, t.country, t.created_at,
           COUNT(DISTINCT u.user_id) as staff_count
    FROM tenants t
    LEFT JOIN users u ON t.tenant_id = u.tenant_id
    WHERE t.status = 'active'
    GROUP BY t.tenant_id
    ORDER BY t.hotel_name ASC
");
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Hotels - MRK Hotels</title>
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
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-blue-700 text-white py-16 mb-12">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Discover Our Partner Hotels</h1>
                <p class="text-lg opacity-90 max-w-2xl mx-auto">Browse through our collection of verified hotels and submit your booking request</p>
            </div>
        </section>

        <div class="container mx-auto px-4" x-data='{ 
            search: "", 
            location: "all",
            hotels: <?php echo json_encode($hotels, JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
            get filteredHotels() {
                return this.hotels.filter(hotel => {
                    const matchesSearch = this.search === "" || 
                        hotel.hotel_name.toLowerCase().includes(this.search.toLowerCase()) ||
                        (hotel.city && hotel.city.toLowerCase().includes(this.search.toLowerCase()));
                    const matchesLocation = this.location === "all" || 
                        hotel.city === this.location;
                    return matchesSearch && matchesLocation;
                });
            }
        }'>
            <!-- Search & Filter -->
            <div class="bg-white rounded-xl shadow-lg border p-6 mb-8">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search Hotels</label>
                        <input type="text" x-model="search" placeholder="Hotel name or city..." class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                        <select x-model="location" class="w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 transition">
                            <option value="all">All Locations</option>
                            <option value="Dar es Salaam">Dar es Salaam</option>
                            <option value="Arusha">Arusha</option>
                            <option value="Zanzibar">Zanzibar</option>
                            <option value="Mwanza">Mwanza</option>
                            <option value="Dodoma">Dodoma</option>
                            <option value="Mbeya">Mbeya</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="search = search.trim()" class="w-full bg-gradient-to-r from-primary to-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:shadow-xl transition">
                            <span x-text="'Search (' + filteredHotels.length + ')'" x-show="filteredHotels.length !== hotels.length"></span>
                            <span x-show="filteredHotels.length === hotels.length">Search Hotels</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hotels Grid -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-secondary mb-2">Available Hotels</h2>
                <p class="text-sm text-gray-600">Found <span x-text="filteredHotels.length"></span> registered properties</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="hotel in filteredHotels" :key="hotel.tenant_id">
                <div class="bg-white rounded-xl shadow-lg border-2 border-gray-100 overflow-hidden hover:shadow-2xl hover:border-primary/30 transition">
                    <!-- Hotel Image Placeholder -->
                    <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="h-16 w-16 mx-auto mb-2 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-sm font-semibold" x-text="hotel.hotel_name.charAt(0).toUpperCase()"></p>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-secondary mb-2" x-text="hotel.hotel_name"></h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-start text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-2 mt-0.5 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="hotel.city + ', ' + hotel.country"></span>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span x-text="hotel.phone"></span>
                            </div>

                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="hotel.email"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="text-xs text-gray-500">
                                <span>Member since </span><span x-text="new Date(hotel.created_at).getFullYear()"></span>
                            </div>
                            <a :href="'hotel-details.php?id=' + hotel.tenant_id" class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow hover:shadow-lg">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                </template>
            </div>

            <!-- No Results Message -->
            <div x-show="filteredHotels.length === 0" class="text-center py-16">
                <svg class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Hotels Found</h3>
                <p class="text-gray-600">Try adjusting your search filters</p>
                <button @click="search = ''; location = 'all'" class="mt-4 bg-primary text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    Clear Filters
                </button>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
