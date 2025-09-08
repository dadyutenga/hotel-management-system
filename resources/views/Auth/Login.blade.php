<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HotelPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }
        
        .overlay-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }
        
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            padding: 0 10px;
        }
        
        .brand-logo {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .brand-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            border: none;
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 30px 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #1a237e, transparent);
        }
        
        .card-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a237e;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .card-title i {
            background: rgba(26, 35, 126, 0.1);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            background: #f9f9f9;
        }
        
        .form-control:focus {
            border-color: #1a237e;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
            outline: none;
            background: #fff;
        }
        
        .form-control.is-invalid {
            border-color: #f44336;
            background: #fff;
        }
        
        .invalid-feedback {
            color: #f44336;
            font-weight: 500;
            font-size: 14px;
            margin-top: 6px;
            display: block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            border: none;
            border-radius: 10px;
            padding: 16px 24px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
        }
        
        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .alert-success {
            background-color: #e8f5e8;
            color: #2e7d32;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        .back-to-home {
            text-align: center;
            margin-top: 25px;
            padding: 5px;
        }
        
        .back-to-home a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .back-to-home a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            z-index: 2;
            pointer-events: none;
        }
        
        .form-control.with-icon {
            padding-left: 48px;
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #f0f0f0;
        }
        
        .register-link a {
            color: #f44336;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            padding: 3px 0;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }
        
        .register-link a:hover {
            border-bottom-color: #f44336;
            text-decoration: none;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 15px;
                max-width: 100%;
            }
            
            .card-body {
                padding: 25px 20px;
            }
            
            .card-header {
                padding: 25px 20px 15px;
            }
            
            .brand-logo {
                font-size: 30px;
            }
            
            .form-control {
                padding: 12px 16px;
            }
            
            .btn-primary {
                padding: 14px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay-pattern"></div>
    
    <div class="login-container">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fas fa-hotel"></i> HotelPro
            </div>
            <div class="brand-subtitle">Hotel Management System</div>
        </div>
        
        <!-- Login Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-user"></i>
                    User Login
                </h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="/login">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   class="form-control with-icon @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email address"
                                   required 
                                   autofocus>
                        </div>
                        @error('email')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   class="form-control with-icon @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                        </div>
                        @error('password')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                    </button>
                </form>
                
                <div class="register-link">
                    <p>Don't have an account? <a href="/register">Register your business</a></p>
                </div>
            </div>
        </div>
        
        <!-- Back to Home -->
        <div class="back-to-home">
            <a href="/">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
