<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Thread — {{ $tenant->name }} — Central Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-soft: #C8E6C9; --green-white: #E8F5E9; --cream: #F1F8E9; --white: #FFFFFF;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-700: #374151; --gray-800: #1F2937;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        .dashboard-layout { padding-top: 82px; }
        .main-content { padding: 28px 36px; max-width: 900px; margin: 0 auto; }
        .flash {
            background: #ECFDF5; border: 1px solid #86EFAC; color: #166534;
            padding: 10px 12px; border-radius: 10px; margin-bottom: 16px; font-weight: 600;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 8px; color: var(--green-primary);
            text-decoration: none; font-weight: 600; margin-bottom: 18px;
        }
        .back-link:hover { color: var(--green-dark); }
        .chat-panel {
            background: var(--white); border-radius: 14px; border: 1px solid var(--green-soft);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); overflow: hidden;
        }
        .chat-header {
            padding: 18px 22px; border-bottom: 1px solid var(--gray-200); background: var(--cream);
        }
        .chat-header h1 { font-size: 1.2rem; color: var(--green-dark); margin-bottom: 4px; }
        .chat-header p { font-size: 0.88rem; color: var(--gray-500); }
        .chat-body { background: #f3f4f6; padding: 20px; min-height: 280px; max-height: 480px; overflow-y: auto; }
        .bubble-row { display: flex; margin-bottom: 14px; }
        .bubble-row.in { justify-content: flex-start; }
        .bubble-row.out { justify-content: flex-end; }
        .bubble {
            max-width: 78%; padding: 12px 16px; border-radius: 14px; line-height: 1.45;
            font-size: 0.95rem; white-space: pre-wrap; word-break: break-word;
        }
        .bubble.in { background: #e5e7eb; color: #111827; border-bottom-left-radius: 4px; }
        .bubble.out { background: #2563EB; color: var(--white); border-bottom-right-radius: 4px; }
        .bubble-meta { margin-top: 6px; font-size: 0.75rem; color: var(--gray-500); }
        .bubble-row.out .bubble-meta { text-align: right; color: rgba(255,255,255,0.85); }
        .reply-area { padding: 16px 20px; border-top: 1px solid var(--gray-200); background: var(--white); }
        .reply-area textarea {
            width: 100%; min-height: 100px; padding: 12px; border-radius: 10px;
            border: 2px solid var(--green-soft); font-family: inherit; font-size: 0.95rem; margin-bottom: 12px;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
            border-radius: 10px; font-weight: 600; border: none; cursor: pointer;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white);
        }
        .thread-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-top: 12px; }
        .btn-delete {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px;
            border-radius: 10px; font-weight: 600; cursor: pointer;
            border: 2px solid #B91C1C; background: var(--white); color: #991B1B;
        }
        .btn-delete:hover { background: #FEE2E2; }
        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'messages'])

    <div class="dashboard-layout">
        <main class="main-content">
            <a href="{{ route('admin.messages', [], false) }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to inbox</a>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <div class="chat-panel">
                <div class="chat-header">
                    <h1>{{ $message->subject ?: 'Message' }}</h1>
                    <p>Tulogan: <strong>{{ $tenant->name }}</strong> · You reply as <strong>ImpaStay (Central Admin)</strong></p>
                </div>
                <div class="chat-body">
                    @foreach ($timeline as $m)
                        @php
                            $fromCentral = (int) $m->sender_id === (int) $proxy->id;
                        @endphp
                        <div class="bubble-row {{ $fromCentral ? 'out' : 'in' }}">
                            <div>
                                <div class="bubble {{ $fromCentral ? 'out' : 'in' }}">{{ $m->content }}</div>
                                <div class="bubble-meta">
                                    {{ $fromCentral ? 'ImpaStay (Central Admin)' : ($m->sender->name ?? 'User') }}
                                    · {{ $m->created_at->format('M j, g:i A') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="reply-area">
                    <form method="POST" action="{{ route('admin.messages.support-reply', ['tenant' => $tenant->getKey(), 'message' => $message->getKey()], false) }}">
                        @csrf
                        <textarea name="content" required placeholder="Write a reply…"></textarea>
                        <button type="submit" class="btn"><i class="fas fa-reply"></i> Send reply</button>
                    </form>
                    <div class="thread-actions">
                        <form method="POST" action="{{ route('admin.messages.destroy', ['tenant' => $tenant->getKey(), 'message' => $message->getKey()], false) }}"
                              onsubmit="return confirm('Delete this entire support thread? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i> Delete thread</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
