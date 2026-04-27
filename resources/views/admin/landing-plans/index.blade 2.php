<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>Plan management</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@include('admin.partials.admin-shell-styles')</style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'landing-plans'])

    <div class="dashboard-layout">
        <main class="main-content">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <div class="page-header-row">
                <div class="page-header">
                    <h1><i class="fas fa-tags" style="margin-right:8px;opacity:0.9;"></i>Plan management</h1>
                    <p>Marketing cards on the central landing page. Edit each plan to set titles, prices, and features; leave optional fields empty to use the standard defaults for that tier.</p>
                </div>
                <a href="{{ route('admin.landing-plans.create') }}" class="btn-admin-primary"><i class="fas fa-plus"></i> Add plan</a>
            </div>

            <div class="card">
                <div class="overflow-x-auto border-t border-emerald-100/90 bg-white">
                    <table class="min-w-[720px] w-full border-collapse text-left text-sm text-gray-800">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gradient-to-r from-emerald-50 via-green-50/90 to-emerald-50/70">
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Order</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Tier key</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Resolved (landing)</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Feature mode</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Visible</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Featured</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($plans as $plan)
                                <tr class="align-top transition-colors hover:bg-gray-50/80">
                                    <td class="px-4 py-4">{{ $plan->sort_order }}</td>
                                    <td class="px-4 py-4"><code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">{{ $plan->tenant_plan_key }}</code></td>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900">{{ $plan->effectiveTitle() }}</p>
                                        @php $ep = $plan->effectivePrice(); @endphp
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $plan->effectiveCurrency() }}{{ number_format($ep, floor($ep) == $ep ? 0 : 2) }}</p>
                                        <ul class="mt-2 list-none space-y-1 text-xs text-gray-700">
                                            @foreach(array_slice($plan->effectiveFeatures(), 0, 4) as $line)
                                                <li class="flex items-start gap-2">
                                                    <span class="mt-0.5 inline-flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full border border-emerald-200 bg-white text-[8px] text-emerald-700">
                                                        <i class="fa-solid fa-check"></i>
                                                    </span>
                                                    <span>{{ \Illuminate\Support\Str::limit($line, 56) }}</span>
                                                </li>
                                            @endforeach
                                            @if(count($plan->effectiveFeatures()) > 4)
                                                <li class="pl-5 text-gray-500">…</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="px-4 py-4 text-xs text-gray-500">
                                        @if($plan->featureSelectionMode() === 'custom_pick')
                                            Features: chosen bullets
                                        @elseif($plan->featureSelectionMode() === 'full_catalog')
                                            Features: full checklist
                                        @else
                                            Features: tier catalog
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col items-start gap-2">
                                            @if($plan->is_visible)
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-900 ring-1 ring-inset ring-emerald-600/15">Visible</span>
                                                <form action="{{ route('admin.landing-plans.toggle-visibility', $plan) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-admin-sm btn-admin-sm-amber" title="Remove this card from the public CA landing">Hide</button>
                                                </form>
                                            @else
                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-800 ring-1 ring-inset ring-red-600/15">Hidden</span>
                                                <form action="{{ route('admin.landing-plans.toggle-visibility', $plan) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-admin-sm btn-admin-sm-mint" title="Show this card on the public CA landing">Unhide</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($plan->is_featured)
                                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-900 ring-1 ring-inset ring-amber-600/15">Yes</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-bold text-gray-700 ring-1 ring-inset ring-gray-500/12">No</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <a href="{{ route('admin.landing-plans.edit', $plan) }}" class="btn-admin-sm btn-admin-sm-outline" style="margin-right:6px;">Edit</a>
                                        <form action="{{ route('admin.landing-plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Delete this landing plan row?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-admin-sm btn-admin-sm-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">No plans yet. Run migrations or add a plan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
