@php
    /** @var \App\Models\CentralLandingPlan $plan */
    /** @var bool $isCreate */
    $catalogLines = \App\Models\CentralLandingPlan::mergedTierCatalogFeatureLines();
    $defaultFeatureMode = old('feature_mode');
    if ($defaultFeatureMode === null) {
        $defaultFeatureMode = $plan->featureSelectionMode();
    }
    $pickedLines = old('feature_pick', $plan->features ?? []);
    if (! is_array($pickedLines)) {
        $pickedLines = [];
    }
    if ($isCreate && old('feature_mode') === null) {
        $defaultFeatureMode = 'custom_pick';
    }
    if ($isCreate && old('feature_pick') === null) {
        $pickedLines = $catalogLines;
    }
    $twField = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25';
    $twCheck = 'h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.partials.favicon')
    <title>{{ $isCreate ? 'Add plan' : 'Edit plan' }} · Plan management</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@include('admin.partials.admin-shell-styles')</style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'landing-plans'])

    <div class="dashboard-layout">
        <main class="main-content main-content-narrow">
            <div class="page-header">
                <h1>{{ $isCreate ? 'Add plan' : 'Edit plan' }}</h1>
                <p><a href="{{ route('admin.landing-plans.index') }}" style="color: var(--green-primary); font-weight: 600;">← Back to list</a></p>
            </div>

            <div class="card card-padded">
                @if(!$isCreate)
                    <div class="mb-6 rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50/90 to-green-50/80 p-4 text-sm shadow-sm">
                        <strong style="color: var(--green-dark);">Live preview (as on CA landing)</strong>
                        @php $ep = $plan->effectivePrice(); @endphp
                        <p class="mt-2 text-gray-800">
                            {{ $plan->effectiveTitle() }} — {{ $plan->effectiveCurrency() }}{{ number_format($ep, floor($ep) == $ep ? 0 : 2) }}
                        </p>
                        <ul class="mt-3 list-none space-y-1.5 text-xs text-gray-700">
                            @foreach($plan->effectiveFeatures() as $f)
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 inline-flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded-full border-2 border-emerald-200 bg-white text-[10px] text-emerald-700">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                    <span>{{ $f }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ $isCreate ? route('admin.landing-plans.store') : route('admin.landing-plans.update', $plan) }}" class="space-y-0">
                    @csrf
                    @if(!$isCreate)
                        @method('PUT')
                    @endif

                    <label for="title" class="mb-1.5 mt-0 block text-sm font-semibold" style="color: var(--green-dark);">Plan name (shown on card)</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $plan->title) }}" maxlength="255" required placeholder="e.g. Summer Owner Promo" class="{{ $twField }}">
                    @error('title')<div class="mt-1 text-sm text-red-700">{{ $message }}</div>@enderror

                    <label for="tenant_plan_key" class="mb-1.5 mt-4 block text-sm font-semibold" style="color: var(--green-dark);">Subscription tier (register link &amp; catalog price)</label>
                    <select id="tenant_plan_key" name="tenant_plan_key" required class="{{ $twField }}">
                        @foreach(\App\Models\CentralLandingPlan::ALLOWED_PLAN_KEYS as $key)
                            <option value="{{ $key }}" @selected(old('tenant_plan_key', $plan->tenant_plan_key ?? 'basic') === $key)>
                                {{ $key }} ({{ \App\Models\Tenant::planLabel($key) }})
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_plan_key')<div class="mt-1 text-sm text-red-700">{{ $message }}</div>@enderror

                    <label for="price_amount" class="mb-1.5 mt-4 block text-sm font-semibold" style="color: var(--green-dark);">Card price (optional)</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">{{ $plan->effectiveCurrency() }}</span>
                        <input type="number" id="price_amount" name="price_amount" value="{{ old('price_amount', $plan->price_amount) }}" min="0" max="999999.99" step="0.01" placeholder="Leave blank for catalog" class="{{ $twField }} min-w-[12rem] flex-1">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Shown on the public card. If you leave this blank, the card uses the standard catalog price for the selected tier. Currency always matches that tier.</p>
                    @error('price_amount')<div class="mt-1 text-sm text-red-700">{{ $message }}</div>@enderror

                    <label for="sort_order" class="mb-1.5 mt-4 block text-sm font-semibold" style="color: var(--green-dark);">Sort order</label>
                    <input type="number" id="sort_order" name="sort_order" min="0" max="32767" value="{{ old('sort_order', $plan->sort_order) }}" required class="{{ $twField }}">
                    @error('sort_order')<div class="mt-1 text-sm text-red-700">{{ $message }}</div>@enderror

                    <p class="mt-3 text-xs text-gray-500">
                        Owner signup still uses the selected tier’s
                        <code class="rounded bg-gray-100 px-1 font-mono text-[11px]">plan</code> query parameter (Basic / Standard / Premium / Promo).
                    </p>

                    <p class="mb-1 mt-5 block text-sm font-semibold" style="color: var(--green-dark);">Features on landing card</p>
                    <p class="mb-2 text-xs text-gray-500">Pick how the checklist is built. The landing button always says <strong class="text-gray-700">Register</strong>.</p>
                    @error('feature_mode')<div class="mb-2 text-sm text-red-700">{{ $message }}</div>@enderror
                    @error('feature_pick')<div class="mb-2 text-sm text-red-700">{{ $message }}</div>@enderror

                    <div class="mt-3 flex items-start gap-2.5">
                        <input type="radio" name="feature_mode" id="feature_mode_tier" value="tier_catalog" @checked($defaultFeatureMode === 'tier_catalog') class="mt-1 h-4 w-4 shrink-0 border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="feature_mode_tier" class="m-0 cursor-pointer text-sm font-semibold leading-snug" style="color: var(--green-dark);">
                            This tier only
                            <span class="mt-0.5 block text-xs font-normal text-gray-500">Same bullet list as the standard checklist for the selected tier (e.g. Basic’s three lines).</span>
                        </label>
                    </div>
                    <div class="mt-3 flex items-start gap-2.5">
                        <input type="radio" name="feature_mode" id="feature_mode_full" value="full_catalog" @checked($defaultFeatureMode === 'full_catalog') class="mt-1 h-4 w-4 shrink-0 border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="feature_mode_full" class="m-0 cursor-pointer text-sm font-semibold leading-snug" style="color: var(--green-dark);">
                            All tiers (Basic → Standard → Premium → Promo)
                            <span class="mt-0.5 block text-xs font-normal text-gray-500">Full combined checklist in catalog order.</span>
                        </label>
                    </div>
                    <div class="mt-3 flex items-start gap-2.5">
                        <input type="radio" name="feature_mode" id="feature_mode_custom_pick" value="custom_pick" @checked($defaultFeatureMode === 'custom_pick') class="mt-1 h-4 w-4 shrink-0 border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="feature_mode_custom_pick" class="m-0 cursor-pointer text-sm font-semibold leading-snug" style="color: var(--green-dark);">
                            Choose bullets
                            <span class="mt-0.5 block text-xs font-normal text-gray-500">Use the checkboxes below to include only the lines you want (catalog lines only).</span>
                        </label>
                    </div>

                    <p class="mb-2 mt-3 text-xs text-gray-500">
                        If you pick <strong class="text-gray-700">This tier only</strong> or <strong class="text-gray-700">All tiers</strong>, checkbox choices are ignored when you save.
                    </p>
                    <div id="feature_pick_panel" role="group" aria-label="Catalog features to include" class="max-h-[280px] overflow-y-auto rounded-xl border border-gray-200 bg-white p-3 shadow-inner">
                        @foreach($catalogLines as $line)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-800 hover:bg-emerald-50/80">
                                <input type="checkbox" name="feature_pick[]" value="{{ e($line) }}" @checked(in_array($line, $pickedLines, true)) class="mt-0.5 h-[18px] w-[18px] shrink-0 cursor-pointer rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="min-w-0 flex-1 leading-snug">{{ $line }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 flex items-center gap-2.5">
                        <input type="checkbox" id="is_visible" name="is_visible" value="1" @checked(old('is_visible', $plan->is_visible)) class="{{ $twCheck }}">
                        <label for="is_visible" class="m-0 cursor-pointer text-sm font-semibold" style="color: var(--green-dark);">Visible on CA landing</label>
                    </div>

                    <div class="mt-3 flex items-center gap-2.5">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $plan->is_featured)) class="{{ $twCheck }}">
                        <label for="is_featured" class="m-0 cursor-pointer text-sm font-semibold" style="color: var(--green-dark);">Featured / highlighted card</label>
                    </div>

                    @if(!$isCreate)
                        <div class="mt-5 flex items-center gap-2.5 border-t border-gray-200 pt-4">
                            <input type="checkbox" id="clear_catalog_overrides" name="clear_catalog_overrides" value="1" class="{{ $twCheck }}">
                            <label for="clear_catalog_overrides" class="m-0 cursor-pointer text-sm font-semibold" style="color: var(--green-dark);">Reset features to this tier only</label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Clears custom bullet picks and full-tier mode; the card uses only this plan’s tier checklist.</p>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Save</button>
                        <a href="{{ route('admin.landing-plans.index') }}" class="btn-admin-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
