<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MRK Hotel Management App</title>
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
        }
    </script>
</head>
<body class="bg-gray-50 flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md rounded-md bg-white p-6 shadow-sm">
        <div class="text-center mb-6">
            <h1 class="text-sm font-bold text-secondary">Forgot Password</h1>
            <p class="mt-2 text-gray-600">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <form action="/auth/forgot-password-process.php" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" required
                       class="mt-1 w-full rounded-md border border-gray-300 px-4 py-3 min-h-[44px] focus:border-primary focus:ring-primary"
                       placeholder="your.business@email.com">
            </div>

            <button type="submit"
                    class="w-full rounded-md bg-primary px-4 py-2 text-white font-medium shadow-sm hover:bg-blue-700 min-h-[44px] transition">
                Send Reset Link
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Remember your password? 
            <a href="login.php" class="text-primary font-medium hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>


