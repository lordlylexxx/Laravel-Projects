<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
            --gray-900: #111827;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        
        /* Navigation */
        .navbar {
            background: var(--white);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 80px; height: 60px; border-radius: 8px; object-fit: contain; background: white; padding: 3px; }
        .nav-logo span { font-size: 1.2rem; font-weight: 700; color: var(--green-dark); }
        
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--green-soft); color: var(--green-dark); }
        
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.primary:hover { background: var(--green-dark); transform: translateY(-2px); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        .nav-btn.secondary:hover { background: var(--green-pale); }
        
        /* Main Container */
        .main-container { padding-top: 80px; max-width: 900px; margin: 0 auto; padding: 90px 20px 40px; }
        
        /* Page Header */
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 8px; }
        .page-header p { color: var(--gray-500); }
        
        /* User Info Card */
        .user-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--green-primary);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            border: 4px solid var(--green-soft);
            overflow: hidden;
        }
        
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        
        .user-info h2 { font-size: 1.3rem; color: var(--gray-800); margin-bottom: 4px; }
        .user-info p { color: var(--gray-500); margin-bottom: 8px; }
        
        .role-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .role-badge.admin { background: #FEE2E2; color: #991B1B; }
        .role-badge.owner { background: #FEF3C7; color: #92400E; }
        .role-badge.client { background: var(--green-soft); color: var(--green-dark); }
        
        /* Settings Sections */
        .settings-section {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
        }
        
        .section-header { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-200); }
        .section-header h2 { font-size: 1.2rem; color: var(--gray-800); margin-bottom: 5px; }
        .section-header p { font-size: 0.9rem; color: var(--gray-500); }
        
        /* Form Styles */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--gray-700); font-size: 0.9rem; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="password"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: var(--white);
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--green-primary);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }
        
        .form-group textarea { resize: vertical; min-height: 100px; }
        
        .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        
        /* Avatar Upload */
        .avatar-upload { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; }
        .avatar-upload .user-avatar { width: 100px; height: 100px; font-size: 2.2rem; flex-shrink: 0; }
        .avatar-upload-area { flex: 1; }
        .avatar-upload-area input[type="file"] { display: none; }
        .avatar-upload-btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--green-soft);
            color: var(--green-dark);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .avatar-upload-btn:hover { background: var(--green-pale); }
        .avatar-upload-area p { margin-top: 8px; font-size: 0.8rem; color: var(--gray-500); }
        
        /* Buttons */
        .btn-group { display: flex; gap: 15px; margin-top: 25px; }
        .btn {
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3); }
        
        .btn-danger { background: #FEE2E2; color: #991B1B; }
        .btn-danger:hover { background: #FECACA; }
        
        .btn-secondary { background: var(--gray-100); color: var(--gray-700); }
        .btn-secondary:hover { background: var(--gray-200); }
        
        /* Password Section */
        .password-fields { display: grid; gap: 15px; }
        .password-field { position: relative; }
        .password-field input { padding-right: 45px; }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray-400);
        }
        
        /* Danger Zone */
        .danger-zone {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 12px;
            padding: 25px;
        }
        
        .danger-zone h3 { color: #991B1B; margin-bottom: 10px; font-size: 1rem; }
        .danger-zone p { color: #7F1D1D; font-size: 0.9rem; margin-bottom: 15px; }
        
        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success { background: #D1FAE5; color: #065F46; }
        .message.error { background: #FEE2E2; color: #991B1B; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .main-container { padding: 80px 15px 30px; }
            .form-row { grid-template-columns: 1fr; }
            .user-card { flex-direction: column; text-align: center; }
            .avatar-upload { flex-direction: column; }
        }
        
        /* Breadcrumb */
        .breadcrumb { display: flex; gap: 10px; margin-bottom: 15px; font-size: 0.85rem; }
        .breadcrumb a { color: var(--green-primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: var(--gray-500); }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="{{ route('landing') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.jpg" alt="System Logo">
            <span>Impasugong</span>
        </a>
        
        @auth
        <ul class="nav-links">
            @if(Auth::user()->role === 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.users') }}">Users</a></li>
                <li><a href="{{ route('admin.bookings') }}">Bookings</a></li>
            @elseif(Auth::user()->role === 'owner')
                <li><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('owner.accommodations.index') }}">Properties</a></li>
            @else
                <li><a href="{{ route('accommodations.index') }}">Browse</a></li>
                <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
            @endif
            <li><a href="{{ route('messages.index') }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="{{ route('profile.edit') }}" class="nav-btn secondary">Settings</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
        @endauth
    </nav>
    
    <!-- Main Content -->
    <div class="main-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('landing') }}">Home</a>
            <span>›</span>
            <span>Profile Settings</span>
        </div>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Profile Settings</h1>
            <p>Manage your account information and preferences</p>
        </div>
        
        <!-- Session Status -->
        @if(session('status') && str_contains(session('status'), 'profile'))
            <div class="message success">
                {{ session('status') === 'profile-updated' ? 'Your profile has been updated successfully!' : session('status') }}
            </div>
        @endif
        
        @if(session('status') === 'password-updated')
            <div class="message success">Your password has been updated successfully!</div>
        @endif
        
        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="message error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- User Info Card -->
        <div class="user-card">
            @if(Auth::user()->avatar)
                <div class="user-avatar">
                    <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                </div>
            @else
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
            @endif
            <div class="user-info">
                <h2>{{ Auth::user()->name }}</h2>
                <p>{{ Auth::user()->email }}</p>
                <span class="role-badge {{ Auth::user()->role }}">{{ Auth::user()->role }}</span>
            </div>
        </div>
        
        <!-- Profile Information Section -->
        <div class="settings-section">
            <div class="section-header">
                <h2>Personal Information</h2>
                <p>Update your personal details and contact information</p>
            </div>
            
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')
                
                <!-- Avatar Upload -->
                <div class="avatar-upload">
                    @if(Auth::user()->avatar)
                        <div class="user-avatar">
                            <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="Avatar">
                        </div>
                    @else
                        <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    @endif
                    <div class="avatar-upload-area">
                        <label for="avatar" class="avatar-upload-btn">
                            📷 Change Photo
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*">
                        <p>JPEG, PNG, JPG. Max 2MB. Recommended: 200x200px</p>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required autocomplete="name">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required autocomplete="email">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="{{ old('address', Auth::user()->address) }}" placeholder="Enter your address">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" placeholder="Tell us about yourself...">{{ old('bio', Auth::user()->bio) }}</textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
        
        <!-- Password Section -->
        <div class="settings-section">
            <div class="section-header">
                <h2>Change Password</h2>
                <p>Ensure your account is using a secure password</p>
            </div>
            
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                
                <div class="password-fields">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
        
        <!-- Danger Zone -->
        <div class="settings-section">
            <div class="danger-zone">
                <h3>Delete Account</h3>
                <p>Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone.</p>
                
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    @csrf
                    @method('delete')
                    
                    <div class="form-group">
                        <label for="password_delete">Enter your password to confirm</label>
                        <input type="password" id="password_delete" name="password" required autocomplete="new-password" placeholder="Your current password">
                    </div>
                    
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Avatar preview
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarContainer = document.querySelector('.avatar-upload .user-avatar');
                    if (avatarContainer.querySelector('img')) {
                        avatarContainer.querySelector('img').src = e.target.result;
                    } else {
                        avatarContainer.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Confirm before delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete your account? This action cannot be undone.');
        }
    </script>
</body>
</html>
