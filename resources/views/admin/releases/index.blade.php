<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.partials.favicon')
    <title>System Updates — Release Registry</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @include('admin.partials.admin-shell-styles')
        /* Match other CA admin pages: full-width main under fixed navbar */
        .releases-main-content {
            max-width: none;
        }
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'updates'])

    <div class="dashboard-layout">
        <main class="main-content releases-main-content">
            <div class="w-full">
                    {{-- Page header --}}
                    <header class="mb-6 flex shrink-0 flex-col gap-4 border-b border-emerald-100/80 pb-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 space-y-1">
                            <h1 class="text-2xl font-bold tracking-tight text-emerald-950 sm:text-3xl">
                                Global release registry
                            </h1>
                            <p class="max-w-3xl text-sm leading-relaxed text-slate-600 sm:text-base">
                                Track GitHub releases and tenant adoption across Tulogans. Sync pulls tags and published releases into this registry.
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-3">
                            <form
                                method="POST"
                                action="{{ route('admin.releases.sync.post', [], false) }}"
                                class="inline-flex"
                                data-swal-confirm
                                data-swal-icon="question"
                                data-swal-title="Sync from GitHub?"
                                data-swal-text="This will fetch the latest releases and tags from GitHub and update the registry. Continue?"
                                data-swal-confirm-button="Yes, sync now"
                                data-swal-cancel-button="Cancel"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-900/15 transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                >
                                    <i class="fas fa-rotate" aria-hidden="true"></i>
                                    Sync from GitHub
                                </button>
                            </form>
                        </div>
                    </header>

                    @if (session('success'))
                        <div
                            class="mb-6 shrink-0 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900"
                            role="status"
                        >
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div
                            class="mb-6 shrink-0 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-900"
                            role="alert"
                        >
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Stats --}}
                    <section class="mb-8 shrink-0">
                        <h2 class="sr-only">Update statistics</h2>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 xl:grid-cols-4 2xl:grid-cols-7">
                            @php
                                $statTiles = [
                                    ['label' => 'Total tenants', 'value' => $stats['total_tenants'] ?? 0, 'hint' => null],
                                    ['label' => 'On latest (stable)', 'value' => $stats['tenants_on_latest'] ?? 0, 'hint' => null],
                                    ['label' => 'Pending latest', 'value' => $stats['tenants_pending_latest'] ?? 0, 'hint' => null],
                                    ['label' => 'Required overdue', 'value' => $stats['tenants_required_overdue'] ?? 0, 'hint' => null],
                                    ['label' => 'Failed updates', 'value' => $stats['tenants_with_failed_updates'] ?? 0, 'hint' => null],
                                    ['label' => 'Latest stable tag', 'value' => $stats['latest_release_tag'] ?? 'N/A', 'hint' => 'Rollups use highest stable semver in registry'],
                                    ['label' => 'Newest in registry', 'value' => $stats['newest_registry_tag'] ?? 'N/A', 'hint' => 'Highest semver (any stability)'],
                                ];
                            @endphp
                            @foreach ($statTiles as $tile)
                                <div class="rounded-xl border border-emerald-100/90 bg-white/90 p-4 shadow-sm ring-1 ring-black/5 backdrop-blur-sm">
                                    <div class="text-[0.65rem] font-semibold uppercase tracking-wider text-slate-500">
                                        {{ $tile['label'] }}
                                    </div>
                                    <div class="mt-1 truncate text-lg font-bold text-emerald-900 sm:text-xl" title="{{ is_scalar($tile['value']) ? (string) $tile['value'] : '' }}">
                                        {{ $tile['value'] }}
                                    </div>
                                    @if (! empty($tile['hint']))
                                        <div class="mt-1 text-[0.65rem] leading-snug text-slate-500">
                                            {{ $tile['hint'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>

                    {{-- Releases (fills remaining width) --}}
                    <section class="flex min-h-0 flex-1 flex-col gap-4" aria-labelledby="releases-heading">
                        <h2 id="releases-heading" class="text-lg font-semibold text-emerald-950">
                            Releases
                        </h2>

                        <div class="flex min-h-0 flex-1 flex-col gap-4">
                            @forelse ($releases as $release)
                                <article class="rounded-2xl border border-emerald-100/90 bg-white p-5 shadow-sm ring-1 ring-black/5 sm:p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0 space-y-1">
                                            <p class="font-mono text-base font-bold text-emerald-900 sm:text-lg">
                                                {{ $release->tag }}
                                            </p>
                                            <p class="text-sm font-semibold text-slate-700 sm:text-base">
                                                {{ $release->title }}
                                            </p>
                                            <p class="text-xs text-slate-500 sm:text-sm">
                                                Published:
                                                {{ optional($release->published_at)->format('M d, Y h:i A') ?: 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="flex shrink-0 flex-wrap gap-2">
                                            @if ($release->is_required)
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900 ring-1 ring-amber-200/80">
                                                    Required
                                                </span>
                                            @endif
                                            @if (! $release->is_stable)
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700 ring-1 ring-slate-200/80">
                                                    Pre-release
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:flex-wrap sm:items-center">
                                        <form
                                            class="flex flex-wrap items-center gap-2"
                                            method="POST"
                                            action="{{ route('admin.releases.required', ['release' => $release], false) }}"
                                            data-swal-confirm
                                            data-swal-title="Mark release as required?"
                                            data-swal-text="Tenants not yet on {{ $release->tag }} will be flagged with a required update (using the grace period you set). Continue?"
                                            data-swal-confirm-button="Yes, mark required"
                                            data-swal-cancel-button="Cancel"
                                        >
                                            @csrf
                                            <label class="sr-only" for="grace-{{ $release->getKey() }}">Grace days</label>
                                            <input
                                                id="grace-{{ $release->getKey() }}"
                                                class="w-20 rounded-lg border border-slate-300 px-2 py-2 text-sm text-slate-800 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                                                type="number"
                                                name="grace_days"
                                                min="0"
                                                max="60"
                                                value="7"
                                            >
                                            <button
                                                type="submit"
                                                class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-amber-950 shadow-sm hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 sm:text-sm"
                                            >
                                                Mark required
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.releases.notify-all', ['release' => $release], false) }}"
                                            data-swal-confirm
                                            data-swal-title="Notify all tenants?"
                                            data-swal-text="This creates update-available records for {{ $release->tag }} for every tenant that does not already have one. Continue?"
                                            data-swal-confirm-button="Yes, notify all"
                                            data-swal-cancel-button="Cancel"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 sm:w-auto sm:text-sm"
                                            >
                                                Notify all
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.releases.force-mark-all-updated', ['release' => $release], false) }}"
                                            data-swal-confirm
                                            data-swal-icon="warning"
                                            data-swal-title="Force mark all tenants updated?"
                                            data-swal-text="Every tenant will be recorded as running {{ $release->tag }}, regardless of their real state. This cannot be undone from here. Continue?"
                                            data-swal-confirm-button="Yes, force mark"
                                            data-swal-cancel-button="Cancel"
                                            data-swal-confirm-color="#b91c1c"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1 sm:w-auto sm:text-sm"
                                            >
                                                Force mark all updated
                                            </button>
                                        </form>

                                        @if ($release->release_url)
                                            <a
                                                href="{{ $release->release_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-800 hover:bg-blue-100 sm:w-auto sm:text-sm"
                                            >
                                                Open GitHub release
                                            </a>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-white/60 px-6 py-16 text-center text-slate-600">
                                    <p class="max-w-md text-sm sm:text-base">
                                        No releases synced yet. Use <strong class="text-emerald-900">Sync from GitHub</strong> to import tags and published releases.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <div class="mt-8 border-t border-emerald-100/80 pt-6">
                        {{ $releases->onEachSide(1)->links('admin.releases.pagination') }}
                    </div>
            </div>
        </main>
    </div>
    @include('partials.sweetalert-form-confirm')
</body>
</html>
