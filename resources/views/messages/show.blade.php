<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Detail - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-700: #374151; --gray-800: #1F2937;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .main-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 100px 20px 40px;
        }

        body.owner-nav-page .main-content.with-owner-nav {
            padding-top: 100px;
        }

        .top-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px; flex-wrap: wrap; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { color: var(--green-dark); }

        .chat-panel {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            overflow: hidden;
        }
        .chat-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            background: var(--cream);
        }
        .chat-title {
            font-size: 1.2rem;
            color: var(--green-dark);
            font-weight: 700;
            margin-bottom: 4px;
        }
        .chat-subtitle {
            font-size: 0.85rem;
            color: var(--gray-500);
        }
        .chat-body {
            background: #f3f4f6;
            min-height: 380px;
            max-height: 560px;
            overflow-y: auto;
            padding: 20px;
        }
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
            font-size: 1rem;
            line-height: 1.45;
            word-break: break-word;
        }
        .bubble-incoming { background: #e5e7eb; color: #111827; border-top-left-radius: 8px; }
        .bubble-outgoing { background: #4361ee; color: var(--white); border-top-right-radius: 8px; }
        .bubble-meta { margin-top: 4px; font-size: 0.72rem; color: var(--gray-500); }
        .bubble-row.outgoing .bubble-meta { text-align: right; }

        .chat-composer {
            padding: 16px 20px;
            border-top: 1px solid var(--gray-200);
            background: var(--white);
        }
        .reply-textarea {
            width: 100%;
            min-height: 86px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            padding: 12px;
            resize: vertical;
            font-size: 0.95rem;
            margin-bottom: 10px;
            outline: none;
            font-family: inherit;
        }
        .reply-textarea:focus { border-color: var(--green-primary); }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
            font-weight: 600;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .main-content { padding: 86px 15px 30px; }
        }

        @if(auth()->user()?->isOwner())
            @include('owner.partials.top-navbar-styles')
        @elseif(auth()->user()?->isClient())
            @include('client.partials.top-navbar-styles')
        @elseif(auth()->user()?->isAdmin())
            @include('admin.partials.top-navbar-styles')
        @endif
    </style>
</head>
<body class="{{ auth()->user()?->isOwner() ? 'owner-nav-page' : '' }}">
    @if(auth()->user()?->isOwner())
        @include('owner.partials.top-navbar')
    @elseif(auth()->user()?->isClient())
        @include('client.partials.top-navbar', ['active' => 'messages'])
    @elseif(auth()->user()?->isAdmin())
        @include('admin.partials.top-navbar', ['active' => 'messages'])
    @else
    <nav class="navbar">
        <a href="{{ route('admin.dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>ImpaStay</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('messages.index') }}" class="active">Messages</a></li>
        </ul>
        <div class="nav-actions">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif

    @php
        $currentUserId = Auth::id();
        $conversation = collect($thread ?? [])->push($message)->sortBy('created_at')->values();
        $chatPartner = (int) $message->sender_id === (int) $currentUserId ? $message->receiver : $message->sender;
    @endphp

    <main class="main-content {{ auth()->user()?->isOwner() ? 'with-owner-nav' : '' }}">
        <div class="top-actions">
            <a href="{{ route('messages.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Messages</a>
        </div>

        <section class="chat-panel">
            <div class="chat-header">
                <div class="chat-title">{{ $chatPartner->name ?? 'Conversation' }}</div>
                <div class="chat-subtitle">{{ $chatPartner->email ?? 'Chat thread' }}</div>
            </div>

            <div class="chat-body">
                @foreach($conversation as $item)
                    @php
                        $isMine = (int) $item->sender_id === (int) $currentUserId;
                        $senderName = $isMine ? 'You' : ($item->sender->name ?? 'User');
                    @endphp
                    <div class="bubble-row {{ $isMine ? 'outgoing' : 'incoming' }}">
                        @if(!$isMine)
                            <div class="bubble-avatar">{{ strtoupper(substr($senderName, 0, 1)) }}</div>
                        @endif
                        <div class="bubble-wrap">
                            <div class="bubble {{ $isMine ? 'bubble-outgoing' : 'bubble-incoming' }}">{{ $item->content }}</div>
                            <div class="bubble-meta">{{ $senderName }} · {{ $item->created_at->format('M d, h:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="chat-composer">
                <form method="POST" action="{{ route('messages.reply', $message) }}">
                    @csrf
                    <textarea name="content" class="reply-textarea" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn">Send</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
