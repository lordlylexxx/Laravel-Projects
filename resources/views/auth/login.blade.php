<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Impasugong Accommodations</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
            --green-light: #66BB6A;
            --green-pale: #81C784;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--white) 50%, var(--green-soft) 100%);
        }
        
        /* Left Side - Branding */
        .branding-section {
            flex: 1;
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-primary) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .branding-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/COMMUNAL.jpg') no-repeat center center/cover;
            opacity: 0.15;
        }
        
        .branding-content {
            text-align: center;
            z-index: 1;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .municipality-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }
        
        .logo-divider {
            width: 3px;
            height: 60px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 2px;
        }
        
        .branding-content h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .branding-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .features-list {
            text-align: left;
            max-width: 300px;
        }
        
        .features-list li {
            list-style: none;
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }
        
        .features-list li::before {
            content: '✓';
            background: rgba(255, 255, 255, 0.2);
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        
        /* Right Side - Form */
        .form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .form-container {
            background: var(--white);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(27, 94, 32, 0.15);
            width: 100%;
            max-width: 450px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .form-header h2 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: var(--green-medium);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--green-dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }
        
        .form-group input::placeholder {
            color: #aaa;
        }
        
        .form-group .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--green-primary);
        }
        
        .forgot-link {
            color: var(--green-primary);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--green-soft);
        }
        
        .register-link p {
            color: var(--green-medium);
        }
        
        .register-link a {
            color: var(--green-primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* Session Status */
        .session-status {
            background: var(--green-soft);
            color: var(--green-dark);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .branding-section {
                padding: 30px;
            }
            
            .form-section {
                padding: 20px;
            }
            
            .form-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Branding Section -->
    <div class="branding-section">
        <div class="branding-content">
            <div class="logo-container">
                <img src="/1.jpg" alt="Municipality Logo" class="municipality-logo">
                <div class="logo-divider"></div>
                <img src="/2.jpg" alt="Municipality Logo" class="municipality-logo">
            </div>
            
            <h1>Impasugong Accommodations</h1>
            <p>Your Gateway to Municipal Services</p>
            
            <ul class="features-list">
                <li>Browse unique accommodations</li>
                <li>Book traveller-inns & Airbnb</li>
                <li>Daily property rentals</li>
                <li>Secure online booking</li>
                <li>Direct owner communication</li>
            </ul>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="form-section">
        <div class="form-container">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="session-status">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me</span>
                    </label>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Forgot password?
                        </a>
                    @endif
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="submit-btn">
                    Sign In
                </button>
                
                <!-- Register Link -->
                <div class="register-link">
                    <p>Don't have an account? <a href="{{ route('register') }}">Create Account</a></p>
                    <p style="margin-top: 10px; font-size: 0.9rem;">
                        <a href="{{ route('landing') }}">← Back to Home</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

