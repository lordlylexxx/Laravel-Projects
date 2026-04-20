<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Messages - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @php
            $authUser = auth()->user();
            $isTenantAdmin = $authUser?->isAdmin() && \App\Models\Tenant::checkCurrent();
            $useOwnerNavbar = $authUser?->isOwner() || $isTenantAdmin;
            $useLegacyMessagesNav = ! $useOwnerNavbar && ! $authUser?->isClient() && ! $authUser?->isAdmin();
            $showComposeButton = $useOwnerNavbar || ($authUser?->isClient() && \App\Models\Tenant::checkCurrent());
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        @if($useLegacyMessagesNav)
        .navbar { background: var(--white); padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1); position: fixed; width: 100%; top: 0; left: 0; right: 0; z-index: 1000; }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
        .nav-logo span { font-size: 1.2rem; font-weight: 700; color: var(--green-dark); }
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 8px 12px; border-radius: 8px; transition: all 0.3s; }
        .nav-links a:hover, .nav-links a.active { background: var(--green-soft); color: var(--green-dark); }
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; }
        .nav-btn.primary { background: var(--green-primary); color: var(--white); }
        .nav-btn.secondary { background: var(--green-soft); color: var(--green-dark); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--green-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        @endif

        @if($useOwnerNavbar)
            @include('owner.partials.top-navbar-styles')
        @elseif($authUser?->isClient())
            @include('client.partials.top-navbar-styles')
        @elseif($authUser?->isAdmin())
            @include('admin.partials.top-navbar-styles')
        @endif

        body { font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif); background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        
        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: var(--client-nav-offset, 100px);
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 40px;
        }
        
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 5px; }
        .page-header p { color: var(--gray-500); }
        .page-header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .page-header-row .page-header-text { flex: 1; min-width: 200px; }
        .btn-compose {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(46, 125, 50, 0.25);
        }
        .btn-compose:hover { filter: brightness(1.05); }
        
        /* Messages Layout */
        .messages-container { display: grid; grid-template-columns: 350px 1fr; gap: 25px; }
        
        .message-list { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08); overflow: hidden; }
        .message-list-header { padding: 20px; border-bottom: 1px solid var(--green-soft); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .message-list-header h3 { color: var(--green-dark); font-size: 1.1rem; font-weight: 600; margin: 0; }
        .mark-all-read-form { margin: 0; }
        .mark-all-read-btn {
            padding: 8px 14px;
            border-radius: 8px;
            border: 2px solid var(--green-primary);
            background: var(--white);
            color: var(--green-dark);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .mark-all-read-btn:hover { background: var(--green-soft); }
        .flash-banner { margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; font-weight: 500; font-size: 0.95rem; }
        .flash-banner.success { background: var(--green-soft); color: var(--green-dark); }
        
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
        
        .message-detail { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08); overflow: hidden; display: flex; flex-direction: column; }
        .chat-header { padding: 20px 24px; border-bottom: 1px solid var(--green-soft); background: var(--cream); }
        .chat-header h2 { color: var(--green-dark); font-size: 1.15rem; margin-bottom: 4px; }
        .chat-header p { color: var(--gray-500); font-size: 0.85rem; }

        .chat-body { padding: 20px; min-height: 360px; max-height: 520px; overflow-y: auto; background: #f3f4f6; }
        .bubble-row { display: flex; align-items: flex-end; gap: 10px; margin-bottom: 12px; }
        .bubble-row.incoming { justify-content: flex-start; }
        .bubble-row.outgoing { justify-content: flex-end; }
        .bubble-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--green-dark);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .bubble-wrap { max-width: 74%; }
        .bubble {
            border-radius: 18px;
            padding: 12px 16px;
            line-height: 1.45;
            font-size: 1rem;
            word-break: break-word;
        }
        .bubble-incoming { background: #e5e7eb; color: #111827; border-top-left-radius: 8px; }
        .bubble-outgoing { background: #4361ee; color: var(--white); border-top-right-radius: 8px; }
        .bubble-meta { margin-top: 4px; font-size: 0.72rem; color: var(--gray-500); }
        .bubble-row.outgoing .bubble-meta { text-align: right; }

        .reply-section { padding: 16px 20px; border-top: 1px solid var(--green-soft); background: var(--white); }
        .reply-textarea { width: 100%; padding: 12px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 0.95rem; resize: vertical; min-height: 86px; outline: none; margin-bottom: 10px; font-family: inherit; }
        .reply-textarea:focus { border-color: var(--green-primary); }
        .btn { padding: 12px 25px; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .chat-header-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
        .chat-header-row .chat-header-text { flex: 1; min-width: 0; }
        .delete-conversation-form { margin: 0; }
        .btn-delete {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid #B91C1C;
            background: var(--white);
            color: #991B1B;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-delete:hover { background: #FEE2E2; }
        
        .empty-state { text-align: center; padding: 100px 20px; background: var(--white); border-radius: 16px; }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state h3 { color: var(--gray-700); margin-bottom: 10px; }
        
        @media (max-width: 768px) {
            @if($useLegacyMessagesNav)
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            @endif
            .messages-container { grid-template-columns: 1fr; }
            .main-content {
                padding-top: calc(var(--client-nav-offset, 100px) - 10px);
                padding-left: 20px;
                padding-right: 20px;
                padding-bottom: 40px;
            }
        }
    </style>
</head>
<body class="{{ $useOwnerNavbar ? 'owner-nav-page' : '' }}">
    @if($useOwnerNavbar)
        @include('owner.partials.top-navbar', ['active' => 'messages'])
    @elseif($authUser?->isClient())
        @include('client.partials.top-navbar', ['active' => 'messages'])
    @elseif($authUser?->isAdmin())
        @include('admin.partials.top-navbar', ['active' => 'messages'])
    @else
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>Impasugong</span>
        </a>
        
        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'active' : '' }}">Browse</a></li>
            @if(! Auth::user()->isClient() || Auth::user()->tenantClientMayManageOwnStays())
                <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            @endif
            @if(! Auth::user()->isClient() || Auth::user()->tenantClientMayUseMessaging())
                <li><a href="{{ route('messages.index', [], false) }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
            @endif
            @if(! Auth::user()->isClient() || Auth::user()->tenantClientMayEditOwnProfile())
                <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Settings</a></li>
            @endif
        </ul>
        
        <div class="nav-actions">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif
    
    <main class="main-content {{ $useOwnerNavbar ? 'with-owner-nav' : '' }}">
        <div class="page-header page-header-row">
            <div class="page-header-text">
                <h1>Messages</h1>
                <p>Your conversations and inquiries</p>
            </div>
            @if($showComposeButton)
                <a href="{{ route('messages.create', [], false) }}" class="btn-compose"><i class="fas fa-plus"></i> New conversation</a>
            @endif
        </div>

        @if (session('success'))
            <div class="flash-banner success">{{ session('success') }}</div>
        @endif
        
        @if(isset($messages) && count($messages) > 0)
            <div class="messages-container">
                <!-- Message List -->
                <div class="message-list">
                    <div class="message-list-header">
                        <h3>Inbox</h3>
                        @if(($unreadCount ?? 0) > 0)
                            <form method="POST" action="{{ route('messages.mark-all-read', [], false) }}" class="mark-all-read-form">
                                @csrf
                                <button type="submit" class="mark-all-read-btn">Mark all as read</button>
                            </form>
                        @endif
                    </div>
                    @foreach($messages as $message)
                        @php
                            $otherParty = (int) $message->sender_id === (int) Auth::id()
                                ? $message->receiver
                                : $message->sender;
                            $otherId = (int) ($otherParty->id ?? 0);
                            $selectedOtherId = $selectedMessage
                                ? ((int) $selectedMessage->sender_id === (int) Auth::id()
                                    ? (int) $selectedMessage->receiver_id
                                    : (int) $selectedMessage->sender_id)
                                : null;
                            $isActiveThread = $selectedOtherId !== null && $otherId === $selectedOtherId;
                            $hasUnreadFromPartner = $otherId > 0 && ($unreadByPartner[$otherId] ?? false);
                        @endphp
                        <div class="message-item {{ $hasUnreadFromPartner ? 'unread' : '' }} {{ $isActiveThread ? 'active' : '' }}"
                             onclick="window.location='{{ url('/messages') }}?partner={{ $otherId }}{{ request()->get('page') ? '&page='.(int) request()->get('page') : '' }}'">
                            <div class="message-avatar">{{ strtoupper(substr($otherParty->name ?? 'U', 0, 2)) }}</div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-sender">{{ $otherParty->name ?? 'Unknown' }}</span>
                                    <span class="message-time">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="message-subject">{{ $message->subject ?? 'No Subject' }}</div>
                                <div class="message-preview">{{ Str::limit($message->content, 50) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Message Detail -->
                @php
                    $currentUserId = Auth::id();
                    $chatPartner = $selectedMessage
                        ? ((int) $selectedMessage->sender_id === (int) $currentUserId ? $selectedMessage->receiver : $selectedMessage->sender)
                        : null;
                @endphp

                <div class="message-detail">
                    <div class="chat-header chat-header-row">
                        <div class="chat-header-text">
                            <h2>{{ $chatPartner->name ?? 'Conversation' }}</h2>
                            <p>{{ $chatPartner->email ?? 'Select a conversation to start chatting.' }}</p>
                        </div>
                        @if($canDeleteSelectedConversation)
                            <form method="POST" action="{{ route('messages.destroy', $selectedMessage, false) }}" class="delete-conversation-form"
                                  onsubmit="return confirm('Delete this entire conversation? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i> Delete</button>
                            </form>
                        @endif
                    </div>

                    <div class="chat-body">
                        @forelse($conversationMessages as $chatMessage)
                            @php
                                $isMine = (int) $chatMessage->sender_id === (int) $currentUserId;
                                $senderName = $isMine ? 'You' : ($chatMessage->sender->name ?? 'User');
                            @endphp
                            <div class="bubble-row {{ $isMine ? 'outgoing' : 'incoming' }}">
                                @if(!$isMine)
                                    <div class="bubble-avatar">{{ strtoupper(substr($senderName, 0, 1)) }}</div>
                                @endif
                                <div class="bubble-wrap">
                                    <div class="bubble {{ $isMine ? 'bubble-outgoing' : 'bubble-incoming' }}">{{ $chatMessage->content }}</div>
                                    <div class="bubble-meta">{{ $senderName }} · {{ $chatMessage->created_at->format('M d, h:i A') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="padding: 40px 20px; box-shadow: none; border-radius: 12px;">
                                <h3>No conversation yet</h3>
                                <p>Open a message from the inbox to start chatting.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($selectedMessage && $replyAnchorMessage)
                        <div class="reply-section">
                            <form method="POST" action="{{ route('messages.reply', $replyAnchorMessage, false) }}">
                                @csrf
                                <textarea name="content" class="reply-textarea" placeholder="Type your message..." required></textarea>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="icon">💬</div>
                <h3>No Messages Yet</h3>
                <p>You don't have any conversations yet.@if($showComposeButton)
                    Start one with @if($authUser?->isClient()) the owner or an administrator.@else a guest, a team member, or ImpaStay central support.@endif
                @endif</p>
                @if($showComposeButton)
                    <p style="margin-top: 20px;">
                        <a href="{{ route('messages.create', [], false) }}" class="btn-compose" style="display: inline-flex;"><i class="fas fa-plus"></i> New conversation</a>
                    </p>
                @endif
            </div>
        @endif
    </main>
</body>
</html>

