<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging — Central Admin</title>
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
        .main-content { padding: 28px 36px; max-width: 1200px; margin: 0 auto; }
        .page-header { margin-bottom: 20px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); margin-bottom: 6px; }
        .page-header p { color: var(--gray-500); max-width: 720px; line-height: 1.5; }
        .flash {
            background: #ECFDF5; border: 1px solid #86EFAC; color: #166534;
            padding: 10px 12px; border-radius: 10px; margin-bottom: 16px; font-weight: 600;
        }
        .card {
            background: var(--white); border-radius: 14px; border: 1px solid var(--green-soft);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); overflow: hidden; margin-bottom: 22px;
        }
        .card-header { padding: 16px 20px; border-bottom: 1px solid var(--green-soft); }
        .card-header h2 { font-size: 1.1rem; color: var(--green-dark); }
        .card-body { padding: 20px; }
        label { display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; font-size: 0.9rem; }
        input, select, textarea {
            width: 100%; max-width: 480px; padding: 10px 12px; border-radius: 8px;
            border: 1px solid var(--gray-200); font-size: 0.95rem; margin-bottom: 14px; font-family: inherit;
        }
        textarea { min-height: 120px; max-width: 100%; }
        .error { color: #B91C1C; font-size: 0.85rem; margin-top: -10px; margin-bottom: 10px; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
            border-radius: 10px; font-weight: 600; border: none; cursor: pointer;
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white);
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.92rem; }
        th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid var(--gray-200); vertical-align: top; }
        th { color: var(--green-dark); font-weight: 700; background: var(--green-white); }
        tr:hover td { background: #fafafa; }
        .badge-unread {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #EF4444;
        }
        .link-open {
            color: var(--green-primary); font-weight: 600; text-decoration: none;
        }
        .link-open:hover { text-decoration: underline; }
        .muted { color: var(--gray-500); font-size: 0.85rem; }
        .pagination { padding: 16px 20px; border-top: 1px solid var(--green-soft); }
        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'messages'])

    <div class="dashboard-layout">
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-envelope"></i> Tulogan messaging</h1>
                <p>
                    Conversations where a tulogan messaged <strong>ImpaStay (Central Admin)</strong>, or you started a thread from here.
                    The inbox shows <strong>one row per person</strong> per tulogan (latest message in that thread). Messages are stored in each tulogan’s database; use the recipient’s <strong>email</strong> exactly as on their account.
                </p>
            </div>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-paper-plane"></i> New message to a tulogan user</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.messages.contact', [], false) }}">
                        @csrf
                        <label for="tenant_id">Tulogan</label>
                        <select name="tenant_id" id="tenant_id" required>
                            <option value="">— Select —</option>
                            @foreach ($tenants as $t)
                                <option value="{{ $t->id }}" @selected(old('tenant_id') == $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>

                        <label for="recipient_email">Recipient email</label>
                        <input type="email" name="recipient_email" id="recipient_email" value="{{ old('recipient_email') }}" required placeholder="owner@example.com">
                        @error('recipient_email')
                            <div class="error">{{ $message }}</div>
                        @enderror

                        <label for="subject">Subject <span class="muted">(optional)</span></label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" maxlength="255">

                        <label for="content">Message</label>
                        <textarea name="content" id="content" required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="error">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-inbox"></i> Inbox</h2>
                </div>
                @if ($paginator->isEmpty())
                    <div class="card-body">
                        <p class="muted">No support threads yet. When a tulogan writes to ImpaStay (Central Admin), it will appear here.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>When</th>
                                <th>Tulogan</th>
                                <th>With</th>
                                <th>Preview</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator as $row)
                                <tr>
                                    <td style="width:24px;">
                                        @if ($row->is_unread)
                                            <span class="badge-unread" title="Unread"></span>
                                        @endif
                                    </td>
                                    <td class="muted">{{ $row->created_at->format('M j, Y g:i A') }}</td>
                                    <td><strong>{{ $row->tenant_name }}</strong></td>
                                    <td>{{ $row->counterpart_name }}</td>
                                    <td>
                                        @if ($row->subject)
                                            <strong>{{ $row->subject }}</strong><br>
                                        @endif
                                        <span class="muted">{{ $row->preview }}</span>
                                    </td>
                                    <td>
                                        <a class="link-open" href="{{ route('admin.messages.thread', ['tenant' => $row->tenant_id, 'message' => $row->message_id], false) }}">Open</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{ $paginator->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
