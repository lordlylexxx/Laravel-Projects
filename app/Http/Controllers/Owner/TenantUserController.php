<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Messaging\TenantCentralSupportProxyUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantUserController extends Controller
{
    public function index(Request $request): View
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canManageUsers($actor), 403);

        $users = User::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('email', 'not like', '__impastay_central_support.tenant-%')
            ->orderByDesc('id')
            ->paginate(15);

        return view('owner.users.index', [
            'users' => $users,
            'currentTenant' => $currentTenant,
            'canCreateUsers' => $this->canCreateUsers($actor),
            'canEditUsers' => $this->canEditUsers($actor),
            'canAssignRoles' => $this->canAssignRoles($actor),
            'canAssignPermissions' => $this->canAssignPermissions($actor),
            'canToggleUsers' => $this->canToggleUsers($actor),
            'assignableRoles' => $this->assignableRoles($actor),
            'assignablePermissions' => $this->assignablePermissions($actor),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canCreateUsers($actor), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'role' => ['required', 'in:admin,client'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'role' => $validated['role'],
            'tenant_id' => $currentTenant->id,
            'is_active' => true,
        ]);

        $user->syncRbacFromLegacyRole();

        return redirect('/owner/users')->with('success', 'Tenant user created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canEditUsers($actor), 403);
        $this->assertManageableUser($actor, $user, $currentTenant);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,client'],
        ]);

        if (! in_array($validated['role'], $this->assignableRoles($actor), true)) {
            return back()->withErrors(['role' => 'You are not allowed to assign this role.']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $validated['role'],
        ]);

        $user->syncRbacFromLegacyRole();

        return redirect('/owner/users')->with('success', 'Tenant user updated.');
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canAssignPermissions($actor), 403);
        $this->assertManageableUser($actor, $user, $currentTenant);

        $allowed = $this->assignablePermissions($actor);
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', $allowed)],
        ]);

        $selected = array_values(array_intersect($validated['permissions'] ?? [], $allowed));
        $user->syncTenantPermissions($selected);

        return redirect('/owner/users')->with('success', 'User permissions updated.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canToggleUsers($actor), 403);
        $this->assertManageableUser($actor, $user, $currentTenant);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update([
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect('/owner/users')->with('success', 'User status updated.');
    }

    private function currentTenantOrFail(): Tenant
    {
        $tenant = Tenant::current();
        abort_if(! $tenant, 404);

        return $tenant;
    }

    private function canManageUsers(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_VIEW);
    }

    private function canCreateUsers(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_CREATE);
    }

    private function canEditUsers(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_UPDATE);
    }

    private function canToggleUsers(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_ACTIVATE);
    }

    private function canAssignRoles(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_ASSIGN_ROLES);
    }

    private function canAssignPermissions(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_ASSIGN_PERMISSIONS);
    }

    private function tenantAdminCan(User $actor, string $permission): bool
    {
        if (! $actor->isAdmin() || ! $this->isTenantScopedActor($actor)) {
            return false;
        }

        if ($actor->hasPermission($permission)) {
            return true;
        }

        // Self-heal legacy role -> RBAC mapping for older tenant users.
        $actor->syncRbacFromLegacyRole();

        return $actor->hasPermission($permission);
    }

    private function isTenantScopedActor(User $actor): bool
    {
        $currentTenant = Tenant::current();

        if (! $currentTenant) {
            return false;
        }

        // In tenant-db context a null tenant_id still belongs to the current tenant.
        return $actor->tenant_id === null || (int) $actor->tenant_id === (int) $currentTenant->id;
    }

    private function assertManageableUser(User $actor, User $managedUser, Tenant $tenant): void
    {
        abort_unless((int) $managedUser->tenant_id === (int) $tenant->id, 404);

        abort_if(TenantCentralSupportProxyUser::isProxy($managedUser), 403);

        // Tenant admins cannot modify owner accounts.
        if ($actor->isAdmin() && ! $actor->isOwner() && $managedUser->isOwner()) {
            abort(403);
        }
    }

    private function assignableRoles(User $actor): array
    {
        if (! $this->canAssignRoles($actor)) {
            return [];
        }

        return [User::ROLE_ADMIN, User::ROLE_CLIENT];
    }

    private function assignablePermissions(User $actor): array
    {
        $all = [
            User::PERM_USERS_VIEW,
            User::PERM_USERS_CREATE,
            User::PERM_USERS_UPDATE,
            User::PERM_USERS_ACTIVATE,
            User::PERM_USERS_ASSIGN_ROLES,
            User::PERM_USERS_ASSIGN_PERMISSIONS,
            User::PERM_ACCOMMODATIONS_CREATE,
            User::PERM_ACCOMMODATIONS_UPDATE,
            User::PERM_ACCOMMODATIONS_DELETE,
            User::PERM_BOOKINGS_MANAGE,
            User::PERM_MESSAGES_MANAGE,
            User::PERM_REPORTS_VIEW,
        ];

        if ($actor->isOwner()) {
            return $all;
        }

        if ($this->canAssignPermissions($actor)) {
            return [
                User::PERM_USERS_VIEW,
                User::PERM_USERS_CREATE,
                User::PERM_USERS_UPDATE,
                User::PERM_USERS_ACTIVATE,
                User::PERM_ACCOMMODATIONS_CREATE,
                User::PERM_ACCOMMODATIONS_UPDATE,
                User::PERM_ACCOMMODATIONS_DELETE,
                User::PERM_BOOKINGS_MANAGE,
                User::PERM_MESSAGES_MANAGE,
                User::PERM_REPORTS_VIEW,
            ];
        }

        return [];
    }
}
