<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.tenant-favicon')
    <title>User Management - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-50: #F9FAFB; --gray-100: #F3F4F6; --gray-200: #E5E7EB;
            --gray-300: #D1D5DB; --gray-400: #9CA3AF; --gray-500: #6B7280;
            --gray-600: #4B5563; --gray-700: #374151; --gray-800: #1F2937;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f1f8e9; color: #1f2937; }
        .main-content { max-width: 1200px; margin: 0 auto; padding: 96px 24px 40px; }
        .panel { background: #fff; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); margin-bottom: 18px; }
        .panel-header { padding: 18px 20px; border-bottom: 1px solid #e5e7eb; }
        .panel-header h1 { font-size: 1.4rem; color: #14532d; }
        .panel-header p { margin-top: 4px; color: #4b5563; font-size: 0.92rem; }
        .flash { margin-bottom: 12px; padding: 12px 14px; border-radius: 10px; font-size: 0.92rem; }
        .flash.success { background: #d1fae5; color: #065f46; }
        .flash.error { background: #fee2e2; color: #991b1b; }
        .flash.warning { background: #fef3c7; color: #92400e; }
        .section-body { padding: 16px 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; align-items: end; }
        .field input, .field select { width: 100%; padding: 10px 11px; border: 1px solid #d1d5db; border-radius: 8px; }
        .btn { border: 0; border-radius: 8px; padding: 10px 14px; cursor: pointer; font-weight: 600; }
        .btn.primary { background: #2e7d32; color: #fff; }
        .btn.muted { background: #e5e7eb; color: #1f2937; }
        .btn.warn { background: #f59e0b; color: #fff; }
        .btn.danger { background: #dc2626; color: #fff; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid #e5e7eb; font-size: 0.9rem; vertical-align: top; }
        th { color: #4b5563; background: #f9fafb; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em; }
        .inline-form { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .inline-form input, .inline-form select { padding: 7px 9px; border: 1px solid #d1d5db; border-radius: 6px; min-width: 110px; }
        .perm-grid { display: grid; grid-template-columns: 1fr; gap: 6px; margin-top: 4px; }
        .perm-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
        }
        .perm-option input[type="checkbox"] {
            width: 15px;
            height: 15px;
            margin-top: 2px;
            flex-shrink: 0;
            accent-color: #2e7d32;
        }
        .perm-option-label {
            font-size: 0.83rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.25;
        }
        .perm-option-key {
            color: #6b7280;
            font-size: 0.74rem;
            font-weight: 500;
            display: block;
            margin-top: 2px;
            line-height: 1.2;
        }
        .perm-dropdown { display: grid; gap: 8px; }
        .permission-card-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            align-items: start;
        }
        .perm-category { border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; overflow: hidden; }
        .perm-category summary {
            list-style: none;
            cursor: pointer;
            padding: 10px 12px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #14532d;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .perm-category summary::-webkit-details-marker { display: none; }
        .perm-category .perm-body { padding: 8px 10px; }
        .rbac-summary { display: flex; gap: 10px; flex-wrap: wrap; }
        .rbac-chip { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; border-radius: 999px; padding: 4px 10px; font-size: 0.78rem; }
        .rbac-note { font-size: 0.84rem; color: #6b7280; margin-top: 8px; }
        .muted { color: #6b7280; font-size: 0.82rem; }
        .template-create-form { grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; }
        .template-create-perms { grid-column: span 2; }
        .template-create-actions { grid-column: span 2; }
        .role-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .role-card { border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; overflow: hidden; }
        .role-card { flex: 1 1 360px; min-width: 320px; }
        .role-summary {
            list-style: none;
            cursor: pointer;
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .role-summary::-webkit-details-marker { display: none; }
        .role-summary-title { font-size: 0.9rem; font-weight: 700; color: #14532d; }
        .role-summary-meta { font-size: 0.76rem; color: #4b5563; }
        .role-card-body { padding: 10px; background: #fafafa; }
        .role-card .perm-grid { grid-template-columns: 1fr; max-height: 190px; overflow: auto; padding-right: 6px; }
        .perm-split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        @media (max-width: 1200px) {
            .permission-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 900px) {
            .form-grid, .perm-split, .template-create-form { grid-template-columns: 1fr; }
            .role-grid { display: grid; grid-template-columns: 1fr; }
            .role-card { min-width: 0; }
            .template-create-perms, .template-create-actions { grid-column: span 1; }
            .permission-card-grid { grid-template-columns: 1fr; }
        }

        @include('owner.partials.top-navbar-styles')
    </style>
</head>
<body class="owner-nav-page bg-[#f1f8e9] text-slate-800 antialiased">
    @include('owner.partials.top-navbar', ['active' => 'users'])

    <main class="main-content with-owner-nav mx-auto max-w-7xl space-y-4 px-6 pb-10 pt-24">
        @if(session('success'))
            <div class="flash success rounded-lg border border-emerald-200 bg-emerald-100 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash error rounded-lg border border-red-200 bg-red-100 px-4 py-3 text-sm font-medium text-red-700">{{ $errors->first() }}</div>
        @endif
        @if(session('warning'))
            <div class="flash warning rounded-lg border border-amber-200 bg-amber-100 px-4 py-3 text-sm font-medium text-amber-800">{{ session('warning') }}</div>
        @endif
        @php
            $permissionSections = [
                'users' => 'User management',
                'accommodations' => 'Accommodations',
                'bookings' => 'Bookings',
                'messages' => 'Messages',
                'reports' => 'Reports',
                'profile' => 'Profile',
            ];
            $sectionForPermission = static function (string $permission): string {
                if (str_starts_with($permission, 'users.')) {
                    return 'users';
                }
                if (str_starts_with($permission, 'accommodations.')) {
                    return 'accommodations';
                }
                if (str_starts_with($permission, 'bookings.')) {
                    return 'bookings';
                }
                if (str_starts_with($permission, 'messages.')) {
                    return 'messages';
                }
                if (str_starts_with($permission, 'reports.')) {
                    return 'reports';
                }
                if (str_starts_with($permission, 'profile.')) {
                    return 'profile';
                }

                return 'users';
            };
        @endphp

        <section class="panel rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="panel-header border-b border-slate-200 px-5 py-4">
                <h1 class="text-2xl font-bold tracking-tight text-green-900">User Management</h1>
                <p class="mt-1 text-sm text-slate-600">Tenant: {{ $currentTenant->name }} | Manage users and role-based access within this tenant only.</p>
            </div>
            <div class="section-body px-5 py-4">
                @php
                    $viewer = auth()->user();
                    $tenantLeader = $viewer->isOwner()
                        || ($viewer->isAdmin() && \App\Models\Tenant::checkCurrent());
                @endphp
                <div class="rbac-summary flex flex-wrap gap-2">
                    <span class="rbac-chip rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Your role: {{ ucfirst($viewer->role) }}</span>
                    <span class="rbac-chip rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Template-based role access</span>
                    @if($tenantLeader)
                        <span class="rbac-chip rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Owner/Core admin controls</span>
                    @endif
                </div>
                <p class="rbac-note mt-2 text-sm text-slate-500">Access is managed through role templates. If a row is your own account, actions are read-only for safety.</p>
                @if(!($customRbacReady ?? false))
                    <p class="rbac-note mt-2 text-sm font-medium text-amber-700">Custom role templates are temporarily unavailable for this tenant database. Run tenant migrations, then reload this page.</p>
                @endif
            </div>
            @if($canCreateUsers)
                <div class="section-body border-t border-slate-100 px-5 py-4">
                    <p class="rbac-note mb-3 text-sm text-slate-500">A secure random password is generated automatically and emailed to the new user. They should sign in and change it.</p>
                    <form action="/owner/users" method="POST" class="form-grid grid gap-3 md:grid-cols-4">
                        @csrf
                        <div class="field"><input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="name" placeholder="Full name" required></div>
                        <div class="field"><input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="email" name="email" placeholder="Email address" required></div>
                        <div class="inline-form flex flex-wrap items-center gap-2 md:col-span-2">
                            @if($customRbacReady ?? false)
                                <select class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" name="role_selection" required>
                                    @if(in_array(\App\Models\User::ROLE_ADMIN, $assignableRoles, true))
                                        <option value="core:admin">Admin</option>
                                    @endif
                                    @if(in_array(\App\Models\User::ROLE_CLIENT, $assignableRoles, true))
                                        <option value="core:client">Client</option>
                                    @endif
                                    @foreach($tenantCustomRoles as $tenantRole)
                                        <option value="custom:{{ $tenantRole->id }}">{{ $tenantRole->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" name="role" required>
                                    @foreach($assignableRoles as $role)
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <button type="submit" class="btn primary rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Create User</button>
                        </div>
                    </form>
                </div>
            @endif
        </section>

        @if($canAssignRoles && ($customRbacReady ?? false))
            <section class="panel rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="panel-header border-b border-slate-200 px-5 py-4">
                    <h1 class="text-2xl font-bold tracking-tight text-green-900">Custom Role Templates</h1>
                    <p class="mt-1 text-sm text-slate-600">Create tenant-specific role templates, then assign them to users. Access is determined by the assigned template permissions.</p>
                </div>
                <div class="section-body px-5 py-4">
                    <form action="{{ route('owner.users.custom-roles.store', [], false) }}" method="POST" class="form-grid template-create-form grid gap-3 md:grid-cols-2">
                        @csrf
                        <div class="field"><input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="name" placeholder="Role name (e.g. Front Desk)" required></div>
                        <div class="field"><input class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="description" placeholder="Optional description"></div>
                        <div class="field template-create-perms">
                            <div class="perm-dropdown permission-card-grid">
                                @foreach($permissionSections as $sectionKey => $sectionLabel)
                                    @php
                                        $sectionPermissions = array_values(array_filter(
                                            $customRoleAssignablePermissions,
                                            static fn (string $permission): bool => $sectionForPermission($permission) === $sectionKey
                                        ));
                                    @endphp
                                    @continue($sectionPermissions === [])
                                    <details class="perm-category">
                                        <summary>
                                            <span>{{ $sectionLabel }}</span>
                                            <span>{{ count($sectionPermissions) }}</span>
                                        </summary>
                                        <div class="perm-body">
                                            <div class="perm-grid">
                                                @foreach($sectionPermissions as $permission)
                                                    <label class="perm-option">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}">
                                                        <span class="perm-option-label">
                                                            {{ \App\Models\User::permissionLabelForUsersTable($permission) }}
                                                            <span class="perm-option-key">{{ $permission }}</span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                        <div class="inline-form template-create-actions flex">
                            <button type="submit" class="btn primary rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Create Role</button>
                        </div>
                    </form>
                </div>
                <div class="section-body border-t border-slate-100 px-5 py-4">
                    <div class="role-grid">
                        @forelse($tenantCustomRoles as $tenantRole)
                            @php
                                $rolePermissionNames = $tenantRole->permissions->pluck('permission_name')->all();
                            @endphp
                            <details class="role-card">
                                <summary class="role-summary">
                                    <span class="role-summary-title">{{ $tenantRole->name }}</span>
                                    <span class="role-summary-meta">{{ count($rolePermissionNames) }}/{{ count($customRoleAssignablePermissions) }} enabled</span>
                                </summary>
                                <div class="role-card-body">
                                    @if($tenantRole->description)
                                        <p class="muted" style="margin-bottom:8px;">{{ $tenantRole->description }}</p>
                                    @endif
                                    <form action="{{ route('owner.users.custom-roles.update', ['tenantCustomRole' => $tenantRole->id], false) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="inline-form mb-2 flex flex-wrap gap-2">
                                            <input class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="name" value="{{ $tenantRole->name }}" required>
                                            <input class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="description" value="{{ $tenantRole->description }}" placeholder="Description">
                                            <button type="submit" class="btn muted rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-300">Update</button>
                                        </div>
                                        <div class="perm-dropdown permission-card-grid">
                                            @foreach($permissionSections as $sectionKey => $sectionLabel)
                                                @php
                                                    $sectionPermissions = array_values(array_filter(
                                                        $customRoleAssignablePermissions,
                                                        static fn (string $permission): bool => $sectionForPermission($permission) === $sectionKey
                                                    ));
                                                    $selectedCount = count(array_intersect($sectionPermissions, $rolePermissionNames));
                                                @endphp
                                                @continue($sectionPermissions === [])
                                                <details class="perm-category">
                                                    <summary>
                                                        <span>{{ $sectionLabel }}</span>
                                                        <span>{{ $selectedCount }}/{{ count($sectionPermissions) }}</span>
                                                    </summary>
                                                    <div class="perm-body">
                                                        <div class="perm-grid">
                                                            @foreach($sectionPermissions as $permission)
                                                                <label class="perm-option">
                                                                    <input
                                                                        type="checkbox"
                                                                        name="permissions[]"
                                                                        value="{{ $permission }}"
                                                                        {{ in_array($permission, $rolePermissionNames, true) ? 'checked' : '' }}
                                                                    >
                                                                    <span class="perm-option-label">
                                                                        {{ \App\Models\User::permissionLabelForUsersTable($permission) }}
                                                                        <span class="perm-option-key">{{ $permission }}</span>
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </details>
                                            @endforeach
                                        </div>
                                    </form>
                                    <form action="{{ route('owner.users.custom-roles.destroy', ['tenantCustomRole' => $tenantRole->id], false) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn danger rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete Template</button>
                                    </form>
                                </div>
                            </details>
                        @empty
                            <p class="muted">No custom templates yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <section class="panel rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="panel-header border-b border-slate-200 px-5 py-4">
                <h1 class="text-2xl font-bold tracking-tight text-green-900">Tenant Users</h1>
            </div>
            <div class="section-body table-wrap px-5 py-4">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Update User</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $managedUser)
                            <tr>
                                <td>{{ $managedUser->name }}</td>
                                <td>{{ $managedUser->email }}</td>
                                <td>{{ ucfirst($managedUser->role) }}</td>
                                <td>
                                    <span>{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span>
                                    @if($canToggleUsers && auth()->id() !== $managedUser->id)
                                        <form action="/owner/users/{{ $managedUser->id }}/activate" method="POST" class="inline-form mt-2 flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="{{ $managedUser->is_active ? 0 : 1 }}">
                                            <button class="btn {{ $managedUser->is_active ? 'danger rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700' : 'warn rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600' }}" type="submit">
                                                {{ $managedUser->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if($canEditUsers && auth()->id() !== $managedUser->id)
                                        <form action="/owner/users/{{ $managedUser->id }}" method="POST" class="inline-form flex flex-wrap items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="text" name="name" value="{{ $managedUser->name }}" required>
                                            <input class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" type="email" name="email" value="{{ $managedUser->email }}" required>
                                            @if($canAssignRoles)
                                                @if($customRbacReady ?? false)
                                                    @php
                                                        $selectedRoleSelection = $managedUser->tenant_custom_role_id
                                                            ? 'custom:'.$managedUser->tenant_custom_role_id
                                                            : 'core:'.$managedUser->role;
                                                    @endphp
                                                    <select class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" name="role_selection" required>
                                                        @if(in_array(\App\Models\User::ROLE_ADMIN, $assignableRoles, true))
                                                            <option value="core:admin" {{ $selectedRoleSelection === 'core:admin' ? 'selected' : '' }}>Admin</option>
                                                        @endif
                                                        @if(in_array(\App\Models\User::ROLE_CLIENT, $assignableRoles, true))
                                                            <option value="core:client" {{ $selectedRoleSelection === 'core:client' ? 'selected' : '' }}>Client</option>
                                                        @endif
                                                        @foreach($tenantCustomRoles as $tenantRole)
                                                            <option
                                                                value="custom:{{ $tenantRole->id }}"
                                                                {{ $selectedRoleSelection === 'custom:'.$tenantRole->id ? 'selected' : '' }}
                                                            >
                                                                {{ $tenantRole->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-200" name="role" required>
                                                        @foreach($assignableRoles as $role)
                                                            <option value="{{ $role }}" {{ $managedUser->role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            @else
                                                <input type="hidden" name="role" value="{{ $managedUser->role }}">
                                            @endif
                                            <button type="submit" class="btn muted rounded-lg bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-300">Save</button>
                                        </form>
                                    @else
                                        <span style="color:#6b7280;">Not editable</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        [$permissionLabels, $fromLegacyFallback] = $managedUser->permissionNamesForOwnerUsersTable();
                                    @endphp
                                    <p class="muted" style="margin-bottom:8px;">
                                        Template: {{ $managedUser->tenantCustomRole?->name ?? 'Default role template' }}
                                    </p>
                                    @if($permissionLabels->isNotEmpty())
                                        @if($fromLegacyFallback)
                                            <span style="color:#6b7280;font-size:0.82rem;display:block;margin-bottom:6px;">Effective access (from role; Spatie not synced yet)</span>
                                        @endif
                                        <div class="rbac-summary">
                                            @foreach($permissionLabels as $permissionName)
                                                <span class="rbac-chip">{{ $permissionName }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color:#6b7280;">No explicit permissions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No tenant users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>
