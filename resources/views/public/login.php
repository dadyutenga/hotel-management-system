<?php
require_once __DIR__ . '/../includes/session.php';
$error = Session::getError();
$success = Session::getSuccess();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MRK Hotel Management App</title>
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
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="rounded-2xl bg-white/95 backdrop-blur-sm p-6 shadow-xl border border-gray-100">
            <div class="mb-3">
                <a href="index.php" class="inline-flex items-center text-xs text-gray-500 hover:text-primary transition-colors">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Home
                </a>
            </div>
            <div class="text-center mb-5">
                <div class="flex justify-center mb-6">
                    <img src="/MRK%20Hotel/assets/logo.png" alt="MRK Hotel Logo" class="h-[40px] w-auto" style="background: transparent; mix-blend-mode: multiply;">
                </div>
                <div class="flex justify-center mb-3">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-primary to-blue-600">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-secondary">Welcome Back</h1>
                <p class="mt-1 text-sm text-gray-500">Sign in to access your hotel dashboard</p>
            </div>
            <?php if ($error): ?>
                <div class="mb-4 p-3 bg-red-50 border-2 border-red-200 rounded-lg text-red-800 text-sm">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="mb-4 p-3 bg-green-50 border-2 border-green-200 rounded-lg text-green-800 text-sm">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
        <form action="../auth/login-process.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 min-h-[48px] focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                       placeholder="your.email@example.com"
                       autofocus>
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 w-full text-sm rounded-lg border-2 border-gray-200 px-4 py-3 min-h-[48px] focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <button type="submit"
                    class="w-full text-sm rounded-lg bg-gradient-to-r from-primary to-blue-600 px-4 py-3 text-white hover:shadow-lg font-semibold min-h-[48px] transition-all transform hover:scale-[1.02] shadow-md">
                Sign In
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="forgot-password.php" class="text-sm text-primary hover:text-blue-700 font-medium">Forgot Password?</a>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 text-center text-sm text-gray-600">
            Don't have an account? 
            <a href="register.php" class="text-primary font-semibold hover:text-blue-700 transition-colors">Create Account</a>
        </div>
    </div>
    </div>
</body>
</html>


