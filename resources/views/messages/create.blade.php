<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New message - Impasugong Accommodations</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @php
            $isClientComposer = $isClientComposer ?? false;
            $authUser = auth()->user();
            $isTenantAdmin = $authUser?->isAdmin() && \App\Models\Tenant::checkCurrent();
            $useOwnerNavbar = ! $isClientComposer && ($authUser?->isOwner() || $isTenantAdmin);
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        @if($useOwnerNavbar)
            @include('owner.partials.top-navbar-styles')
        @else
            @include('client.partials.top-navbar-styles')
        @endif
        body { font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif); background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        .main-content {
            max-width: 640px;
            margin: 0 auto;
            padding-left: 40px;
            padding-right: 40px;
            padding-bottom: 40px;
        }
        .compose-client-main { padding-top: calc(var(--client-nav-offset, 108px) + 24px); }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 18px;
            font-size: 0.9rem;
        }
        .back-link:hover { color: var(--green-dark); }
        .compose-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(27, 94, 32, 0.08);
            padding: 28px;
        }
        .compose-card h1 { font-size: 1.35rem; color: var(--green-dark); margin-bottom: 8px; }
        .compose-card .hint { color: var(--gray-500); font-size: 0.9rem; margin-bottom: 22px; line-height: 1.5; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 600; color: var(--gray-700); margin-bottom: 8px; font-size: 0.9rem; }
        .form-group select, .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
        }
        .form-group textarea { min-height: 140px; resize: vertical; }
        .form-group select:focus, .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--green-primary);
        }
        .error { color: #B91C1C; font-size: 0.85rem; margin-top: 6px; }
        .btn-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
        .btn {
            padding: 12px 22px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .btn-primary:disabled { opacity: 0.55; cursor: not-allowed; }
        @media (max-width: 768px) {
            .main-content { padding-left: 20px; padding-right: 20px; }
        }
    </style>
</head>
<body class="{{ $useOwnerNavbar ? 'owner-nav-page' : '' }}">
    @if($useOwnerNavbar)
        @include('owner.partials.top-navbar', ['active' => 'messages'])
    @else
        @include('client.partials.top-navbar', ['active' => 'messages'])
    @endif

    <main class="main-content {{ $useOwnerNavbar ? 'with-owner-nav' : 'compose-client-main' }}">
        <a href="{{ route('messages.index', [], false) }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Messages</a>

        <div class="compose-card">
            <h1><i class="fas fa-pen" style="margin-right: 8px;"></i>New conversation</h1>
            @if($isClientComposer)
                <p class="hint">Send a message to your <strong>property owner</strong> or a <strong>business administrator</strong> for {{ $currentTenant->name ?? 'this business' }}. They will see it in their Messages inbox.</p>
            @else
                <p class="hint"><strong>Owner</strong> and <strong>tenant administrator</strong> use this screen the same way: message a <strong>client</strong>, a <strong>team</strong> member, or <strong>ImpaStay (central admin)</strong>. Prefer your <strong>business (tenant) site</strong> so everything stays scoped to that business. Optional email to staff: <code>IMPASTAY_CENTRAL_SUPPORT_NOTIFY_EMAIL</code>.</p>
            @endif

            @if($isClientComposer && $team->isEmpty())
                <p class="hint" style="color:#B91C1C;">No owner or administrator is available to message yet. Please try again later or contact support.</p>
            @else
            <form method="POST" action="{{ route('messages.store', [], false) }}">
                @csrf
                <div class="form-group">
                    <label for="recipient_key">Recipient</label>
                    <select id="recipient_key" name="recipient_key" required>
                        <option value="" disabled {{ old('recipient_key') ? '' : 'selected' }}>Choose a recipient…</option>
                        @if(! $isClientComposer)
                            <option value="central" {{ old('recipient_key') === 'central' ? 'selected' : '' }}>ImpaStay (Central Admin)</option>
                            @if($clients->isNotEmpty())
                                <optgroup label="Clients">
                                    @foreach($clients as $client)
                                        <option value="user:{{ $client->id }}" {{ old('recipient_key') === 'user:'.$client->id ? 'selected' : '' }}>
                                            {{ $client->name }} — {{ $client->email }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endif
                        @if($team->isNotEmpty())
                            <optgroup label="{{ $isClientComposer ? 'Owner & administrators' : 'Team' }}">
                                @foreach($team as $member)
                                    <option value="user:{{ $member->id }}" {{ old('recipient_key') === 'user:'.$member->id ? 'selected' : '' }}>
                                        {{ $member->name }} — {{ $member->role === 'admin' ? 'Admin' : 'Owner' }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    @error('recipient_key')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span style="font-weight:400;color:var(--gray-500);">(optional)</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" maxlength="255" placeholder="e.g. Question about my booking">
                    @error('subject')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Message</label>
                    <textarea id="content" name="content" required placeholder="Write your message…">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button>
                    <a href="{{ route('messages.index', [], false) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            @endif
        </div>
    </main>
</body>
</html>
