<?php
/**
 * System Test Page
 * Verify installation and paths
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Test - MRK Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">MRK Hotel System Test</h1>
                
                <!-- Server Info -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Server Information</h2>
                    <div class="bg-gray-50 p-4 rounded space-y-2 text-sm">
                        <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                        <p><strong>Document Root:</strong> <code class="bg-gray-200 px-2 py-1 rounded"><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></code></p>
                        <p><strong>Current Directory:</strong> <code class="bg-gray-200 px-2 py-1 rounded"><?php echo __DIR__; ?></code></p>
                    </div>
                </div>
                
                <!-- File Structure Test -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Folder Structure</h2>
                    <div class="space-y-2">
                        <?php
                        $folders = [
                            'public' => __DIR__,
                            'manager' => __DIR__ . '/../manager',
                            'worker' => __DIR__ . '/../worker',
                            'includes' => __DIR__ . '/../includes',
                            'config' => __DIR__ . '/../config',
                            'database' => __DIR__ . '/../database'
                        ];
                        
                        foreach ($folders as $name => $path) {
                            $exists = is_dir($path);
                            $color = $exists ? 'green' : 'red';
                            $icon = $exists ? '✓' : '✗';
                            echo "<div class='flex items-center'>";
                            echo "<span class='text-$color-600 font-bold mr-2'>$icon</span>";
                            echo "<span class='text-gray-700'>/$name/</span>";
                            if (!$exists) {
                                echo "<span class='text-red-500 text-xs ml-2'>(Missing!)</span>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Key Files Test -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Key Files</h2>
                    <div class="space-y-2">
                        <?php
                        $files = [
                            'Database Config' => __DIR__ . '/../config/database.php',
                            'Session Handler' => __DIR__ . '/../includes/session.php',
                            'Auth System' => __DIR__ . '/../includes/auth.php',
                            'Manager Dashboard' => __DIR__ . '/../manager/dashboard.php',
                            'Worker Reservations' => __DIR__ . '/../worker/reservations.php',
                            'Login Page' => __DIR__ . '/login.php'
                        ];
                        
                        foreach ($files as $name => $path) {
                            $exists = file_exists($path);
                            $color = $exists ? 'green' : 'red';
                            $icon = $exists ? '✓' : '✗';
                            echo "<div class='flex items-center'>";
                            echo "<span class='text-$color-600 font-bold mr-2'>$icon</span>";
                            echo "<span class='text-gray-700'>$name</span>";
                            if (!$exists) {
                                echo "<span class='text-red-500 text-xs ml-2'>(Missing!)</span>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Database Connection Test -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Database Connection</h2>
                    <?php
                    try {
                        require_once __DIR__ . '/../config/database.php';
                        $db = Database::getInstance();
                        $conn = $db->getConnection();
                        echo "<div class='bg-green-50 border-l-4 border-green-500 p-4'>";
                        echo "<p class='text-green-700'><strong>✓ Connected!</strong> Database connection successful.</p>";
                        echo "</div>";
                    } catch (Exception $e) {
                        echo "<div class='bg-red-50 border-l-4 border-red-500 p-4'>";
                        echo "<p class='text-red-700'><strong>✗ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
                
                <!-- Quick Links -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Quick Links</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="/MRK%20Hotel/public/index.php" class="bg-blue-600 text-white px-4 py-2 rounded text-center hover:bg-blue-700 transition">Homepage</a>
                        <a href="/MRK%20Hotel/public/login.php" class="bg-green-600 text-white px-4 py-2 rounded text-center hover:bg-green-700 transition">Login</a>
                        <a href="/MRK%20Hotel/manager/dashboard.php" class="bg-purple-600 text-white px-4 py-2 rounded text-center hover:bg-purple-700 transition">Manager Dashboard</a>
                        <a href="/MRK%20Hotel/worker/reservations.php" class="bg-orange-600 text-white px-4 py-2 rounded text-center hover:bg-orange-700 transition">Worker Page</a>
                    </div>
                </div>
                
                <!-- URL Test -->
                <div>
                    <h2 class="text-xl font-semibold mb-3">Current Request Info</h2>
                    <div class="bg-gray-50 p-4 rounded space-y-2 text-sm">
                        <p><strong>Request URI:</strong> <code class="bg-gray-200 px-2 py-1 rounded"><?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A'); ?></code></p>
                        <p><strong>Script Name:</strong> <code class="bg-gray-200 px-2 py-1 rounded"><?php echo htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'N/A'); ?></code></p>
                        <p><strong>Script Filename:</strong> <code class="bg-gray-200 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($_SERVER['SCRIPT_FILENAME'] ?? 'N/A'); ?></code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
