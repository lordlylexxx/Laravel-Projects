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
            --cream: #F1F8E9;
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
            border-radius: 12px;
            border: none;
            object-fit: contain;
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
        
        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 24px;
            left: 50%;
            width: 50%;
            height: 2px;
            background: var(--green-soft);
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step.active::after,
        .step.completed::after {
            background: var(--green-primary);
        }
        
        .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--green-soft);
            color: var(--green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .step.active .step-number {
            background: var(--green-primary);
            color: white;
        }
        
        .step.completed .step-number {
            background: var(--green-primary);
            color: white;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: var(--green-medium);
            font-weight: 500;
        }
        
        .step.active .step-label {
            color: var(--green-dark);
            font-weight: 600;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            font-size: 1.8rem;
            color: var(--green-dark);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: var(--green-medium);
            font-size: 0.95rem;
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
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
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
        
        /* Color Picker */
        .color-picker-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .color-input-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }
        
        .color-input-wrapper input[type="color"] {
            width: 50px;
            height: 50px;
            border: none;
            cursor: pointer;
            padding: 0;
            border-radius: 8px;
        }
        
        .color-input-wrapper input[type="text"] {
            flex: 1;
            padding: 10px 15px;
        }
        
        /* Logo Upload */
        .logo-upload-wrapper {
            position: relative;
            overflow: hidden;
        }
        
        .logo-upload-wrapper input[type="file"] {
            display: none;
        }
        
        .logo-upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 40px;
            border: 2px dashed var(--green-soft);
            border-radius: 10px;
            background: var(--cream);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
            color: var(--green-medium);
        }
        
        .logo-upload-wrapper:hover .logo-upload-btn {
            border-color: var(--green-primary);
            background: var(--green-white);
        }
        
        .logo-preview {
            margin-top: 15px;
            text-align: center;
        }
        
        .logo-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
        }
        
        /* Features Checkboxes */
        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .feature-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .feature-checkbox:hover {
            border-color: var(--green-light);
            background: var(--green-white);
        }
        
        .feature-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--green-primary);
        }
        
        .feature-details {
            flex: 1;
        }
        
        .feature-details .feature-name {
            font-weight: 600;
            color: var(--green-dark);
            margin-bottom: 3px;
        }
        
        .feature-details .feature-description {
            font-size: 0.85rem;
            color: var(--green-medium);
        }
        
        /* Step Content */
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
        }
        
        /* Buttons */
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-secondary {
            background: var(--green-soft);
            color: var(--green-dark);
        }
        
        .btn-secondary:hover {
            background: var(--green-light);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.3);
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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
        
        /* Summary */
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--green-soft);
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 600;
            color: var(--green-dark);
        }
        
        .summary-value {
            color: var(--green-medium);
            text-align: right;
        }
        
        .summary-value.color-preview {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .color-swatch {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        
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
            
            .progress-steps {
                flex-wrap: wrap;
            }
            
            .step {
                flex: 0 0 32%;
            }
            
            .step::after {
                display: none;
            }
            
            .color-picker-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Branding Section -->
    <div class="branding-section">
        <div class="branding-content">
            <div class="logo-container">
                <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo" class="municipality-logo">
                <div class="logo-divider"></div>
                <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo" class="municipality-logo">
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
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active" id="step-1-indicator">
                    <div class="step-number">1</div>
                    <div class="step-label">Account</div>
                </div>
                <div class="step" id="step-2-indicator">
                    <div class="step-number">2</div>
                    <div class="step-label">Customize</div>
                </div>
                <div class="step" id="step-3-indicator">
                    <div class="step-number">3</div>
                    <div class="step-label">Review</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('register') }}" id="registration-form" enctype="multipart/form-data">
                @csrf
                
                <!-- STEP 1: Account Details -->
                <div class="step-content active" id="step-1">
                    <div class="form-header">
                        <h2>Create Account</h2>
                        <p>Select your role and provide account details</p>
                    </div>
                    
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
                                <input type="radio" id="role_owner" name="role" value="owner" {{ old('role') === 'owner' ? 'checked' : '' }} onchange="updateStepTwo()">
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
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name">
                        @error('name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                        @error('phone')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Create a password">
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                    </div>
                </div>
                
                <!-- STEP 2: Customization -->
                <div class="step-content" id="step-2">
                    <div class="form-header">
                        <h2>Customize Your App</h2>
                        <p>Set up your app preferences</p>
                    </div>
                    
                    <!-- App Title -->
                    <div class="form-group">
                        <label for="app_title">Business/App Name</label>
                        <input type="text" id="app_title" name="app_title" value="{{ old('app_title') }}" placeholder="e.g., Sarah's Space Stays">
                        <small style="color: var(--green-medium);">This will be displayed in your app. Leave blank to auto-generate.</small>
                    </div>
                    
                    <!-- Theme Colors -->
                    <div class="form-group">
                        <label>Theme Colors</label>
                        <div class="color-picker-group">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem;">Primary Color</label>
                                <div class="color-input-wrapper">
                                    <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', '#2E7D32') }}">
                                    <input type="text" id="primary_color_hex" name="primary_color_hex" value="{{ old('primary_color', '#2E7D32') }}" placeholder="#2E7D32">
                                </div>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem;">Accent Color</label>
                                <div class="color-input-wrapper">
                                    <input type="color" id="accent_color" name="accent_color" value="{{ old('accent_color', '#43A047') }}">
                                    <input type="text" id="accent_color_hex" name="accent_color_hex" value="{{ old('accent_color', '#43A047') }}" placeholder="#43A047">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Logo Upload -->
                    <div class="form-group">
                        <label>Business Logo (Optional)</label>
                        <div class="logo-upload-wrapper">
                            <input type="file" id="logo_path" name="logo_path" accept="image/*" onchange="previewLogo(this)">
                            <div class="logo-upload-btn" onclick="document.getElementById('logo_path').click()">
                                📤 Click to upload or drag and drop your logo
                            </div>
                            <div class="logo-preview" id="logo-preview" style="display: none;">
                                <img id="logo-preview-img" src="" alt="Logo preview">
                                <button type="button" onclick="clearLogo()" style="margin-top: 10px; padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">Remove</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Locale Selection -->
                    <div class="form-group">
                        <label for="locale">Language</label>
                        <select id="locale" name="locale">
                            <option value="en" {{ old('locale', 'en') === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ old('locale') === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="fr" {{ old('locale') === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="de" {{ old('locale') === 'de' ? 'selected' : '' }}>Deutsch</option>
                        </select>
                    </div>
                    
                    <!-- Features Selection -->
                    <div class="form-group">
                        <label>Enable Features</label>
                        <div class="features-grid">
                            <label class="feature-checkbox">
                                <input type="checkbox" name="feature_bookings" value="1" {{ old('feature_bookings') ? 'checked' : 'checked' }}>
                                <div class="feature-details">
                                    <div class="feature-name">📅 Booking System</div>
                                    <div class="feature-description">Allow guests to book accommodations</div>
                                </div>
                            </label>
                            
                            <label class="feature-checkbox">
                                <input type="checkbox" name="feature_messaging" value="1" {{ old('feature_messaging') ? 'checked' : 'checked' }}>
                                <div class="feature-details">
                                    <div class="feature-name">💬 Messaging</div>
                                    <div class="feature-description">Enable direct communication with guests</div>
                                </div>
                            </label>
                            
                            <label class="feature-checkbox">
                                <input type="checkbox" name="feature_reviews" value="1" {{ old('feature_reviews') ? 'checked' : 'checked' }}>
                                <div class="feature-details">
                                    <div class="feature-name">⭐ Reviews</div>
                                    <div class="feature-description">Allow guests to leave reviews</div>
                                </div>
                            </label>
                            
                            <label class="feature-checkbox">
                                <input type="checkbox" name="feature_payments" value="1" {{ old('feature_payments') ? 'checked' : 'checked' }}>
                                <div class="feature-details">
                                    <div class="feature-name">💳 Online Payments</div>
                                    <div class="feature-description">Accept online payments for bookings</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- STEP 3: Review -->
                <div class="step-content" id="step-3">
                    <div class="form-header">
                        <h2>Review Your Setup</h2>
                        <p>Confirm all details before creating your account</p>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--green-dark); margin-bottom: 15px;">Account Summary</h3>
                        <div class="summary-item">
                            <span class="summary-label">Name</span>
                            <span class="summary-value" id="summary-name"></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Email</span>
                            <span class="summary-value" id="summary-email"></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Phone</span>
                            <span class="summary-value" id="summary-phone"></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Role</span>
                            <span class="summary-value" id="summary-role"></span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 1.1rem; color: var(--green-dark); margin-bottom: 15px;">App Customization</h3>
                        <div class="summary-item">
                            <span class="summary-label">App Name</span>
                            <span class="summary-value" id="summary-app-title"></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Theme</span>
                            <span class="summary-value color-preview">
                                <span id="summary-colors"></span>
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Language</span>
                            <span class="summary-value" id="summary-locale"></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Features</span>
                            <span class="summary-value" id="summary-features"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="btn-prev" style="display: none;" onclick="prevStep()">← Back</button>
                    <button type="button" class="btn btn-primary" id="btn-next" onclick="nextStep()">Next →</button>
                </div>
            </form>
            
            <div class="login-link">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
            </div>
        </div>
    </div>
    
    <script>
        let currentStep = 1;
        
        document.getElementById('primary_color').addEventListener('input', function() {
            document.getElementById('primary_color_hex').value = this.value.toUpperCase();
        });
        
        document.getElementById('primary_color_hex').addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                document.getElementById('primary_color').value = this.value;
            }
        });
        
        document.getElementById('accent_color').addEventListener('input', function() {
            document.getElementById('accent_color_hex').value = this.value.toUpperCase();
        });
        
        document.getElementById('accent_color_hex').addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                document.getElementById('accent_color').value = this.value;
            }
        });
        
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview-img').src = e.target.result;
                    document.getElementById('logo-preview').style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function clearLogo() {
            document.getElementById('logo_path').value = '';
            document.getElementById('logo-preview').style.display = 'none';
        }
        
        function updateStepTwo() {
            const role = document.querySelector('input[name="role"]:checked');
            const step2 = document.getElementById('step-2');
            const roleIndicator = document.getElementById('step-2-indicator');
            
            if (role && role.value === 'owner') {
                step2.style.display = 'block';
                roleIndicator.style.display = 'flex';
            } else {
                step2.style.display = 'none';
                roleIndicator.style.display = 'none';
            }
        }
        
        function nextStep() {
            const role = document.querySelector('input[name="role"]:checked');
            
            if (!role) {
                alert('Please select a role');
                return;
            }
            
            if (currentStep === 1 && !validateStep1()) return;
            
            if (currentStep === 1) {
                if (role.value === 'client') {
                    document.getElementById('registration-form').submit();
                    return;
                }
                currentStep = 2;
            } else if (currentStep === 2) {
                currentStep = 3;
                updateSummary();
            } else if (currentStep === 3) {
                document.getElementById('registration-form').submit();
            }
            
            updateUI();
        }
        
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        }
        
        function validateStep1() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            
            if (!name || !email || !password || !passwordConfirm) {
                alert('Please fill in all required fields');
                return false;
            }
            
            if (password !== passwordConfirm) {
                alert('Passwords do not match');
                return false;
            }
            
            return true;
        }
        
        function updateUI() {
            // Hide all content
            document.getElementById('step-1').classList.remove('active');
            document.getElementById('step-2').classList.remove('active');
            document.getElementById('step-3').classList.remove('active');
            
            // Show current content
            document.getElementById('step-' + currentStep).classList.add('active');
            
            // Update step indicators
            document.getElementById('step-1-indicator').className = 'step' + (currentStep > 1 ? ' completed' : (currentStep === 1 ? ' active' : ''));
            document.getElementById('step-2-indicator').className = 'step' + (currentStep > 2 ? ' completed' : (currentStep === 2 ? ' active' : ''));
            document.getElementById('step-3-indicator').className = 'step' + (currentStep === 3 ? ' active' : '');
            
            // Update buttons
            document.getElementById('btn-prev').style.display = currentStep > 1 ? 'block' : 'none';
            document.getElementById('btn-next').textContent = currentStep === 3 ? 'Create Account' : 'Next →';
            
            // Show/hide step 2 based on role
            const role = document.querySelector('input[name="role"]:checked');
            const step2Indicator = document.getElementById('step-2-indicator');
            const step3Indicator = document.getElementById('step-3-indicator');
            
            if (role && role.value === 'owner') {
                step2Indicator.style.display = 'flex';
                step3Indicator.style.display = 'flex';
            } else {
                step2Indicator.style.display = 'none';
                step3Indicator.style.display = 'none';
            }
        }
        
        function updateSummary() {
            // Account info
            document.getElementById('summary-name').textContent = document.getElementById('name').value;
            document.getElementById('summary-email').textContent = document.getElementById('email').value;
            document.getElementById('summary-phone').textContent = document.getElementById('phone').value || 'Not provided';
            document.getElementById('summary-role').textContent = 'Property Owner';
            
            // Customization info
            const appTitle = document.getElementById('app_title').value || 'Auto-generated from your name';
            document.getElementById('summary-app-title').textContent = appTitle;
            
            const primaryColor = document.getElementById('primary_color').value;
            const accentColor = document.getElementById('accent_color').value;
            document.getElementById('summary-colors').innerHTML = `
                <span style="display: flex; gap: 8px; align-items: center;">
                    <div class="color-swatch" style="background: ${primaryColor};"></div>
                    <div class="color-swatch" style="background: ${accentColor};"></div>
                </span>
            `;
            
            const localeMap = {
                'en': 'English',
                'es': 'Español',
                'fr': 'Français',
                'de': 'Deutsch'
            };
            document.getElementById('summary-locale').textContent = localeMap[document.getElementById('locale').value];
            
            const features = [];
            if (document.querySelector('input[name="feature_bookings"]').checked) features.push('📅 Bookings');
            if (document.querySelector('input[name="feature_messaging"]').checked) features.push('💬 Messaging');
            if (document.querySelector('input[name="feature_reviews"]').checked) features.push('⭐ Reviews');
            if (document.querySelector('input[name="feature_payments"]').checked) features.push('💳 Payments');
            document.getElementById('summary-features').textContent = features.join(', ') || 'None';
        }
        
        // Initialize
        updateStepTwo();
    </script>
</body>
</html>
