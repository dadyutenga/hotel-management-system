<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Request Submitted - MRK Hotels</title>
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
</head>
<body class="bg-gray-50 font-sans antialiased">
    <?php include __DIR__ . '/../partials/header.php'; ?>
    
    <main class="pt-20 pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg border p-8 text-center">
                    <!-- Success Icon -->
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-secondary mb-4">Request Submitted Successfully!</h1>
                    
                    <p class="text-gray-700 mb-6">
                        Thank you for your booking request at <strong><?php echo htmlspecialchars($_GET['hotel'] ?? 'our hotel'); ?></strong>.
                    </p>

                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-6">
                        <p class="text-sm text-gray-600 mb-2">Your Reference Number</p>
                        <p class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($_GET['ref'] ?? 'N/A'); ?></p>
                    </div>

                    <div class="text-left bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="font-bold text-secondary mb-3">What happens next?</h3>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>The hotel will review your booking request</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>You'll receive a confirmation email within 24 hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>The hotel may contact you for additional details</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Once confirmed, you'll receive booking details and payment instructions</span>
                            </li>
                        </ul>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="hotels.php" class="bg-primary text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:shadow-xl transition">
                            Browse More Hotels
                        </a>
                        <a href="index.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-bold hover:bg-gray-300 transition">
                            Back to Home
                        </a>
                    </div>

                    <p class="text-xs text-gray-500 mt-6">
                        Please check your email (including spam folder) for confirmation
                    </p>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
