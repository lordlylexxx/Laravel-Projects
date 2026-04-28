<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>New message - Impasugong Accommodations</title>
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
            $isClientComposer = $isClientComposer ?? false;
            $authUser = auth()->user();
            $isTenantAdmin = $authUser?->isAdmin() && \App\Models\Tenant::checkCurrent();
            $useOwnerNavbar = ! $isClientComposer && ($authUser?->isOwner() || $isTenantAdmin);
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }
        @if($useOwnerNavbar)
            @include('owner.partials.top-navbar-styles')
        @else
            @include('client.partials.top-navbar-styles')
        @endif

        /* Create message: full-width shell, minimal gutters (matches messages index) */
        body.owner-nav-page main.messages-create-main.main-content.with-owner-nav {
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
    @else
        @include('client.partials.top-navbar', ['active' => 'messages'])
    @endif

    <main
        class="messages-create-main {{ $useOwnerNavbar ? 'main-content with-owner-nav flex w-full min-h-screen flex-col' : 'mx-auto flex min-h-screen w-full max-w-none flex-col px-3 pb-6 sm:px-4 lg:px-6' }}"
        @if(! $useOwnerNavbar) style="padding-top: calc(var(--client-nav-offset, 108px) + 12px);" @endif
    >
        <a
            href="{{ route('messages.index', [], false) }}"
            class="mb-2 inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-[var(--green-primary)] transition hover:text-[var(--green-dark)] sm:mb-3"
        >
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Messages
        </a>

        <div
            class="messages-create-split grid min-h-0 flex-1 grid-cols-1 gap-3 sm:gap-4 lg:grid-cols-12 lg:grid-rows-[minmax(0,1fr)] lg:gap-4"
        >
            <aside class="flex flex-col justify-between rounded-xl border border-green-100/80 bg-white/95 p-4 shadow-sm shadow-green-900/5 sm:rounded-2xl sm:p-5 lg:col-span-4 lg:h-full lg:min-h-0 xl:col-span-3">
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-[var(--green-dark)] sm:text-xl md:text-2xl">
                        <i class="fas fa-pen mr-2 text-[var(--green-primary)]"></i>New conversation
                    </h1>
                    @if($isClientComposer)
                        <p class="mt-3 text-sm leading-relaxed text-gray-600 sm:mt-4">
                            Send a message to your <strong class="text-[var(--green-dark)]">property owner</strong> or a
                            <strong class="text-[var(--green-dark)]">business administrator</strong> for
                            {{ $currentTenant->name ?? 'this business' }}. They will see it in their Messages inbox.
                        </p>
                    @else
                        <p class="mt-3 text-sm leading-relaxed text-gray-600 sm:mt-4">
                            <strong class="text-[var(--green-dark)]">Owner</strong> and
                            <strong class="text-[var(--green-dark)]">tenant administrator</strong> use this screen the same way: message a
                            <strong class="text-[var(--green-dark)]">client</strong>, a <strong class="text-[var(--green-dark)]">team</strong> member, or
                            <strong class="text-[var(--green-dark)]">ImpaStay (central admin)</strong>. Prefer your
                            <strong class="text-[var(--green-dark)]">business (tenant) site</strong> so everything stays scoped to that business.
                        </p>
                        <p class="mt-2 text-xs leading-relaxed text-gray-500 sm:mt-3">
                            Optional email to staff: <code class="rounded bg-green-50 px-1 py-0.5 text-[11px] text-[var(--green-dark)]">IMPASTAY_CENTRAL_SUPPORT_NOTIFY_EMAIL</code>
                        </p>
                    @endif
                </div>
                <p class="mt-4 hidden text-xs text-gray-400 lg:block">Use the form to choose a recipient and write your message.</p>
            </aside>

            <section class="flex min-h-0 flex-col rounded-xl border border-green-100/80 bg-white shadow-md shadow-green-900/10 sm:rounded-2xl lg:col-span-8 lg:h-full lg:min-h-0 xl:col-span-9">
                @if($isClientComposer && $team->isEmpty())
                    <div class="flex flex-1 flex-col items-center justify-center p-6 text-center sm:p-8">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-600">
                            <i class="fas fa-user-slash text-xl"></i>
                        </div>
                        <p class="max-w-md text-sm font-medium text-red-800">
                            No owner or administrator is available to message yet. Please try again later or contact support.
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('messages.store', [], false) }}" class="flex min-h-0 flex-1 flex-col p-4 sm:p-5 lg:p-6">
                        @csrf

                        <div class="grid flex-shrink-0 gap-3 sm:grid-cols-2 sm:gap-4">
                            <div class="sm:col-span-2">
                                <label for="recipient_key" class="mb-1.5 block text-sm font-semibold text-gray-700 sm:mb-2">Recipient</label>
                                <select
                                    id="recipient_key"
                                    name="recipient_key"
                                    required
                                    class="w-full rounded-xl border-2 border-[var(--green-soft)] bg-white px-3 py-2.5 text-sm text-gray-800 transition focus:border-[var(--green-primary)] focus:outline-none sm:px-4 sm:py-3"
                                >
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
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="subject" class="mb-1.5 block text-sm font-semibold text-gray-700 sm:mb-2">
                                    Subject <span class="font-normal text-gray-500">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    value="{{ old('subject') }}"
                                    maxlength="255"
                                    placeholder="e.g. Question about my booking"
                                    class="w-full rounded-xl border-2 border-[var(--green-soft)] bg-white px-3 py-2.5 text-sm text-gray-800 transition focus:border-[var(--green-primary)] focus:outline-none sm:px-4 sm:py-3"
                                >
                                @error('subject')
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 flex min-h-0 flex-1 flex-col sm:mt-4">
                            <label for="content" class="mb-1.5 block flex-shrink-0 text-sm font-semibold text-gray-700 sm:mb-2">Message</label>
                            <textarea
                                id="content"
                                name="content"
                                required
                                placeholder="Write your message…"
                                class="min-h-[160px] w-full flex-1 resize-y rounded-xl border-2 border-[var(--green-soft)] bg-white px-3 py-2.5 text-sm leading-relaxed text-gray-800 transition focus:border-[var(--green-primary)] focus:outline-none sm:min-h-[200px] sm:px-4 sm:py-3"
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-2 flex-shrink-0 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4 flex flex-shrink-0 flex-col gap-2 border-t border-green-100 pt-4 sm:mt-5 sm:flex-row sm:flex-wrap sm:items-center sm:gap-3 sm:pt-5">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[var(--green-primary)] to-[var(--green-medium)] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-green-900/15 transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-55 sm:w-auto sm:px-6 sm:py-3"
                            >
                                <i class="fas fa-paper-plane text-xs"></i>
                                Send
                            </button>
                            <a
                                href="{{ route('messages.index', [], false) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[var(--green-soft)] px-5 py-2.5 text-sm font-semibold text-[var(--green-dark)] transition hover:bg-green-200/60 sm:w-auto sm:px-6 sm:py-3"
                            >
                                Cancel
                            </a>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </main>
</body>
</html>
