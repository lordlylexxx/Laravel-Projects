<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipal Official Website - Impasugong</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 50, 0, 0.7), rgba(0, 60, 0, 0.8)),
                        url('/COMMUNAL.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        
        .landing-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        /* Municipality Logos Section */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .municipality-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #2E7D32;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            object-fit: cover;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .municipality-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(46, 125, 50, 0.4);
        }
        
        .logo-divider {
            width: 3px;
            height: 80px;
            background: linear-gradient(to bottom, #4CAF50, #2E7D32, #4CAF50);
            border-radius: 2px;
        }
        
        /* Main Title */
        .main-title {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .main-title h1 {
            font-size: 3.5rem;
            color: #E8F5E9;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        
        .main-title .subtitle {
            font-size: 1.5rem;
            color: #A5D6A7;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            letter-spacing: 4px;
        }
        
        /* Content Section */
        .content-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 60px;
            margin: 30px 0;
            border: 2px solid rgba(76, 175, 80, 0.3);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
        }
        
        .content-section h2 {
            color: #C8E6C9;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .content-section p {
            color: #E8F5E9;
            font-size: 1.2rem;
            line-height: 1.8;
            text-align: center;
            margin-bottom: 25px;
        }
        
        /* Buttons */
        .btn-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .btn {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            color: white;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(46, 125, 50, 0.6);
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }
        
        .btn-secondary {
            background: transparent;
            color: #A5D6A7;
            border: 3px solid #4CAF50;
        }
        
        .btn-secondary:hover {
            background: rgba(76, 175, 80, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }
        
        /* Features Section */
        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
            width: 100%;
            max-width: 1200px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(76, 175, 80, 0.2);
            transition: transform 0.3s ease, background 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .feature-card h3 {
            color: #81C784;
            font-size: 1.3rem;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #E8F5E9;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #A5D6A7;
            padding: 20px;
            font-size: 0.9rem;
        }
        
        .footer a {
            color: #81C784;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-in {
            animation: fadeInUp 0.8s ease forwards;
        }
        
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="landing-container">
        <!-- Municipality Logos Section -->
        <div class="logo-section animate-in">
            <img src="/1.jpg" alt="Municipality Logo 1" class="municipality-logo">
            <div class="logo-divider"></div>
            <img src="/2.jpg" alt="Municipality Logo 2" class="municipality-logo">
        </div>
        
        <!-- Main Title -->
        <div class="main-title animate-in delay-1">
            <h1>WELCOME TO IMPASUGONG</h1>
            <p class="subtitle">MUNICIPALITY OF IMPASUGONG</p>
        </div>
        
        <!-- Content Section -->
        <div class="content-section animate-in delay-2">
            <h2>Your Gateway to Municipal Services</h2>
            <p>
                Experience seamless access to government services and information. 
                Our digital platform connects you with essential municipal resources, 
                news, and services—all in one place.
            </p>
            
            <div class="btn-container">
                <a href="/login" class="btn btn-primary">Login</a>
                <a href="/register" class="btn btn-secondary">Register</a>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="features-section animate-in delay-3">
            <div class="feature-card">
                <h3>📋 Easy Registration</h3>
                <p>Quick and simple registration process to access all municipal services online.</p>
            </div>
            
            <div class="feature-card">
                <h3>💼 Service Requests</h3>
                <p>Submit and track your service requests with ease and transparency.</p>
            </div>
            
            <div class="feature-card">
                <h3>📰 News & Updates</h3>
                <p>Stay informed with the latest announcements and municipal updates.</p>
            </div>
            
            <div class="feature-card">
                <h3>📞 Contact Support</h3>
                <p>Get help anytime with our dedicated support team ready to assist you.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer animate-in delay-3">
            <p>&copy; 2024 Municipality of Impasugong. All Rights Reserved.</p>
            <p>Official Government Website | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </div>
</body>
</html>

