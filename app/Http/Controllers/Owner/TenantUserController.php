<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Mail\TenantUserWelcomeMail;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Messaging\TenantCentralSupportProxyUser;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class TenantUserController extends Controller
{
    public function index(Request $request): View
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canManageUsers($actor), 403);

        $this->bootstrapTenantSpatieRbac();

        $users = User::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('email', 'not like', '__impastay_central_support.tenant-%')
            ->orderByDesc('id')
            ->paginate(15);

        $this->ensureClientUsersHaveBaselinePermissions($users->getCollection(), $currentTenant);

        return view('owner.users.index', [
            'users' => $users,
            'currentTenant' => $currentTenant,
            'canCreateUsers' => $this->canCreateUsers($actor),
            'canEditUsers' => $this->canEditUsers($actor),
            'canAssignRoles' => $this->canAssignRoles($actor),
            'canAssignPermissions' => $this->canAssignPermissions($actor),
            'canToggleUsers' => $this->canToggleUsers($actor),
            'assignableRoles' => $this->assignableRoles($actor),
            'assignableStaffPermissions' => $this->assignableStaffPermissions($actor),
            'assignableClientPermissions' => $this->assignableClientPermissions($actor),
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
            'role' => ['required', 'in:admin,client'],
        ]);

        $plainPassword = Str::password(16, true, true, true, false);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => $plainPassword,
            'role' => $validated['role'],
            'tenant_id' => $currentTenant->id,
            'is_active' => true,
        ]);

        $user->syncRbacFromLegacyRole();

        if ($user->isClient()) {
            $this->bootstrapTenantSpatieRbac();
            $previousTeam = getPermissionsTeamId();
            setPermissionsTeamId($currentTenant->id);
            try {
                $user->syncPermissions(User::defaultClientSpatiePermissions());
            } finally {
                setPermissionsTeamId($previousTeam);
            }
        }

        $loginUrl = url('/login');

        try {
            Mail::to($user->email)->send(new TenantUserWelcomeMail(
                userName: $user->name,
                tenantName: $currentTenant->name,
                roleLabel: ucfirst((string) $user->role),
                emailAddress: $user->email,
                temporaryPassword: $plainPassword,
                loginUrl: $loginUrl,
            ));
        } catch (Throwable $e) {
            report($e);

            return redirect('/owner/users')
                ->with('success', 'Tenant user created.')
                ->with('warning', 'We could not email their temporary password. Check your mail configuration or set a password for them manually.');
        }

        return redirect('/owner/users')
            ->with('success', 'Tenant user created. A temporary password was sent to '.$user->email.'.');
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

        $allowed = $this->assignablePermissionsForManagedUser($actor, $user);
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', $allowed)],
        ]);

        $selected = array_values(array_intersect($validated['permissions'] ?? [], $allowed));

        $this->bootstrapTenantSpatieRbac();

        $previousTeam = getPermissionsTeamId();
        setPermissionsTeamId($currentTenant->id);
        try {
            if ($user->isClient()) {
                foreach (User::staffSpatiePermissionNames() as $staffPerm) {
                    if ($user->getDirectPermissions()->contains(fn ($p) => $p->name === $staffPerm)) {
                        $user->revokePermissionTo($staffPerm);
                    }
                }
            }

            $user->syncPermissions($selected);
        } finally {
            setPermissionsTeamId($previousTeam);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

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

    /**
     * Ensure permission rows and role grants exist on the current tenant database (idempotent).
     * Avoids PermissionDoesNotExist when syncing client defaults or displaying RBAC before a manual seed.
     */
    private function bootstrapTenantSpatieRbac(): void
    {
        RbacCatalog::ensurePermissionsExist();
        RbacCatalog::ensureRolesAndGrantPermissions();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * On the tenant app, `admin` is treated as the business owner for access control.
     */
    private function tenantManagerEquivalentToOwner(User $actor): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $actor->isAdmin() && $this->isTenantScopedActor($actor);
    }

    private function canManageUsers(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_VIEW);
    }

    private function canCreateUsers(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_CREATE);
    }

    private function canEditUsers(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_UPDATE);
    }

    private function canToggleUsers(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_ACTIVATE);
    }

    private function canAssignRoles(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return true;
        }

        return $this->tenantAdminCan($actor, User::PERM_USERS_ASSIGN_ROLES);
    }

    private function canAssignPermissions(User $actor): bool
    {
        if ($this->tenantManagerEquivalentToOwner($actor)) {
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

        if ($actor->hasPermission($permission)) {
            return true;
        }

        // Tenant DB may lack Spatie migrations/seed; mirror RolesAndPermissionsSeeder for role=admin.
        return in_array($permission, User::defaultTenantAdminSpatiePermissions(), true);
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

        // Non–tenant-scoped admins cannot modify owner accounts; tenant `admin` is owner-equivalent.
        if ($actor->isAdmin() && ! $actor->isOwner() && $managedUser->isOwner() && ! $this->isTenantScopedActor($actor)) {
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

    /**
     * Staff (tenant admin) rows: owner/manager style permissions — not shown on client rows.
     *
     * @return list<string>
     */
    private function assignableStaffPermissions(User $actor): array
    {
        $all = User::staffSpatiePermissionNames();

        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return $all;
        }

        if ($this->canAssignPermissions($actor)) {
            return array_values(array_diff($all, [
                User::PERM_USERS_ASSIGN_ROLES,
                User::PERM_USERS_ASSIGN_PERMISSIONS,
            ]));
        }

        return [];
    }

    /**
     * Client rows only: guest capabilities on this tenant app.
     *
     * @return list<string>
     */
    private function assignableClientPermissions(User $actor): array
    {
        if (! $this->canAssignPermissions($actor)) {
            return [];
        }

        return User::defaultClientSpatiePermissions();
    }

    /**
     * @return list<string>
     */
    private function assignablePermissionsForManagedUser(User $actor, User $managedUser): array
    {
        return $managedUser->isClient()
            ? $this->assignableClientPermissions($actor)
            : $this->assignableStaffPermissions($actor);
    }

    /**
     * Clients should only carry guest-capability permissions. Strip mistaken staff direct grants,
     * then ensure at least the default client capability set (direct; client Spatie role has none).
     *
     * @param  Collection<int, User>  $users
     */
    private function ensureClientUsersHaveBaselinePermissions(Collection $users, Tenant $currentTenant): void
    {
        $previousTeam = getPermissionsTeamId();
        setPermissionsTeamId($currentTenant->id);
        $mutated = false;
        try {
            foreach ($users as $managedUser) {
                if (! $managedUser->isClient()) {
                    continue;
                }
                foreach (User::staffSpatiePermissionNames() as $staffPerm) {
                    if ($managedUser->getDirectPermissions()->contains(fn ($p) => $p->name === $staffPerm)) {
                        $managedUser->revokePermissionTo($staffPerm);
                        $mutated = true;
                    }
                }
                $managedUser->refresh();
                if ($managedUser->getAllPermissions()->isEmpty()) {
                    $managedUser->syncPermissions(User::defaultClientSpatiePermissions());
                    $mutated = true;
                }
            }
        } finally {
            setPermissionsTeamId($previousTeam);
        }

        if ($mutated) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
