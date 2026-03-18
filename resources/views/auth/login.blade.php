<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WikiEngine') }} - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Figtree', sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 90%;
            display: flex;
            min-height: 500px;
        }
        
        .login-illustration {
            flex: 1;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }
        
        .login-illustration img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        .login-form-container {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #718096;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #4a5568;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-input::placeholder {
            color: #a0aec0;
        }
        
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 13px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #4a5568;
        }
        
        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #fc8181;
        }
        
        .success-message {
            background: #c6f6d5;
            color: #2f855a;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #68d391;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-illustration {
                min-height: 200px;
                padding: 20px;
            }
            
            .login-form-container {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Illustration -->
        <div class="login-illustration">
            @if(file_exists(public_path('images/login-illustration.png')))
                <img src="{{ asset('images/login-illustration.png') }}" alt="Login Illustration">
            @else
                <!-- Person Sitting with Device - Login Illustration -->
                <svg width="280" height="320" viewBox="0 0 280 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Background Circle -->
                    <circle cx="140" cy="160" r="110" fill="#667eea" opacity="0.1"/>
                    
                    <!-- Chair -->
                    <rect x="90" y="210" width="100" height="12" rx="6" fill="#4a5568"/>
                    <rect x="95" y="222" width="10" height="60" rx="5" fill="#4a5568"/>
                    <rect x="175" y="222" width="10" height="60" rx="5" fill="#4a5568"/>
                    <rect x="105" y="185" width="70" height="40" rx="8" fill="#667eea" opacity="0.8"/>
                    
                    <!-- Person's Body -->
                    <!-- Legs -->
                    <rect x="120" y="200" width="18" height="50" rx="9" fill="#764ba2"/>
                    <rect x="142" y="200" width="18" height="50" rx="9" fill="#764ba2"/>
                    
                    <!-- Torso -->
                    <ellipse cx="140" cy="160" rx="35" ry="45" fill="#667eea"/>
                    
                    <!-- Arms -->
                    <rect x="95" y="145" width="15" height="55" rx="7.5" fill="#667eea" transform="rotate(-20 102.5 172.5)"/>
                    <rect x="170" y="145" width="15" height="55" rx="7.5" fill="#667eea" transform="rotate(20 177.5 172.5)"/>
                    
                    <!-- Head -->
                    <circle cx="140" cy="110" r="28" fill="#fbb6ce"/>
                    
                    <!-- Hair -->
                    <ellipse cx="140" cy="95" rx="30" ry="25" fill="#2d3748"/>
                    
                    <!-- Face Details -->
                    <circle cx="130" cy="108" r="3" fill="#2d3748"/>
                    <circle cx="150" cy="108" r="3" fill="#2d3748"/>
                    <path d="M 130 120 Q 140 125 150 120" stroke="#2d3748" stroke-width="2" fill="none" stroke-linecap="round"/>
                    
                    <!-- Laptop/Device -->
                    <g transform="translate(95, 168)">
                        <!-- Screen -->
                        <rect x="0" y="0" width="90" height="60" rx="4" fill="#2d3748"/>
                        <rect x="5" y="5" width="80" height="50" rx="2" fill="#4299e1"/>
                        
                        <!-- Login Form on Screen -->
                        <rect x="15" y="15" width="60" height="8" rx="4" fill="white" opacity="0.8"/>
                        <rect x="15" y="28" width="60" height="8" rx="4" fill="white" opacity="0.8"/>
                        <rect x="15" y="41" width="40" height="8" rx="4" fill="#48bb78"/>
                        
                        <!-- Keyboard Base -->
                        <path d="M 5 60 L 0 70 L 90 70 L 85 60 Z" fill="#718096"/>
                    </g>
                    
                    <!-- Floating Security Icons -->
                    <!-- Lock Icon -->
                    <g transform="translate(30, 100)">
                        <rect x="8" y="15" width="20" height="20" rx="3" fill="#667eea" opacity="0.6"/>
                        <rect x="13" y="10" width="10" height="15" rx="5" fill="none" stroke="#667eea" stroke-width="2.5" opacity="0.6"/>
                        <circle cx="18" cy="25" r="2.5" fill="white"/>
                    </g>
                    
                    <!-- Shield Icon -->
                    <g transform="translate(220, 120)">
                        <path d="M 15 5 L 25 10 L 25 25 Q 25 35 15 40 Q 5 35 5 25 L 5 10 Z" fill="#764ba2" opacity="0.6"/>
                        <path d="M 11 22 L 14 25 L 20 19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </g>
                    
                    <!-- Key Icon -->
                    <g transform="translate(230, 200)">
                        <circle cx="10" cy="10" r="8" fill="none" stroke="#667eea" stroke-width="2.5" opacity="0.5"/>
                        <line x1="10" y1="18" x2="10" y2="35" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" opacity="0.5"/>
                        <line x1="6" y1="26" x2="10" y2="26" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" opacity="0.5"/>
                        <line x1="6" y1="31" x2="10" y2="31" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" opacity="0.5"/>
                    </g>
                    
                    <!-- Check Mark -->
                    <g transform="translate(40, 200)">
                        <circle cx="15" cy="15" r="15" fill="#48bb78" opacity="0.2"/>
                        <path d="M 8 15 L 13 20 L 22 11" stroke="#48bb78" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </g>
                </svg>
            @endif
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <h1 class="login-title">Welcome Back!</h1>
            <p class="login-subtitle">Please login to your account</p>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif
            
            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">E-Mail Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="Enter your email"
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                    >
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter your password"
                        required 
                        autocomplete="current-password"
                    >
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>Remember me</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">
                            Forgot Password?
                        </a>
                    @endif
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="login-button">
                    Log In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
