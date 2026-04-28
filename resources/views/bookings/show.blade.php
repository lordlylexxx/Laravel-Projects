<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>Booking Details - Impasugong Accommodations</title>
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
            $currentTenant = \App\Models\Tenant::current();
            $isTenantManager = $authUser && (
                $authUser->isOwner()
                || ($authUser->isAdmin() && $currentTenant && (int) $authUser->tenant_id === (int) $currentTenant->id)
            );
            $useLegacyBookingsNav = ! $isTenantManager && ! $authUser?->isClient();
        @endphp
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-500: #6B7280; --gray-600: #4B5563; --gray-700: #374151;
            --gray-800: #1F2937;
        }

        @if($useLegacyBookingsNav)
        .navbar {
            background: var(--white);
            padding: 0 40px;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(27, 94, 32, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
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
        @endif

        @if($isTenantManager)
            @include('owner.partials.top-navbar-styles')
        @elseif($authUser?->isClient())
            @include('client.partials.top-navbar-styles')
        @endif

        body {
            font-family: var(--client-nav-font, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            min-height: 100vh;
            color: var(--gray-800);
        }
    </style>
</head>
<body class="{{ $isTenantManager ? 'owner-nav-page' : '' }} flex min-h-screen flex-col bg-gradient-to-br from-emerald-50 via-lime-50/90 to-emerald-100 text-slate-900 antialiased">
    @if($isTenantManager)
        @include('owner.partials.top-navbar')
    @elseif(auth()->user()?->isClient())
        @include('client.partials.top-navbar', ['active' => 'bookings'])
    @else
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="/SYSTEMLOGO.png" alt="ImpaStay Logo">
            <span>Impasugong</span>
        </a>

        <ul class="nav-links">
            @auth
                @if(Auth::user()->isAdmin())
                    @php
                        $adminDashboardHref = \App\Models\Tenant::checkCurrent() ? '/owner/dashboard' : '/admin/dashboard';
                    @endphp
                    <li><a href="{{ $adminDashboardHref }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Dashboard</a></li>
                @elseif(Auth::user()->isOwner())
                    <li><a href="{{ route('owner.dashboard') }}" class="{{ request()->routeIs('owner.*') ? 'active' : '' }}">Dashboard</a></li>
                @else
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Browse</a></li>
                @endif
            @endauth
            <li><a href="{{ route(Auth::check() && $isTenantManager && \Illuminate\Support\Facades\Route::has('owner.accommodations.index') ? 'owner.accommodations.index' : (\Illuminate\Support\Facades\Route::has('accommodations.index') ? 'accommodations.index' : 'dashboard')) }}" class="{{ request()->routeIs('accommodations.*') || request()->routeIs('owner.accommodations.*') ? 'active' : '' }}">Browse</a></li>
            <li><a href="{{ Auth::check() && $isTenantManager ? route('owner.bookings.index') : route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') || request()->routeIs('owner.bookings.*') ? 'active' : '' }}">My Bookings</a></li>
            <li><a href="{{ route('messages.index', [], false) }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages</a></li>
            <li><a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Settings</a></li>
        </ul>

        <div class="nav-actions">
            <form action="{{ route('profile.edit') }}" method="GET">
                @csrf
                <button type="submit" class="nav-btn secondary">⚙️ Settings</button>
            </form>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="nav-btn primary">Logout</button>
            </form>
        </div>
    </nav>
    @endif

    @php
        $bookingsIndexRoute = Auth::check() && $isTenantManager ? 'owner.bookings.index' : 'bookings.index';
    @endphp

    <main
        class="flex w-full flex-1 flex-col {{ $isTenantManager ? 'main-content with-owner-nav' : '' }} min-h-0 min-h-[calc(100dvh-5rem)] px-4 pb-10 pt-6 sm:px-6 lg:min-h-[calc(100dvh-6rem)] lg:px-8"
        @if(! $isTenantManager) style="padding-top: calc(var(--client-nav-offset, 108px) + 16px);" @endif
    >
        <div class="mx-auto flex w-full max-w-[1920px] flex-1 flex-col gap-4">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-900 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <a
                href="{{ route($bookingsIndexRoute, [], false) }}"
                class="inline-flex w-max items-center gap-2 text-sm font-semibold text-emerald-800 transition hover:text-emerald-950"
            >
                <i class="fas fa-arrow-left text-xs"></i>
                Back to My Bookings
            </a>

            @if(Auth::check() && $isTenantManager)
                <section class="rounded-2xl border border-emerald-100 bg-white/95 p-4 shadow-sm sm:p-5">
                    <h3 class="mb-3 text-base font-bold text-emerald-900">GCash QR Code</h3>
                    @if($currentTenant?->getGcashQrUrl())
                        <div class="mb-3 flex flex-wrap items-center gap-3">
                            <img
                                src="{{ $currentTenant->getGcashQrUrl() }}"
                                alt="GCash QR"
                                class="h-24 w-24 rounded-xl border border-slate-200 object-cover"
                            >
                            <form method="POST" action="{{ route('owner.bookings.payment-settings.gcash-qr.remove', [], false) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                                    Remove QR
                                </button>
                            </form>
                        </div>
                    @endif
                    <form
                        method="POST"
                        action="{{ route('owner.bookings.payment-settings.gcash-qr.upload', [], false) }}"
                        enctype="multipart/form-data"
                        class="flex flex-wrap items-center gap-3"
                    >
                        @csrf
                        <input
                            type="file"
                            name="gcash_qr"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            required
                            class="max-w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-700"
                        >
                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                            {{ $currentTenant?->getGcashQrUrl() ? 'Replace' : 'Upload' }} GCash QR Photo
                        </button>
                    </form>
                    @error('gcash_qr')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            @if(isset($booking))
                @php
                    $paymentUi = $booking->payment_ui_state;
                    $paymentToneClass = $paymentUi['tone'] === 'pending_review' ? 'bg-amber-50 text-amber-900 ring-amber-200' : ($paymentUi['tone'] === 'paid' ? 'bg-emerald-50 text-emerald-900 ring-emerald-200' : 'bg-slate-100 text-slate-800 ring-slate-200');
                @endphp
                <article class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-emerald-100/90 bg-white shadow-lg">
                    <header class="bg-gradient-to-r from-emerald-900 to-emerald-700 px-5 py-6 text-white sm:px-8 sm:py-8">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-emerald-100">Booking #{{ $booking->id }}</p>
                                <h1 class="mt-1 text-2xl font-bold leading-tight sm:text-3xl">
                                    {{ $booking->accommodation->name ?? 'N/A' }}
                                </h1>
                                <p class="mt-2 text-sm text-emerald-100">
                                    Booked on {{ $booking->created_at->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                                <span class="inline-flex rounded-full bg-white px-4 py-1.5 text-sm font-bold text-emerald-900 shadow-sm">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $paymentToneClass }}">
                                    {{ $paymentUi['label'] }}
                                </span>
                            </div>
                        </div>
                    </header>

                    <div class="flex flex-1 flex-col gap-8 p-5 sm:p-8 lg:gap-10">
                        <div class="flex flex-col gap-6 border-b border-slate-100 pb-8 lg:flex-row lg:gap-10">
                            <div class="shrink-0 lg:w-72">
                                @if($booking->accommodation && $booking->accommodation->primary_image)
                                    <img
                                        src="{{ $booking->accommodation->primary_image_url }}"
                                        alt="{{ $booking->accommodation->name }}"
                                        class="h-48 w-full rounded-2xl object-cover shadow-md ring-1 ring-slate-200/80 lg:h-56"
                                    >
                                @else
                                    <img
                                        src="/COMMUNAL.jpg"
                                        alt="Property"
                                        class="h-48 w-full rounded-2xl object-cover shadow-md ring-1 ring-slate-200/80 lg:h-56"
                                    >
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-xl font-bold text-slate-900 sm:text-2xl">
                                    {{ $booking->accommodation->name ?? 'N/A' }}
                                </h2>
                                <p class="mt-2 flex items-start gap-2 text-sm text-slate-600">
                                    <i class="fas fa-location-dot mt-0.5 text-emerald-600"></i>
                                    {{ $booking->accommodation->address ?? 'Impasugong, Bukidnon' }}
                                </p>
                                <p class="mt-4 text-sm leading-relaxed text-slate-600">
                                    {{ $booking->accommodation->description ?? 'No description available.' }}
                                </p>
                            </div>
                        </div>

                        <section>
                            <h3 class="mb-4 text-lg font-bold text-emerald-900">Booking details</h3>
                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Check-in</p>
                                    <p class="mt-1 text-base font-bold text-slate-900">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }}</p>
                                </div>
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Check-out</p>
                                    <p class="mt-1 text-base font-bold text-slate-900">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</p>
                                </div>
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Guests</p>
                                    <p class="mt-1 text-base font-bold text-slate-900">{{ $booking->number_of_guests ?? 1 }} guest(s)</p>
                                </div>
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Type</p>
                                    <p class="mt-1 text-base font-bold text-slate-900">{{ ucfirst(str_replace('-', ' ', $booking->accommodation->type ?? 'Standard')) }}</p>
                                </div>
                            </div>
                        </section>

                        <section class="grid gap-4 lg:grid-cols-3">
                            <div class="rounded-2xl bg-gradient-to-br from-emerald-900 to-emerald-700 p-6 text-white shadow-inner lg:col-span-2">
                                <p class="text-sm text-emerald-100">Total amount</p>
                                <p class="mt-1 text-3xl font-bold tracking-tight sm:text-4xl">₱{{ number_format($booking->total_price, 2) }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Price per night</p>
                                <p class="mt-2 text-2xl font-bold text-slate-900">
                                    ₱{{ number_format($booking->accommodation->price_per_night ?? 0, 2) }}
                                </p>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment summary</p>
                            <p class="mt-1 text-lg font-bold text-slate-900">{{ $paymentUi['label'] }}</p>
                            <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <dt class="text-xs text-slate-500">Channel</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['channel'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Method</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['method'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Reference</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['reference'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Paid at</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['paid_at'] ? $paymentUi['paid_at']->format('M d, Y h:i A') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Proof submitted</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['submitted_at'] ? $paymentUi['submitted_at']->format('M d, Y h:i A') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Reviewed at</dt>
                                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $paymentUi['reviewed_at'] ? $paymentUi['reviewed_at']->format('M d, Y h:i A') : 'N/A' }}</dd>
                                </div>
                            </dl>
                        </section>

                        @if(Auth::check() && $isTenantManager)
                            <section>
                                <h3 class="mb-3 text-lg font-bold text-emerald-900">Client payment proof</h3>
                                @if($booking->gcash_payment_proof_url)
                                    <a href="{{ $booking->gcash_payment_proof_url }}" target="_blank" rel="noopener noreferrer" class="inline-block">
                                        <img
                                            src="{{ $booking->gcash_payment_proof_url }}"
                                            alt="Payment proof"
                                            class="max-h-64 max-w-full rounded-xl border border-slate-200 object-contain shadow-sm"
                                        >
                                    </a>
                                @else
                                    <p class="text-sm text-slate-500">No proof screenshot uploaded yet.</p>
                                @endif
                            </section>
                        @endif

                        @if(isset($booking->messages) && count($booking->messages) > 0)
                            <section class="border-t border-slate-100 pt-8">
                                <h3 class="mb-4 text-lg font-bold text-emerald-900">Conversation</h3>
                                <ul class="space-y-3">
                                    @foreach($booking->messages as $message)
                                        <li class="rounded-xl border border-emerald-100/80 bg-emerald-50/40 p-4 shadow-sm">
                                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                                <span class="font-semibold text-emerald-900">{{ $message->sender->name ?? 'Unknown' }}</span>
                                                <time class="text-xs text-slate-500">{{ $message->created_at->format('M d, Y h:i A') }}</time>
                                            </div>
                                            <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $message->content }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif

                        @if(Auth::check() && $isTenantManager && $booking->status === 'pending')
                            <div class="flex flex-wrap gap-3 border-t border-slate-100 pt-8">
                                <form action="{{ route('owner.bookings.update-status', $booking, false) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800">
                                        Approve booking
                                    </button>
                                </form>
                                <form action="{{ route('owner.bookings.update-status', $booking, false) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button
                                        type="submit"
                                        class="rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-red-700"
                                        onclick="return confirm('Decline this booking request?')"
                                    >
                                        Decline booking
                                    </button>
                                </form>
                            </div>
                        @elseif(Auth::check() && Auth::user()->isClient() && ($booking->status == 'pending' || $booking->status == 'confirmed'))
                            @php
                                $isPaymentRecorded = ($paymentUi['tone'] ?? 'neutral') === 'paid';
                            @endphp
                            <div class="flex flex-col flex-wrap gap-3 border-t border-slate-100 pt-8 sm:flex-row sm:items-center">
                                @if($booking->status === 'pending')
                                    <p class="w-full text-sm text-slate-600 sm:max-w-xl">
                                        Please pay first (Stripe or GCash proof upload), then the host will review and approve.
                                    </p>
                                    @if(! $isPaymentRecorded)
                                        <a
                                            href="{{ route('bookings.payment', $booking, false) }}"
                                            class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800"
                                        >
                                            Pay / Upload proof
                                        </a>
                                    @endif
                                @endif
                                @if($booking->status === 'confirmed' && ! $isPaymentRecorded)
                                    <a
                                        href="{{ route('bookings.payment', $booking, false) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800"
                                    >
                                        Pay via Stripe
                                    </a>
                                @endif
                                <form action="{{ route('bookings.cancel', $booking, false) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button
                                        type="submit"
                                        class="rounded-xl border-2 border-red-200 bg-white px-6 py-3 text-sm font-bold text-red-700 shadow-sm transition hover:bg-red-50"
                                        onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')"
                                    >
                                        Cancel booking
                                    </button>
                                </form>
                                <a
                                    href="{{ route('messages.index', [], false) }}"
                                    class="inline-flex items-center justify-center rounded-xl border-2 border-emerald-600 bg-white px-6 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50"
                                >
                                    Contact host
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @else
                <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-white/90 p-12 text-center shadow-sm">
                    <h2 class="text-xl font-bold text-slate-800">Booking not found</h2>
                    <p class="mt-2 max-w-md text-sm text-slate-600">The booking you are looking for does not exist or has been removed.</p>
                    <a
                        href="{{ route($bookingsIndexRoute, [], false) }}"
                        class="mt-6 inline-flex rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800"
                    >
                        Back to My Bookings
                    </a>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
