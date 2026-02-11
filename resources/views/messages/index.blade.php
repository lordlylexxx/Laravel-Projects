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
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        
        /* Navigation */
        .navbar { background: var(--white); padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--green-primary); }
        .nav-logo span { font-size: 1.2rem; font-weight: 700; color: var(--green-dark); }
        
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--green-soft); color: var(--green-dark); }
        
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        
        /* Main Content */
        .main-content { padding-top: 100px; max-width: 1400px; margin: 0 auto; padding: 100px 40px 40px; }
        
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        
        /* Messages Layout */
        .messages-container { display: grid; grid-template-columns: 350px 1fr; gap: 25px; }
        
        .message-list { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08); overflow: hidden; }
        .message-list-header { padding: 20px; border-bottom: 1px solid var(--green-soft); }
        .message-list-header h3 { color: var(--green-dark); font-size: 1.1rem; font-weight: 600; }
        
        .message-item { display: flex; gap: 15px; padding: 20px; border-bottom: 1px solid var(--green-soft); cursor: pointer; transition: background 0.3s; }
        .message-item:hover, .message-item.active { background: var(--green-white); }
        .message-item.unread { background: rgba(46, 125, 50, 0.05); border-left: 4px solid var(--green-primary); }
        .message-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0; font-size: 1rem; }
        .message-content { flex: 1; min-width: 0; }
        .message-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .message-sender { font-weight: 600; color: var(--green-dark); font-size: 0.95rem; }
        .message-time { font-size: 0.8rem; color: var(--gray-500); }
        .message-subject { font-size: 0.9rem; color: var(--green-dark); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .message-preview { font-size: 0.85rem; color: var(--gray-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .message-detail { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08); }
        .message-detail-header { padding: 25px; border-bottom: 1px solid var(--green-soft); }
        .message-detail-header h2 { color: var(--green-dark); font-size: 1.4rem; margin-bottom: 10px; }
        .message-meta { display: flex; gap: 15px; color: var(--gray-500); font-size: 0.9rem; flex-wrap: wrap; }
        .message-detail-body { padding: 25px; min-height: 300px; }
        .message-detail-body p { color: var(--gray-700); line-height: 1.8; font-size: 1rem; }
        
        .reply-section { padding: 25px; border-top: 1px solid var(--green-soft); }
        .reply-textarea { width: 100%; padding: 15px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 1rem; resize: vertical; min-height: 100px; outline: none; margin-bottom: 15px; font-family: inherit; }
        .reply-textarea:focus { border-color: var(--green-primary); }
        .btn { padding: 12px 25px; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        
        .empty-state { text-align: center; padding: 100px 20px; background: var(--white); border-radius: 16px; }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { color: var(--gray-700); margin-bottom: 10px; }
        
        @media (max-width: 768px) { 
            .navbar { padding: 15px 20px; }
            .nav-links { display: none; }
            .messages-container { grid-template-columns: 1fr; }
            .main-content { padding: 100px 20px 40px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/1.jpg" alt="Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Dashboard</a></li>
                @elseif(Auth::user()->isOwner())
                    <li><a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.*') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'active' : '' }}">Properties</a></li>
            <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: var(--green-soft); color: var(--green-dark); text-decoration: none; transition: all 0.3s;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="page-header">
            <h1>Messages</h1>
            <p>Your conversations and inquiries</p>
        </div>
        
        @if(isset($messages) && count($messages) > 0)
            <div class="messages-container">
                <!-- Message List -->
                <div class="message-list">
                    <div class="message-list-header">
                        <h3>Inbox</h3>
                    </div>
                    @foreach($messages as $message)
                        <div class="message-item {{ $message->is_unread ? 'unread' : '' }}" onclick="window.location='{{ route('messages.show', $message) }}'">
                            <div class="message-avatar">{{ substr($message->sender->name ?? 'U', 0, 2) }}</div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-sender">{{ $message->sender->name ?? 'Unknown' }}</span>
                                    <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="message-subject">{{ $message->subject ?? 'No Subject' }}</div>
                                <div class="message-preview">{{ Str::limit($message->content, 50) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Message Detail -->
                <div class="message-detail">
                    <div class="message-detail-header">
                        <h2>{{ $messages[0]->subject ?? 'Welcome to Messages' }}</h2>
                        <div class="message-meta">
                            <span>From: {{ $messages[0]->sender->name ?? 'Unknown' }}</span>
                            <span>•</span>
                            <span>{{ $messages[0]->sender->email ?? 'N/A' }}</span>
                            <span>•</span>
                            <span>{{ $messages[0]->created_at->format('F d, Y') }}</span>
                        </div>
                    </div>
                    <div class="message-detail-body">
                        <p>{{ $messages[0]->content ?? 'Select a message to view its contents.' }}</p>
                    </div>
                    <div class="reply-section">
                        <textarea class="reply-textarea" placeholder="Type your reply..."></textarea>
                        <button class="btn btn-primary">Send Reply</button>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="icon">💬</div>
                <h3>No Messages Yet</h3>
                <p>You haven't received any messages yet.</p>
            </div>
        @endif
    </main>
</body>
</html>

