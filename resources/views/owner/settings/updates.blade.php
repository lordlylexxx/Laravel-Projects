<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.tenant-favicon')
    <title>Updates - Owner Settings</title>
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
        :root {
            @include('partials.tenant-theme-css-vars')
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }

        @include('owner.partials.top-navbar-styles')

        body.owner-nav-page {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--gray-800);
            background: linear-gradient(135deg, #E8F5E9 0%, #F1F8E9 50%, #C8E6C9 100%);
        }
        .tenant-updates-shell {
            width: min(1440px, 100%);
            margin: 0 auto;
            padding: var(--owner-content-offset) clamp(12px, 2vw, 28px) 24px;
            min-height: calc(100vh - var(--owner-content-offset));
        }
    </style>
</head>
<body class="owner-nav-page">
    @include('owner.partials.top-navbar', ['active' => 'updates'])
    <main class="tenant-updates-shell with-owner-nav">
        <div class="h-full space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                <h1 class="text-3xl font-bold text-green-900">Updates</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">Applying an update downloads the release, runs central post-install steps, migrates this tenant’s database, and re-syncs roles and permissions with the <strong>currently deployed</strong> server layout. Deploy new application code on the host before tenants apply.</p>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-white/80 px-3 py-2 text-xs text-slate-600">
                    Available releases: <span class="font-semibold text-slate-800">{{ $availableReleases->count() }}</span>
                    @if(! empty($newestAvailableTag))
                        <span class="mx-1 text-slate-400">|</span>
                        Latest to apply: <span class="font-semibold text-emerald-800">{{ $newestAvailableTag }}</span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                <section class="rounded-2xl border border-emerald-100 bg-white/95 p-5 shadow-sm xl:col-span-4">
                    <h2 class="text-lg font-semibold text-slate-900">Current Release</h2>
                    @if($currentRelease)
                        <div class="mt-3 space-y-1 text-sm text-slate-600">
                            <p class="text-base font-semibold text-emerald-800">{{ $currentRelease->tag }} <span class="font-medium text-slate-700">- {{ $currentRelease->title }}</span></p>
                            <p>Published: {{ optional($currentRelease->published_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                            <p>Applied: {{ optional($currentTenantUpdate?->applied_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-slate-500">No current release assigned yet.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-emerald-100 bg-white/95 p-5 shadow-sm xl:col-span-8">
                    <h2 class="text-lg font-semibold text-slate-900">Available Updates</h2>
                    <div class="mt-3 space-y-4">
                        @forelse($availableReleases as $release)
                            <article class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-base font-semibold text-slate-900">{{ $release->tag }}</span>
                                    <span class="text-sm text-slate-600">{{ $release->title }}</span>
                                    @if($release->is_required)
                                        <span class="rounded-full border border-amber-300 bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Required</span>
                                    @endif
                                </div>

                                <p class="mt-2 text-xs text-slate-500">Published: {{ optional($release->published_at)->format('M d, Y h:i A') ?: 'N/A' }}</p>

                                @if(filled($release->changelog))
                                    <div class="mt-3 rounded-lg border border-slate-200 bg-white p-3">
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Release notes</p>
                                        <div class="max-h-44 overflow-y-auto whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-700">{!! nl2br(e($release->changelog)) !!}</div>
                                    </div>
                                @endif

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <form
                                        method="POST"
                                        action="{{ route('settings.updates.apply', [], false) }}"
                                        data-swal-confirm
                                        data-swal-icon="question"
                                        data-swal-title="Apply this update?"
                                        data-swal-text="You are about to apply {{ $release->tag }} ({{ $release->title }}) for this tenant. Only continue if the server already has this release deployed (or you accept the self-update installer copying it in)."
                                        data-swal-confirm-button="Yes, apply update"
                                        data-swal-cancel-button="Cancel"
                                    >
                                        @csrf
                                        <input type="hidden" name="release_id" value="{{ $release->id }}">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800"
                                        >
                                            Apply Update Now
                                        </button>
                                    </form>
                                    @if($release->release_url)
                                        <a
                                            href="{{ $release->release_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                                        >
                                            View Release
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-slate-500">No updates available.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>
    @include('partials.sweetalert-form-confirm')
</body>
</html>
