<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Impasugong Accommodations</title>
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
        
        .benefits-list {
            text-align: left;
            max-width: 350px;
        }
        
        .benefits-list li {
            list-style: none;
            padding: 12px 0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 1rem;
        }
        
        .benefits-list li::before {
            content: '✓';
            background: rgba(255, 255, 255, 0.2);
            min-width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-top: 2px;
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
            max-width: 500px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: var(--green-medium);
        }
        
        /* Role Selection */
        .role-selection {
            margin-bottom: 30px;
        }
        
        .role-selection label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--green-dark);
        }
        
        .role-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .role-option {
            position: relative;
        }
        
        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        
        .role-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid var(--green-soft);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .role-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .role-card .title {
            font-weight: 600;
            color: var(--green-dark);
            margin-bottom: 5px;
        }
        
        .role-card .description {
            font-size: 0.85rem;
            color: var(--green-medium);
        }
        
        .role-option input[type="radio"]:checked + .role-card {
            border-color: var(--green-primary);
            background: var(--green-soft);
        }
        
        .role-option:hover .role-card {
            border-color: var(--green-light);
        }
        
        .form-group {
            margin-bottom: 20px;
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
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--green-soft);
        }
        
        .login-link p {
            color: var(--green-medium);
        }
        
        .login-link a {
            color: var(--green-primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .branding-section {
                padding: 30px;
            }
            
            .role-options {
                grid-template-columns: 1fr;
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
            
            <h1>Join Impasugong</h1>
            <p>Create your account today</p>
            
            <ul class="benefits-list">
                <li>Access to unique accommodations</li>
                <li>Easy booking management</li>
                <li>Direct communication with hosts</li>
                <li>Secure payment processing</li>
                <li>Verified listings only</li>
            </ul>
        </div>
    </div>
    
    <!-- Form Section -->
    <div class="form-section">
        <div class="form-container">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Select your account type and fill in the details</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Role Selection -->
                <div class="role-selection">
                    <label>I want to:</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="role_client" name="role" value="client" {{ old('role') === 'client' ? 'checked' : '' }}>
                            <label for="role_client" class="role-card">
                                <span class="icon">🏠</span>
                                <span class="title">Find Accommodation</span>
                                <span class="description">Browse and book properties</span>
                            </label>
                        </div>
                        
                        <div class="role-option">
                            <input type="radio" id="role_owner" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }}>
                            <label for="role_owner" class="role-card">
                                <span class="icon">🏨</span>
                                <span class="title">List My Property</span>
                                <span class="description">Manage accommodations</span>
                            </label>
                        </div>
                    </div>
                    @error('role')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Name -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           placeholder="Enter your full name">
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}" 
                           autocomplete="tel"
                           placeholder="Enter your phone number">
                    @error('phone')
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
                           autocomplete="new-password"
                           placeholder="Create a password">
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="submit-btn">
                    Create Account
                </button>
                
                <!-- Login Link -->
                <div class="login-link">
                    <p>Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
                    <p style="margin-top: 10px;">
                        <a href="{{ route('landing') }}">← Back to Home</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

