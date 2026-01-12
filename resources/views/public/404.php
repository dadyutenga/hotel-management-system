<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - MRK Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
                <p class="text-gray-600 mb-8">The page you're looking for doesn't exist or has been moved.</p>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-left">
                    <p class="text-sm text-blue-700 mb-2"><strong>Note:</strong> Pages have been reorganized:</p>
                    <ul class="text-xs text-blue-600 ml-4 space-y-1">
                        <li>• Management pages are now in <code>/manager/</code></li>
                        <li>• Department pages are now in <code>/worker/</code></li>
                        <li>• Old <code>/app/</code> folder is deprecated</li>
                    </ul>
                </div>
                
                <div class="space-y-3">
                    <a href="/MRK%20Hotel/public/index.php" class="block w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Go to Homepage
                    </a>
                    <a href="/MRK%20Hotel/public/login.php" class="block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Login
                    </a>
                </div>
                
                <div class="mt-6 text-xs text-gray-500">
                    <p>Requested URL: <code class="bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown'); ?></code></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
