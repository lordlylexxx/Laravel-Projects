<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Management - Admin Dashboard</title>
    @vite(['resources/css/app.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --cream: #F1F8E9;
            --white: #FFFFFF;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-800: #1F2937;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .dashboard-layout {
            padding-top: 82px;
        }

        .main-content {
            padding: 28px 36px;
        }

        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--green-dark);
            margin-bottom: 6px;
        }

        .page-header p {
            color: var(--gray-500);
        }

        .flash {
            background: #ECFDF5;
            border: 1px solid #86EFAC;
            color: #166534;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .card {
            background: var(--white);
            border-radius: 14px;
            border: 1px solid var(--green-soft);
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 1.1rem;
            color: var(--green-dark);
        }

        .pagination {
            padding: 14px 20px;
        }

        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'tenants'])

    <div class="dashboard-layout">
        <main class="main-content">
            @if(session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif
            @if($errors->has('onboarding'))
                <div class="flash" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B;">{{ $errors->first('onboarding') }}</div>
            @endif
            @if($errors->has('confirm_slug'))
                <div class="flash" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B;">{{ $errors->first('confirm_slug') }}</div>
            @endif
            @if($errors->has('delete'))
                <div class="flash" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B;">{{ $errors->first('delete') }}</div>
            @endif

            <div class="page-header">
                <h1>Tenant Management</h1>
                <p>Manage tenant plans and domain availability from the central admin app.</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Tenants ({{ $tenants->total() }})</h3>
                    <span style="color: var(--gray-500); font-size: 0.85rem;">
                        Plan + Domain controls |
                        Bandwidth = estimated HTTP transfer per tenant host (static assets skipped) |
                        <a href="{{ route('admin.tenants.lifecycle-logs') }}" style="color: var(--green-primary); font-weight: 700;">View Lifecycle Logs</a>
                    </span>
                </div>

                @php
                    $twInput = 'w-full min-w-[140px] rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25';
                    $twSelect = $twInput . ' max-w-full';
                    $twBtnPrimary = 'inline-flex cursor-pointer items-center justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1';
                    $twBtnDanger = 'inline-flex cursor-pointer items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1';
                    $twBtnSecondary = 'inline-flex cursor-pointer items-center justify-center rounded-lg bg-gray-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1';
                    $twBtnBlue = 'inline-flex cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1';
                @endphp
                <div class="overflow-x-auto border-t border-emerald-100/90 bg-white">
                    <table class="min-w-[1520px] w-full border-collapse text-left text-sm text-gray-800">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gradient-to-r from-emerald-50 via-green-50/90 to-emerald-50/70">
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Tenant</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Owner</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Plan</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Domain</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Subscription</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Onboarding</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">DB Used (MB)</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Bandwidth</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Period Ends</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5 text-xs font-semibold uppercase tracking-wider text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tenants as $tenant)
                                @php
                                    $domainEnabled = (bool) ($tenant->domain_enabled ?? true);
                                    $statusValue = (string) ($tenant->subscription_status ?? 'unknown');
                                    $latestLifecycle = $latestLifecycleByTenant[$tenant->id] ?? null;
                                    $centralPort = (int) env('CENTRAL_PORT', 8000);
                                    $statusBadgeClass = match ($statusValue) {
                                        'active' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/15',
                                        'trialing' => 'bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/15',
                                        'past_due' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/15',
                                        'cancelled' => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/12',
                                        default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/12',
                                    };

                                    $onboardingStatus = (string) ($tenant->onboarding_status ?? 'approved');
                                    $onboardingBadgeClass = match ($onboardingStatus) {
                                        'awaiting_payment' => 'bg-indigo-100 text-indigo-800 ring-1 ring-inset ring-indigo-600/15',
                                        'pending_approval' => 'bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/15',
                                        'approved' => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/15',
                                        'rejected' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/15',
                                        default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/12',
                                    };

                                    $domainLabel = $tenant->domain
                                        ? ($tenant->domain . ':' . $centralPort)
                                        : ('127.0.0.1:' . $centralPort);
                                    $periodEnds = $tenant->current_period_ends_at ?? $tenant->trial_ends_at;
                                    $dbUsed = $tenant->database ? ($databaseUsageMbByDatabase[$tenant->database] ?? null) : null;
                                @endphp
                                <tr class="align-top transition-colors hover:bg-gray-50/80">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900">{{ $tenant->name }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $tenant->slug }}</p>
                                        <form class="mt-3 flex flex-col gap-1.5" action="{{ route('admin.tenants.update-profile', $tenant) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $tenant->name }}" placeholder="Tenant name" class="{{ $twInput }}">
                                            <input type="text" name="app_title" value="{{ $tenant->app_title }}" placeholder="App title" class="{{ $twInput }}">
                                            <select name="locale" class="{{ $twSelect }}">
                                                <option value="en" {{ ($tenant->locale ?? 'en') === 'en' ? 'selected' : '' }}>EN</option>
                                                <option value="es" {{ ($tenant->locale ?? 'en') === 'es' ? 'selected' : '' }}>ES</option>
                                                <option value="fr" {{ ($tenant->locale ?? 'en') === 'fr' ? 'selected' : '' }}>FR</option>
                                                <option value="de" {{ ($tenant->locale ?? 'en') === 'de' ? 'selected' : '' }}>DE</option>
                                            </select>
                                            <input type="text" name="reason" placeholder="Reason (required)" required class="{{ $twInput }}">
                                            <button type="submit" class="{{ $twBtnPrimary }} w-fit">Update Profile</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-gray-900">{{ $tenant->owner?->name ?? 'Unassigned' }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $tenant->owner?->email ?? 'N/A' }}</p>
                                        <form class="mt-3 flex flex-col gap-1.5" action="{{ route('admin.tenants.resend-onboarding-email', $tenant) }}" method="POST">
                                            @csrf
                                            <input type="text" name="reason" placeholder="Reason (required); issues a new random password" required class="{{ $twInput }}">
                                            <button type="submit" class="{{ $twBtnSecondary }} w-fit">Email tenant admin credentials</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <form class="flex flex-col gap-1.5 sm:flex-row sm:flex-wrap sm:items-end" action="{{ route('admin.tenants.update-plan', $tenant) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="plan" class="{{ $twSelect }} sm:max-w-[9rem]">
                                                <option value="basic" {{ $tenant->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                                                <option value="plus" {{ $tenant->plan === 'plus' ? 'selected' : '' }}>Standard</option>
                                                <option value="pro" {{ $tenant->plan === 'pro' ? 'selected' : '' }}>Premium</option>
                                            </select>
                                            <input type="text" name="reason" placeholder="Reason (required)" required class="{{ $twInput }} sm:min-w-[8rem] sm:flex-1">
                                            <button type="submit" class="{{ $twBtnPrimary }} w-fit shrink-0">Save</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                                            @if($domainEnabled)
                                                <a href="{{ $tenant->publicUrl() }}" class="text-sm font-semibold text-emerald-700 underline-offset-2 hover:text-emerald-800 hover:underline" target="_blank" rel="noopener noreferrer">{{ $domainLabel }}</a>
                                            @else
                                                <span class="text-sm font-semibold text-red-700">{{ $domainLabel }}</span>
                                            @endif

                                            <form class="flex flex-col gap-1.5 sm:flex-row sm:flex-wrap sm:items-end" action="{{ route('admin.tenants.toggle-domain', $tenant) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="domain_enabled" value="{{ $domainEnabled ? 0 : 1 }}">
                                                <input type="text" name="reason" placeholder="Reason (required)" required class="{{ $twInput }} sm:min-w-[7rem] sm:max-w-[10rem]">
                                                <button type="submit" class="{{ $domainEnabled ? $twBtnDanger : $twBtnBlue }} w-fit shrink-0">
                                                    {{ $domainEnabled ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $statusValue)) }}</span>
                                        <form class="mt-3 flex flex-col gap-1.5 sm:flex-row sm:flex-wrap sm:items-end" action="{{ route('admin.tenants.update-subscription', $tenant) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="subscription_status" class="{{ $twSelect }} sm:max-w-[9rem]">
                                                <option value="trialing" {{ $statusValue === 'trialing' ? 'selected' : '' }}>Trialing</option>
                                                <option value="active" {{ $statusValue === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="past_due" {{ $statusValue === 'past_due' ? 'selected' : '' }}>Past Due</option>
                                                <option value="cancelled" {{ $statusValue === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <input type="text" name="reason" placeholder="Reason (required)" required class="{{ $twInput }} sm:min-w-[7rem] sm:flex-1">
                                            <button type="submit" class="{{ $twBtnPrimary }} w-fit shrink-0">Update</button>
                                        </form>
                                        @if($latestLifecycle)
                                            <div class="mt-3 text-xs leading-relaxed text-gray-500">
                                                Last: <span class="font-semibold text-gray-700">{{ str_replace('.', ' ', ucfirst($latestLifecycle->action)) }}</span><br>
                                                {{ $latestLifecycle->created_at?->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $onboardingBadgeClass }}">{{ str_replace('_', ' ', ucfirst($onboardingStatus)) }}</span>
                                        @if($tenant->payment_reference)
                                            <p class="mt-2 text-xs text-gray-500">Ref: {{ $tenant->payment_reference }}</p>
                                        @endif
                                        @if($onboardingStatus === 'pending_approval')
                                            <form class="mt-3 flex flex-col gap-1.5" action="{{ route('admin.tenants.approve-onboarding', $tenant) }}" method="POST">
                                                @csrf
                                                <input type="text" name="reason" placeholder="Approval reason (required)" required class="{{ $twInput }}">
                                                <button type="submit" class="{{ $twBtnPrimary }} w-fit">Approve &amp; provision</button>
                                            </form>
                                            <form class="mt-2 flex flex-col gap-1.5" action="{{ route('admin.tenants.reject-onboarding', $tenant) }}" method="POST">
                                                @csrf
                                                <input type="text" name="reason" placeholder="Rejection reason (required)" required class="{{ $twInput }}">
                                                <button type="submit" class="{{ $twBtnDanger }} w-fit">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 tabular-nums text-gray-800">{{ is_null($dbUsed) ? 'N/A' : number_format((float) $dbUsed, 2) }}</td>
                                    <td class="px-4 py-4">
                                        @php
                                            $bwUsed = (int) ($tenant->bandwidth_usage_bytes ?? 0);
                                            $bwQuota = $tenant->bandwidth_quota_bytes;
                                            $bwPct = $tenant->bandwidthUsagePercent();
                                        @endphp
                                        <div class="text-sm">
                                            <strong class="font-semibold text-gray-900" title="Accumulated request + response bytes (estimate)">{{ \Illuminate\Support\Number::fileSize($bwUsed) }}</strong>
                                            @if($bwQuota)
                                                <span class="text-gray-600"> / {{ \Illuminate\Support\Number::fileSize((int) $bwQuota) }}</span>
                                            @else
                                                <span class="text-gray-500"> / no cap</span>
                                            @endif
                                        </div>
                                        @if($bwPct !== null)
                                            <div class="mt-2 h-2 max-w-[180px] overflow-hidden rounded-full bg-gray-200">
                                                <div
                                                    class="h-full rounded-full transition-all {{ $bwPct >= 90 ? 'bg-red-600' : ($bwPct >= 70 ? 'bg-amber-500' : 'bg-emerald-600') }}"
                                                    style="width: {{ min(100, $bwPct) }}%;"
                                                ></div>
                                            </div>
                                            <p class="mt-1 text-[0.7rem] text-gray-500">{{ $bwPct }}% of quota</p>
                                        @endif
                                        @if($tenant->bandwidth_last_recorded_at)
                                            <p class="mt-1 text-[0.7rem] text-gray-500">Updated {{ $tenant->bandwidth_last_recorded_at->diffForHumans() }}</p>
                                        @endif
                                        <form class="mt-3 flex flex-col gap-1.5" action="{{ route('admin.tenants.update-bandwidth-quota', $tenant) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="bandwidth_quota_mb" min="0" step="1" placeholder="Quota (MiB/mo), empty = unlimited" value="{{ $bwQuota ? (int) round($bwQuota / 1024 / 1024) : '' }}" class="{{ $twInput }} max-w-[200px]">
                                            <input type="text" name="reason" placeholder="Reason (required)" required class="{{ $twInput }}">
                                            <button type="submit" class="{{ $twBtnPrimary }} w-fit">Set quota</button>
                                        </form>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-gray-800">{{ $periodEnds ? $periodEnds->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-4 py-4">
                                        <form
                                            class="flex flex-col gap-1.5"
                                            action="{{ route('admin.tenants.destroy', $tenant) }}"
                                            method="POST"
                                            onsubmit="return confirm('Permanently delete this tenant? Its landlord record will be removed and the tenant database will be dropped if it exists. This cannot be undone.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <label class="text-[0.7rem] font-semibold uppercase tracking-wide text-gray-500">Confirm slug</label>
                                            <input type="text" name="confirm_slug" value="{{ old('confirm_slug') }}" placeholder="{{ $tenant->slug }}" required autocomplete="off" class="{{ $twInput }} font-mono text-[0.85rem]">
                                            <input type="text" name="reason" placeholder="Reason (required)" required value="{{ old('reason') }}" class="{{ $twInput }}">
                                            <button type="submit" class="{{ $twBtnDanger }} w-fit">Delete tenant</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center text-sm text-gray-500">No tenants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tenants->hasPages())
                    <div class="pagination border-t border-gray-100">
                        {{ $tenants->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
