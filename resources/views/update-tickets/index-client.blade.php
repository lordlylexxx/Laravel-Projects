<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Support - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --cream: #F1F8E9; --white: #FFFFFF;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
        }
        @include('client.partials.top-navbar-styles')
        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }
        .page-shell { padding: calc(var(--client-nav-offset, 108px) + 24px) 24px 40px; max-width: 900px; margin: 0 auto; }
        .card {
            background: var(--white);
            border: 1px solid var(--green-soft);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 18px;
            box-shadow: 0 5px 20px rgba(27, 94, 32, 0.08);
        }
        h1 { font-size: 1.5rem; color: var(--green-dark); margin-bottom: 8px; }
        .sub { color: var(--gray-500); margin-bottom: 18px; font-size: 0.95rem; }
        label { display: block; font-weight: 600; margin-bottom: 6px; color: var(--gray-700); }
        input, textarea {
            width: 100%; max-width: 640px; padding: 10px 12px; border: 1px solid var(--gray-200);
            border-radius: 8px; margin-bottom: 14px; font-size: 0.95rem;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 9px;
            border: none; font-weight: 600; cursor: pointer; text-decoration: none; font-size: 0.92rem;
        }
        .btn.primary { background: var(--green-primary); color: #fff; }
        .flash { background: #ECFDF5; border: 1px solid #86EFAC; color: #166534; padding: 10px 12px; border-radius: 10px; margin-bottom: 16px; font-weight: 600; }
        .flash-error { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 10px 12px; border-radius: 10px; margin-bottom: 16px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid var(--gray-200); text-align: left; font-size: 0.9rem; }
        th { background: var(--green-white); color: var(--gray-700); font-size: 0.78rem; text-transform: uppercase; }
        .pill { display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; font-weight: 600; border-radius: 999px; padding: 5px 10px; }
        .pill.open { background: #DCFCE7; color: var(--green-dark); }
        .pill.resolved { background: #DBEAFE; color: #1D4ED8; }
        .pagination { margin-top: 16px; }
    </style>
</head>
<body>
    @include('client.partials.top-navbar', ['active' => 'update-tickets'])

    <main class="page-shell">
        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        <h1><i class="fas fa-life-ring"></i> Support</h1>
        <p class="sub">Submit issues about system updates or downloads. Central admin will review your ticket.</p>

        <section class="card">
            <h2 style="font-size:1.05rem; margin-bottom:12px; color:var(--gray-700);">New ticket</h2>
            <form method="POST" action="/update-tickets">
                @csrf
                <label for="subject">Subject</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" required maxlength="255">
                <label for="body">Details</label>
                <textarea id="body" name="body" rows="4" required maxlength="10000">{{ old('body') }}</textarea>
                @if ($errors->any())
                    <div class="flash-error">{{ $errors->first() }}</div>
                @endif
                <button type="submit" class="btn primary"><i class="fas fa-paper-plane"></i> Submit</button>
            </form>
        </section>

        <section class="card">
            <h2 style="font-size:1.05rem; margin-bottom:8px; color:var(--gray-700);">Your tickets</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->created_at?->format('M j, Y') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                            <td>
                                @if($ticket->status === \App\Models\UpdateTicket::STATUS_RESOLVED)
                                    <span class="pill resolved"><i class="fas fa-check"></i> Resolved</span>
                                @else
                                    <span class="pill open"><i class="fas fa-inbox"></i> Open</span>
                                @endif
                            </td>
                            <td><a href="/update-tickets/{{ $ticket->id }}" class="btn primary" style="padding:6px 12px;font-size:0.85rem;">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No tickets yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $tickets->links() }}</div>
        </section>
    </main>
</body>
</html>
