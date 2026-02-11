<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Impasugong Accommodations</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
        }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        .navbar { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .nav-logo { display: flex; align-items: center; gap: 15px; }
        .nav-logo img { width: 50px; height: 50px; border-radius: 50%; border: 2px solid var(--green-primary); }
        .nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .notification-btn { position: relative; background: none; border: none; cursor: pointer; font-size: 1.5rem; color: var(--green-primary); }
        .notification-badge { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.7rem; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        .main-content { padding-top: 90px; max-width: 1400px; margin: 0 auto; padding: 90px 40px 40px; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 8px; }
        .page-header p { color: var(--green-medium); }
        .messages-container { display: grid; grid-template-columns: 350px 1fr; gap: 25px; }
        .message-list { background: var(--white); border-radius: 15px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); overflow: hidden; }
        .message-list-header { padding: 20px; border-bottom: 1px solid var(--green-soft); }
        .message-list-header h3 { color: var(--green-dark); font-size: 1.1rem; }
        .message-item { display: flex; gap: 15px; padding: 20px; border-bottom: 1px solid var(--green-soft); cursor: pointer; transition: background 0.3s; }
        .message-item:hover, .message-item.active { background: var(--green-white); }
        .message-item.unread { background: rgba(46, 125, 50, 0.05); border-left: 4px solid var(--green-primary); }
        .message-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0; }
        .message-content { flex: 1; min-width: 0; }
        .message-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .message-sender { font-weight: 600; color: var(--green-dark); }
        .message-time { font-size: 0.8rem; color: var(--green-medium); }
        .message-subject { font-size: 0.9rem; color: var(--green-dark); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .message-preview { font-size: 0.85rem; color: var(--green-medium); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .message-detail { background: var(--white); border-radius: 15px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); }
        .message-detail-header { padding: 25px; border-bottom: 1px solid var(--green-soft); }
        .message-detail-header h2 { color: var(--green-dark); font-size: 1.4rem; margin-bottom: 10px; }
        .message-meta { display: flex; gap: 20px; color: var(--green-medium); font-size: 0.9rem; }
        .message-detail-body { padding: 25px; min-height: 300px; }
        .message-detail-body p { color: var(--green-dark); line-height: 1.8; }
        .reply-section { padding: 25px; border-top: 1px solid var(--green-soft); }
        .reply-textarea { width: 100%; padding: 15px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 1rem; resize: vertical; min-height: 100px; outline: none; margin-bottom: 15px; }
        .reply-textarea:focus { border-color: var(--green-primary); }
        .btn { padding: 12px 25px; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .empty-state { text-align: center; padding: 100px 20px; }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { color: var(--green-dark); margin-bottom: 10px; }
        @media (max-width: 768px) { .messages-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">
            <a href="{{ route('landing') }}" style="display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="/1.jpg" alt="Municipality Logo">
                <span>Impasugong</span>
            </a>
        </div>
        <div class="nav-actions">
            <a href="{{ route('messages.index') }}" class="notification-btn" style="text-decoration: none;">
                🔔
                @if(isset($unreadCount) && $unreadCount > 0)
                    <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <div class="nav-menu" style="display: flex; gap: 15px; align-items: center;">
                <a href="{{ route('dashboard') }}" style="padding: 8px 16px; background: var(--green-soft); color: var(--green-dark); border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                    Home
                </a>
                <div onclick="event.preventDefault(); document.getElementById('profile-form').submit();" style="cursor: pointer;">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="user-avatar" 
                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--green-primary);">
                    @else
                        <div class="user-avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    @endif
                </div>
                <form id="profile-form" action="{{ route('profile.edit') }}" method="GET" style="display: none;"></form>
                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" 
                   style="padding: 8px 16px; background: var(--green-dark); color: white; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
                   Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <div class="page-header">
            <h1>Messages</h1>
            <p>Your conversations and inquiries</p>
        </div>
        
        <div class="messages-container">
            <div class="message-list">
                <div class="message-list-header">
                    <h3>Inbox</h3>
                </div>
                <div class="message-item active">
                    <div class="message-avatar">JM</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Juan Miguel</span>
                            <span class="message-time">2h ago</span>
                        </div>
                        <div class="message-subject">Booking Inquiry - Mountain View Inn</div>
                        <div class="message-preview">Hi, I'd like to book this property for 3 nights...</div>
                    </div>
                </div>
                <div class="message-item unread">
                    <div class="message-avatar">SC</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Sarah Chen</span>
                            <span class="message-time">5h ago</span>
                        </div>
                        <div class="message-subject">Thank you for verifying!</div>
                        <div class="message-preview">Thank you so much for verifying my property...</div>
                    </div>
                </div>
                <div class="message-item">
                    <div class="message-avatar">RP</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Robert Perez</span>
                            <span class="message-time">1d ago</span>
                        </div>
                        <div class="message-subject">Check-in issue</div>
                        <div class="message-preview">I'm having trouble with the check-in process...</div>
                    </div>
                </div>
                <div class="message-item">
                    <div class="message-avatar">ML</div>
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">Maria Lopez</span>
                            <span class="message-time">2d ago</span>
                        </div>
                        <div class="message-subject">Extended stay request</div>
                        <div class="message-preview">Would it be possible to extend my stay by 2 more days?</div>
                    </div>
                </div>
            </div>
            
            <div class="message-detail">
                <div class="message-detail-header">
                    <h2>Booking Inquiry - Mountain View Inn</h2>
                    <div class="message-meta">
                        <span>From: Juan Miguel</span>
                        <span>•</span>
                        <span>juan@email.com</span>
                        <span>•</span>
                        <span>December 10, 2024</span>
                    </div>
                </div>
                <div class="message-detail-body">
                    <p>Hi there!</p>
                    <br>
                    <p>I came across your Mountain View Inn listing and I'm very interested in booking it for a weekend getaway. I wanted to inquire about availability for December 15-18, 2024.</p>
                    <br>
                    <p>There will be 2 adults and we're looking forward to experiencing the local hospitality. Could you please let me know if the property is available for those dates and if there are any additional fees or requirements?</p>
                    <br>
                    <p>Thank you so much for your time!</p>
                    <br>
                    <p>Best regards,<br>Juan Miguel</p>
                </div>
                <div class="reply-section">
                    <textarea class="reply-textarea" placeholder="Type your reply..."></textarea>
                    <button class="btn btn-primary">Send Reply</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

