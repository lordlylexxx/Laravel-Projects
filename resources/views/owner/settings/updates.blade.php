<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Updates - Owner Settings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-soft: #C8E6C9; --green-white: #E8F5E9; --cream: #F1F8E9; --white: #FFFFFF;
            --gray-200: #E5E7EB; --gray-500: #6B7280; --gray-700: #374151; --gray-800: #1F2937;
        }
        @include('owner.partials.top-navbar-styles')
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            color: var(--gray-800);
            min-height: 100vh;
        }
        .main-content {
            max-width: 980px;
            margin: 0 auto;
            padding: var(--owner-content-offset) 22px 28px;
        }
        .page-header { margin-bottom: 18px; }
        .page-header h1 { font-size: 1.8rem; color: var(--green-dark); margin-bottom: 4px; }
        .page-header p { color: var(--gray-500); }
        .card { background: var(--white); border: 1px solid var(--green-soft); border-radius: 14px; padding: 16px; margin-bottom: 14px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); }
        .muted { color: var(--gray-500); font-size: 0.9rem; }
        .ok { background:#ecfdf5; border:1px solid #86efac; color:#166534; padding:10px; border-radius:10px; margin-bottom:12px; }
        .err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:10px; border-radius:10px; margin-bottom:12px; }
        .btn { border:none; border-radius:10px; padding:10px 12px; cursor:pointer; font-weight:700; background:linear-gradient(135deg, var(--green-primary), var(--green-medium)); color:#fff; }
        .btn-secondary { background:#334155; text-decoration:none; display:inline-flex; align-items:center; }
        .row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        form { margin: 0; }
        .pill { background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:999px; font-size:0.8rem; }
    </style>
</head>
<body class="owner-nav-page">
    @include('owner.partials.top-navbar', ['active' => 'updates'])
    <main class="main-content with-owner-nav">
    <div class="page-header">
        <h1>Updates</h1>
        <p class="muted">Track and apply app releases for this tenant.</p>
    </div>

    @if(session('success'))
        <div class="ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="err">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h3 style="margin-top:0;">Current Release</h3>
        @if($currentRelease)
            <div><strong>{{ $currentRelease->tag }}</strong> — {{ $currentRelease->title }}</div>
            <div class="muted">Published: {{ optional($currentRelease->published_at)->format('M d, Y h:i A') ?: 'N/A' }}</div>
            <div class="muted">Applied: {{ optional($currentTenantUpdate?->applied_at)->format('M d, Y h:i A') ?: 'N/A' }}</div>
        @else
            <div class="muted">No current release assigned yet.</div>
        @endif
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Available Updates</h3>
        @forelse($availableReleases as $release)
            <div style="padding:10px 0; border-bottom:1px solid #e2e8f0;">
                <div class="row">
                    <strong>{{ $release->tag }}</strong>
                    <span class="muted">{{ $release->title }}</span>
                    @if($release->is_required)
                        <span class="pill">Required</span>
                    @endif
                </div>
                <div class="muted">Published: {{ optional($release->published_at)->format('M d, Y h:i A') ?: 'N/A' }}</div>
                <div class="row" style="margin-top:8px;">
                    <form method="POST" action="{{ route('settings.updates.apply') }}">
                        @csrf
                        <input type="hidden" name="release_id" value="{{ $release->id }}">
                        <button class="btn" type="submit">Apply Update Now</button>
                    </form>
                    @if($release->release_url)
                        <a href="{{ $release->release_url }}" target="_blank" class="btn btn-secondary" style="text-decoration:none;">View Release</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="muted">No updates available.</div>
        @endforelse
    </div>
    </main>
</body>
</html>
