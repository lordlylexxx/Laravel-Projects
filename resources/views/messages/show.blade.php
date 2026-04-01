<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Detail - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @php
            $authUser = auth()->user();
            $isTenantAdmin = $authUser?->isAdmin() && \App\Models\Tenant::checkCurrent();
            $useOwnerNavbar = $authUser?->isOwner() || $isTenantAdmin;
            $useLegacyMessagesNav = ! $useOwnerNavbar && ! $authUser?->isClient() && ! $authUser?->isAdmin();
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
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
        @endif

        @if($useOwnerNavbar)
            @include('owner.partials.top-navbar-styles')
        @elseif($authUser?->isClient())
            @include('client.partials.top-navbar-styles')
        @elseif($authUser?->isAdmin())
            @include('admin.partials.top-navbar-styles')
        @endif

        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .main-content {
            max-width: 1100px;
            margin: 0 auto;
            padding-top: var(--client-nav-offset, 100px);
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 40px;
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
            @if($useLegacyMessagesNav)
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            @endif
            .main-content {
                padding-top: calc(var(--client-nav-offset, 100px) - 14px);
                padding-left: 15px;
                padding-right: 15px;
                padding-bottom: 30px;
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
    @php
        $adminDashboardHref = \App\Models\Tenant::checkCurrent() ? '/owner/dashboard' : '/admin/dashboard';
    @endphp
    <nav class="navbar">
        <a href="{{ $adminDashboardHref }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>ImpaStay</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ $adminDashboardHref }}">Dashboard</a></li>
            <li><a href="/messages" class="active">Messages</a></li>
        </ul>
        <div class="nav-actions">
            <form action="/logout" method="POST">
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

    <main class="main-content {{ $useOwnerNavbar ? 'with-owner-nav' : '' }}">
        <div class="top-actions">
            <a href="/messages" class="back-link"><i class="fas fa-arrow-left"></i> Back to Messages</a>
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
                <form method="POST" action="/messages/{{ $message->id }}/reply">
                    @csrf
                    <textarea name="content" class="reply-textarea" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn">Send</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
