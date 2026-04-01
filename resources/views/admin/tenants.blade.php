<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--gray-200);
            text-align: left;
            vertical-align: middle;
            font-size: 0.92rem;
        }

        th {
            background: var(--green-white);
            color: var(--gray-700);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-badge.active { background: #DCFCE7; color: #166534; }
        .status-badge.trialing { background: #FEF3C7; color: #92400E; }
        .status-badge.past-due { background: #FEE2E2; color: #991B1B; }
        .status-badge.cancelled { background: #F3F4F6; color: #374151; }

        .inline-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .inline-form.stacked {
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
        }

        .inline-form select {
            padding: 6px 8px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            background: #fff;
        }

        .inline-form input[type="text"] {
            padding: 6px 8px;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            background: #fff;
            min-width: 170px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
        }

        .btn.save { background: var(--green-primary); }
        .btn.disable { background: #DC2626; }
        .btn.enable { background: #2563EB; }
        .btn.secondary { background: #6B7280; }

        .tenant-url {
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .tenant-url:hover { text-decoration: underline; }

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
            @if($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
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
                        <a href="/admin/tenant-lifecycle-logs" style="color: var(--green-primary); font-weight: 700;">View Lifecycle Logs</a>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Tenant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Owner</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Domain</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Subscription</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">DB Used (MB)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Bandwidth (MB)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Period Ends</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Delete</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($tenants as $tenant)
                                @php
                                    $domainEnabled = (bool) ($tenant->domain_enabled ?? true);
                                    $statusValue = (string) ($tenant->subscription_status ?? 'unknown');
                                    $latestLifecycle = $latestLifecycleByTenant[$tenant->id] ?? null;
                                    $centralPort = (int) env('CENTRAL_PORT', 8000);
                                    $statusClass = match ($statusValue) {
                                        'active' => 'active',
                                        'trialing' => 'trialing',
                                        'past_due' => 'past-due',
                                        'cancelled' => 'cancelled',
                                        default => 'cancelled',
                                    };

                                    $domainLabel = $tenant->domain
                                        ? ($tenant->domain . ':' . $centralPort)
                                        : ('127.0.0.1:' . $centralPort);
                                    $periodEnds = $tenant->current_period_ends_at ?? $tenant->trial_ends_at;
                                    $dbUsed = $tenant->database ? ($databaseUsageMbByDatabase[$tenant->database] ?? null) : null;
                                    $bandwidthUsed = $tenant->database ? ($bandwidthUsageMbByDatabase[$tenant->database] ?? null) : null;
                                    $isCustomPlan = str_starts_with((string) $tenant->plan, 'custom:');
                                    $customPlanName = $isCustomPlan ? str_replace('-', ' ', substr((string) $tenant->plan, 7)) : '';
                                @endphp
                                <tr class="align-top hover:bg-slate-50/60">
                                    <td class="px-4 py-4">
                                        <strong class="text-slate-800">{{ $tenant->name }}</strong>
                                        <div class="mt-1 text-xs text-slate-500">{{ $tenant->slug }}</div>
                                        <form class="mt-3 flex flex-col gap-2" action="/admin/tenants/{{ $tenant->id }}/profile" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="name" value="{{ $tenant->name }}" placeholder="Tenant name">
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="app_title" value="{{ $tenant->app_title }}" placeholder="App title">
                                            <select class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" name="locale">
                                                <option value="en" {{ ($tenant->locale ?? 'en') === 'en' ? 'selected' : '' }}>EN</option>
                                                <option value="es" {{ ($tenant->locale ?? 'en') === 'es' ? 'selected' : '' }}>ES</option>
                                                <option value="fr" {{ ($tenant->locale ?? 'en') === 'fr' ? 'selected' : '' }}>FR</option>
                                                <option value="de" {{ ($tenant->locale ?? 'en') === 'de' ? 'selected' : '' }}>DE</option>
                                            </select>
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason (required)" required>
                                            <button type="submit" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Update Profile</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ $tenant->owner?->name ?? 'Unassigned' }}
                                        <div class="mt-1 text-xs text-slate-500">{{ $tenant->owner?->email ?? 'N/A' }}</div>
                                        <form class="mt-3 flex flex-col gap-2" action="/admin/tenants/{{ $tenant->id }}/resend-onboarding-email" method="POST">
                                            @csrf
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason to resend (required)" required>
                                            <button type="submit" class="rounded-md bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Resend Onboarding Email</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <form class="flex flex-col gap-2" action="/admin/tenants/{{ $tenant->id }}/plan" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" name="plan" id="plan-{{ $tenant->id }}">
                                                <option value="basic" {{ $tenant->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                                                <option value="plus" {{ $tenant->plan === 'plus' ? 'selected' : '' }}>Standard</option>
                                                <option value="pro" {{ $tenant->plan === 'pro' ? 'selected' : '' }}>Premium</option>
                                                <option value="custom" {{ $isCustomPlan ? 'selected' : '' }}>Custom</option>
                                            </select>
                                            <div class="flex gap-2">
                                                <input class="w-full rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="custom_plan" id="custom-plan-{{ $tenant->id }}" value="{{ $customPlanName ? ucwords($customPlanName) : '' }}" placeholder="Custom plan name">
                                                <button type="button" class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700" onclick="setCustomPlan({{ $tenant->id }})">Add</button>
                                            </div>
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason (required)" required>
                                            <button type="submit" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Save</button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($domainEnabled)
                                                <a href="{{ $tenant->publicUrl() }}" class="font-semibold text-emerald-700 hover:underline" target="_blank">{{ $domainLabel }}</a>
                                            @else
                                                <span class="font-semibold text-rose-700">{{ $domainLabel }}</span>
                                            @endif

                                            <form class="flex flex-col gap-2" action="/admin/tenants/{{ $tenant->id }}/domain-status" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="domain_enabled" value="{{ $domainEnabled ? 0 : 1 }}">
                                                <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason (required)" required>
                                                <button type="submit" class="rounded-md px-3 py-1.5 text-xs font-semibold text-white {{ $domainEnabled ? 'bg-rose-600 hover:bg-rose-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                                                    {{ $domainEnabled ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass === 'active' ? 'bg-emerald-100 text-emerald-700' : ($statusClass === 'trialing' ? 'bg-amber-100 text-amber-700' : ($statusClass === 'past-due' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">{{ ucfirst(str_replace('_', ' ', $statusValue)) }}</span>
                                        <form class="mt-2 flex flex-col gap-2" action="/admin/tenants/{{ $tenant->id }}/subscription" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" name="subscription_status">
                                                <option value="trialing" {{ $statusValue === 'trialing' ? 'selected' : '' }}>Trialing</option>
                                                <option value="active" {{ $statusValue === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="past_due" {{ $statusValue === 'past_due' ? 'selected' : '' }}>Past Due</option>
                                                <option value="cancelled" {{ $statusValue === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason (required)" required>
                                            <button type="submit" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Update</button>
                                        </form>
                                        @if($latestLifecycle)
                                            <div class="mt-2 text-xs text-slate-500">
                                                Last: <strong>{{ str_replace('.', ' ', ucfirst($latestLifecycle->action)) }}</strong><br>
                                                {{ $latestLifecycle->created_at?->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-slate-700">{{ is_null($dbUsed) ? 'N/A' : number_format((float) $dbUsed, 2) }}</td>
                                    <td class="px-4 py-4 text-slate-700">{{ is_null($bandwidthUsed) ? 'N/A' : number_format((float) $bandwidthUsed, 2) }}</td>
                                    <td class="px-4 py-4 text-slate-700">{{ $periodEnds ? $periodEnds->format('M d, Y') : 'N/A' }}</td>
                                    <td class="px-4 py-4">
                                        <form
                                            class="flex max-w-[200px] flex-col gap-2"
                                            action="{{ route('admin.tenants.destroy', $tenant, false) }}"
                                            method="POST"
                                            onsubmit="return confirm('This permanently deletes the tenant, its landlord record, and (on MySQL) its database. Continue?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="reason" placeholder="Reason (required)" required>
                                            <input class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs" type="text" name="confirm_slug" placeholder="Type slug: {{ $tenant->slug }}" required autocomplete="off">
                                            <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Delete tenant</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-500">No tenants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tenants->hasPages())
                    <div class="pagination">
                        {{ $tenants->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
    <script>
        function setCustomPlan(tenantId) {
            const planSelect = document.getElementById(`plan-${tenantId}`);
            const customInput = document.getElementById(`custom-plan-${tenantId}`);
            if (!planSelect || !customInput) return;
            planSelect.value = 'custom';
            customInput.focus();
        }
    </script>
</body>
</html>
