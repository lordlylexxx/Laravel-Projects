<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - ImpaStay</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        .form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; align-items: end; }
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
        .perm-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px 8px; margin-top: 8px; }
        .perm-grid label { display: flex; gap: 6px; align-items: center; font-size: 0.82rem; color: #374151; }
        .rbac-summary { display: flex; gap: 10px; flex-wrap: wrap; }
        .rbac-chip { background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; border-radius: 999px; padding: 4px 10px; font-size: 0.78rem; }
        .rbac-note { font-size: 0.84rem; color: #6b7280; margin-top: 8px; }
        @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }

        @include('owner.partials.top-navbar-styles')
    </style>
</head>
<body class="owner-nav-page">
    @include('owner.partials.top-navbar', ['active' => 'users'])

    <main class="main-content with-owner-nav">
        @if(session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash error">{{ $errors->first() }}</div>
        @endif
        @if(session('warning'))
            <div class="flash warning">{{ session('warning') }}</div>
        @endif

        <section class="panel">
            <div class="panel-header">
                <h1>User Management</h1>
                <p>Tenant: {{ $currentTenant->name }} | Manage users and role-based access within this tenant only.</p>
            </div>
            <div class="section-body">
                @php
                    $viewer = auth()->user();
                    $tenantLeader = $viewer->isOwner()
                        || ($viewer->isAdmin() && \App\Models\Tenant::checkCurrent());
                @endphp
                <div class="rbac-summary">
                    <span class="rbac-chip">Your role: {{ ucfirst($viewer->role) }}</span>
                    @if($tenantLeader)
                        <span class="rbac-chip">Full tenant RBAC access</span>
                    @elseif($canAssignPermissions)
                        <span class="rbac-chip">Can assign permissions</span>
                    @else
                        <span class="rbac-chip">View-only RBAC in this table</span>
                    @endif
                </div>
                <p class="rbac-note">RBAC controls appear per-user in the table below. If a row is your own account, actions are read-only for safety.</p>
            </div>
            @if($canCreateUsers)
                <div class="section-body">
                    <p class="rbac-note" style="margin-bottom: 12px;">A secure random password is generated automatically and emailed to the new user. They should sign in and change it.</p>
                    <form action="/owner/users" method="POST" class="form-grid">
                        @csrf
                        <div class="field"><input type="text" name="name" placeholder="Full name" required></div>
                        <div class="field"><input type="email" name="email" placeholder="Email address" required></div>
                        <div class="inline-form">
                            <select name="role" required>
                                @foreach($assignableRoles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn primary">Create User</button>
                        </div>
                    </form>
                </div>
            @endif
        </section>

        <section class="panel">
            <div class="panel-header">
                <h1>Tenant Users</h1>
            </div>
            <div class="section-body table-wrap">
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
                                        <form action="/owner/users/{{ $managedUser->id }}/activate" method="POST" class="inline-form" style="margin-top:8px;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="{{ $managedUser->is_active ? 0 : 1 }}">
                                            <button class="btn {{ $managedUser->is_active ? 'danger' : 'warn' }}" type="submit">
                                                {{ $managedUser->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if($canEditUsers && auth()->id() !== $managedUser->id)
                                        <form action="/owner/users/{{ $managedUser->id }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $managedUser->name }}" required>
                                            <input type="email" name="email" value="{{ $managedUser->email }}" required>
                                            @if($canAssignRoles)
                                                <select name="role" required>
                                                    @foreach($assignableRoles as $role)
                                                        <option value="{{ $role }}" {{ $managedUser->role === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="role" value="{{ $managedUser->role }}">
                                            @endif
                                            <button type="submit" class="btn muted">Save</button>
                                        </form>
                                    @else
                                        <span style="color:#6b7280;">Not editable</span>
                                    @endif
                                </td>
                                <td>
                                    @if($canAssignPermissions && auth()->id() !== $managedUser->id)
                                        @php
                                            $rowAssignablePermissions = $managedUser->isClient()
                                                ? $assignableClientPermissions
                                                : $assignableStaffPermissions;
                                        @endphp
                                        @if($managedUser->isClient())
                                            <p style="color:#6b7280;font-size:0.82rem;margin-bottom:8px;max-width:320px;">Guest capabilities for this tenant (bookings, messages, profile). Staff permissions are not applied to clients.</p>
                                        @endif
                                        <form action="/owner/users/{{ $managedUser->id }}/permissions" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="perm-grid">
                                                @foreach($rowAssignablePermissions as $permission)
                                                    <label>
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}" {{ $managedUser->hasPermission($permission) ? 'checked' : '' }}>
                                                        <span>{{ \App\Models\User::permissionLabelForUsersTable($permission) }}</span>
                                                        <span style="color:#9ca3af;font-size:0.75rem;display:block;">{{ $permission }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="btn muted" style="margin-top:8px;">Save Permissions</button>
                                        </form>
                                    @else
                                        @php
                                            [$permissionLabels, $fromLegacyFallback] = $managedUser->permissionNamesForOwnerUsersTable();
                                        @endphp
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

                <div style="margin-top:12px;">
                    {{ $users->links() }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>
