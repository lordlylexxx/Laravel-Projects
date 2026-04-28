<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Messages - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind = {
            config: {
                corePlugins: {
                    preflight: false,
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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
            @include('partials.tenant-theme-css-vars')
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

        @media (max-width: 768px) {
            @if($useLegacyMessagesNav)
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            @endif
        }

        /* Messages index: full-width shell, minimal side gutters, flex-fill split pane */
        body.owner-nav-page main.messages-index-main.main-content.with-owner-nav {
            max-width: none !important;
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: clamp(8px, 1vw, 16px) !important;
            padding-right: clamp(8px, 1vw, 16px) !important;
            padding-bottom: clamp(6px, 1vw, 12px) !important;
        }
    </style>
</head>
<body class="{{ $useOwnerNavbar ? 'owner-nav-page bg-gray-50 text-gray-800' : 'min-h-screen bg-gradient-to-br from-green-50 via-lime-50 to-white text-gray-800' }}">
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

    <main
        class="messages-index-main {{ $useOwnerNavbar ? 'main-content with-owner-nav flex w-full min-h-screen flex-col' : 'mx-auto flex min-h-screen w-full max-w-none flex-col px-3 pb-6 sm:px-4 lg:px-6' }}"
        @if(! $useOwnerNavbar) style="padding-top: calc(var(--client-nav-offset, 108px) + 12px);" @endif
    >
        <header class="mb-3 flex flex-shrink-0 flex-col gap-3 sm:mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold tracking-tight text-[var(--green-dark)] sm:text-3xl">Messages</h1>
                <p class="mt-1 text-sm text-gray-600 sm:text-base">Your conversations and inquiries</p>
            </div>
            @if($showComposeButton)
                <a
                    href="{{ route('messages.create', [], false) }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[var(--green-primary)] to-[var(--green-medium)] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-900/15 transition hover:brightness-105"
                >
                    <i class="fas fa-plus text-xs"></i>
                    New conversation
                </a>
            @endif
        </header>

        @if (session('success'))
            <div class="mb-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-[var(--green-dark)]">
                {{ session('success') }}
            </div>
        @endif

        @if(isset($messages) && count($messages) > 0)
            <div
                class="messages-split grid min-h-0 flex-1 grid-cols-1 gap-3 sm:gap-4 lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] lg:gap-4"
            >
                <aside class="flex min-h-[220px] flex-col overflow-hidden rounded-xl border border-green-100/80 bg-white shadow-sm shadow-green-900/5 sm:rounded-2xl lg:col-span-4 lg:h-full lg:min-h-0 xl:col-span-4">
                    <div class="flex flex-shrink-0 flex-wrap items-center justify-between gap-2 border-b border-green-100 bg-white px-3 py-3 sm:px-4 sm:py-3.5">
                        <h2 class="text-base font-semibold text-[var(--green-dark)]">Inbox</h2>
                        @if(($unreadCount ?? 0) > 0)
                            <form method="POST" action="{{ route('messages.mark-all-read', [], false) }}" class="m-0">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-lg border-2 border-[var(--green-primary)] bg-white px-3 py-1.5 text-xs font-semibold text-[var(--green-dark)] transition hover:bg-[var(--green-soft)]"
                                >
                                    Mark all as read
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain">
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
                            <a
                                href="{{ url('/messages') }}?partner={{ $otherId }}{{ request()->get('page') ? '&page='.(int) request()->get('page') : '' }}"
                                class="flex gap-3 border-b border-green-50 px-3 py-3 transition sm:gap-3 sm:px-4 sm:py-3.5 {{ $isActiveThread ? 'bg-[var(--green-white)]' : 'hover:bg-[var(--green-white)]/70' }} {{ $hasUnreadFromPartner ? 'border-l-4 border-l-[var(--green-primary)] bg-green-50/50' : '' }}"
                            >
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[var(--green-primary)] text-sm font-semibold text-white sm:h-12 sm:w-12">
                                    {{ strtoupper(substr($otherParty->name ?? 'U', 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-0.5 flex items-center justify-between gap-2">
                                        <span class="truncate text-sm font-semibold text-[var(--green-dark)]">{{ $otherParty->name ?? 'Unknown' }}</span>
                                        <span class="shrink-0 text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="truncate text-sm text-[var(--green-dark)]">{{ $message->subject ?? 'No Subject' }}</div>
                                    <div class="truncate text-xs text-gray-500">{{ Str::limit($message->content, 50) }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </aside>

                @php
                    $currentUserId = Auth::id();
                    $chatPartner = $selectedMessage
                        ? ((int) $selectedMessage->sender_id === (int) $currentUserId ? $selectedMessage->receiver : $selectedMessage->sender)
                        : null;
                @endphp

                <section class="flex min-h-[260px] flex-col overflow-hidden rounded-xl border border-green-100/80 bg-white shadow-sm shadow-green-900/5 sm:rounded-2xl lg:col-span-8 lg:h-full lg:min-h-0 xl:col-span-8">
                    <div class="flex flex-shrink-0 flex-wrap items-start justify-between gap-2 border-b border-green-100 bg-[var(--cream)] px-3 py-3 sm:gap-3 sm:px-4 sm:py-3.5 lg:px-5">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-semibold text-[var(--green-dark)]">{{ $chatPartner->name ?? 'Conversation' }}</h2>
                            <p class="mt-0.5 text-xs text-gray-600 sm:text-sm">{{ $chatPartner->email ?? 'Select a conversation from the inbox to start chatting.' }}</p>
                        </div>
                        @if($canDeleteSelectedConversation)
                            <form
                                method="POST"
                                action="{{ route('messages.destroy', $selectedMessage, false) }}"
                                class="m-0 shrink-0"
                                onsubmit="return confirm('Delete this entire conversation? This cannot be undone.');"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-lg border-2 border-red-700 bg-white px-3 py-1.5 text-xs font-semibold text-red-800 transition hover:bg-red-50"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain bg-gray-100 px-3 py-3 sm:px-4 sm:py-4 lg:px-5">
                        @forelse($conversationMessages as $chatMessage)
                            @php
                                $isMine = (int) $chatMessage->sender_id === (int) $currentUserId;
                                $senderName = $isMine ? 'You' : ($chatMessage->sender->name ?? 'User');
                            @endphp
                            <div class="mb-3 flex items-end gap-2 sm:mb-4 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                @if(! $isMine)
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[var(--green-dark)] text-xs font-bold text-white">
                                        {{ strtoupper(substr($senderName, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="max-w-[85%] sm:max-w-[74%]">
                                    <div
                                        class="rounded-2xl px-4 py-3 text-sm leading-relaxed sm:text-base {{ $isMine ? 'rounded-tr-md bg-gradient-to-br from-[var(--green-primary)] to-[var(--green-medium)] text-white' : 'rounded-tl-md bg-gray-200 text-gray-900' }}"
                                    >
                                        {{ $chatMessage->content }}
                                    </div>
                                    <p class="mt-1 text-[0.7rem] text-gray-500 {{ $isMine ? 'text-right' : '' }}">
                                        {{ $senderName }} · {{ $chatMessage->created_at->format('M d, h:i A') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="flex min-h-[200px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-white/80 px-4 py-12 text-center">
                                <h3 class="text-base font-semibold text-gray-700">No conversation yet</h3>
                                <p class="mt-2 max-w-sm text-sm text-gray-500">Open a thread from the inbox to view messages here.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($selectedMessage && $replyAnchorMessage)
                        <div class="flex-shrink-0 border-t border-green-100 bg-white px-3 py-3 sm:px-4 sm:py-3.5 lg:px-5">
                            <form method="POST" action="{{ route('messages.reply', $replyAnchorMessage, false) }}" class="flex flex-col gap-2.5 sm:gap-3">
                                @csrf
                                <textarea
                                    name="content"
                                    class="min-h-[80px] w-full resize-y rounded-xl border-2 border-green-100 px-3 py-2.5 text-sm outline-none transition focus:border-[var(--green-primary)] sm:min-h-[88px] sm:text-base"
                                    placeholder="Type your message..."
                                    required
                                ></textarea>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[var(--green-primary)] to-[var(--green-medium)] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-900/15 transition hover:brightness-105 sm:w-auto sm:min-w-[7.5rem]"
                                    >
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </section>
            </div>
        @else
            <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-green-100/80 bg-white px-6 py-20 text-center shadow-sm shadow-green-900/5 sm:py-28">
                <div class="mb-4 text-5xl sm:text-6xl" aria-hidden="true">💬</div>
                <h3 class="text-xl font-semibold text-gray-800">No messages yet</h3>
                <p class="mx-auto mt-3 max-w-md text-sm text-gray-600 sm:text-base">
                    You do not have any conversations yet.
                    @if($showComposeButton)
                        Start one with
                        @if($authUser?->isClient())
                            the owner or an administrator.
                        @else
                            a guest, a team member, or ImpaStay central support.
                        @endif
                    @endif
                </p>
                @if($showComposeButton)
                    <a
                        href="{{ route('messages.create', [], false) }}"
                        class="mt-8 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[var(--green-primary)] to-[var(--green-medium)] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-900/15 transition hover:brightness-105"
                    >
                        <i class="fas fa-plus text-xs"></i>
                        New conversation
                    </a>
                @endif
            </div>
        @endif
    </main>
</body>
</html>
