<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Mail\TenantUserWelcomeMail;
use App\Models\Tenant;
use App\Models\TenantCustomRole;
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
        $customRbacReady = User::tenantCustomRbacSchemaReady();

        abort_unless($this->canManageUsers($actor), 403);

        $this->bootstrapTenantSpatieRbac();

        $users = User::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('email', 'not like', '__impastay_central_support.tenant-%')
            ->when($customRbacReady, fn ($query) => $query->with(['tenantCustomRole.permissions']))
            ->orderByDesc('id')
            ->paginate(5);

        $this->ensureVisibleUsersHaveSyncedPermissions($users->getCollection(), $currentTenant);

        $tenantCustomRoles = $customRbacReady
            ? TenantCustomRole::query()
                ->where('tenant_id', $currentTenant->id)
                ->with('permissions')
                ->orderBy('name')
                ->get()
            : collect();

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
            'tenantCustomRoles' => $tenantCustomRoles,
            'customRoleAssignablePermissions' => $customRbacReady ? $this->assignableCustomRolePermissions($actor) : [],
            'customRbacReady' => $customRbacReady,
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
        ]);

        $role = User::ROLE_CLIENT;
        $tenantCustomRoleId = null;
        if (User::tenantCustomRbacSchemaReady()) {
            $extra = $request->validate([
                'role_selection' => ['required', 'string'],
            ]);
            [$role, $tenantCustomRoleId] = $this->parseRoleSelection(
                (string) $extra['role_selection'],
                $currentTenant
            );
        } else {
            $extra = $request->validate([
                'role' => ['required', 'in:admin,client'],
            ]);
            $role = (string) $extra['role'];
        }

        if (! in_array($role, $this->assignableRoles($actor), true)) {
            return back()->withErrors(['role_selection' => 'You are not allowed to assign this role.'])->withInput();
        }

        $plainPassword = Str::password(16, true, true, true, false);

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => $plainPassword,
            'role' => $role,
            'tenant_id' => $currentTenant->id,
            'is_active' => true,
        ];

        if (User::tenantCustomRbacSchemaReady()) {
            $payload['tenant_custom_role_id'] = $tenantCustomRoleId;
            $payload['tenant_permission_grants'] = [];
            $payload['tenant_permission_revokes'] = [];
        }

        $user = User::create($payload);

        $user->syncRbacFromLegacyRole();
        $user->syncEffectiveTenantPermissions($currentTenant);

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
        ]);

        $role = User::ROLE_CLIENT;
        $tenantCustomRoleId = null;
        if (User::tenantCustomRbacSchemaReady()) {
            $extra = $request->validate([
                'role_selection' => ['required', 'string'],
            ]);
            [$role, $tenantCustomRoleId] = $this->parseRoleSelection(
                (string) $extra['role_selection'],
                $currentTenant
            );
        } else {
            $extra = $request->validate([
                'role' => ['required', 'in:admin,client'],
            ]);
            $role = (string) $extra['role'];
        }

        if (! in_array($role, $this->assignableRoles($actor), true)) {
            return back()->withErrors(['role_selection' => 'You are not allowed to assign this role.']);
        }

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $role,
        ];

        if (User::tenantCustomRbacSchemaReady()) {
            $payload['tenant_custom_role_id'] = $tenantCustomRoleId;
        }

        $user->update($payload);

        $user->syncRbacFromLegacyRole();
        $user->syncEffectiveTenantPermissions($currentTenant);

        return redirect('/owner/users')->with('success', 'Tenant user updated.');
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canAssignPermissions($actor), 403);
        $this->assertManageableUser($actor, $user, $currentTenant);
        $user->syncTenantPermissionOverrides([], [], $currentTenant);

        return redirect('/owner/users')->with('warning', 'Per-user permission overrides are disabled. Manage access via role templates only.');
    }

    public function storeCustomRole(Request $request): RedirectResponse
    {
        abort_unless(User::tenantCustomRbacSchemaReady(), 503, 'Tenant RBAC schema is not available yet.');

        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canAssignRoles($actor), 403);

        $allowedPermissions = $this->assignableCustomRolePermissions($actor);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', $allowedPermissions)],
        ]);

        $baseSlug = TenantCustomRole::normalizeSlug($validated['name']);
        $slug = $baseSlug;
        $suffix = 2;
        while (TenantCustomRole::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $role = TenantCustomRole::create([
            'tenant_id' => $currentTenant->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncCustomRolePermissions($role, $validated['permissions'] ?? [], $allowedPermissions);

        return redirect('/owner/users')->with('success', 'Custom role created.');
    }

    public function updateCustomRole(Request $request, TenantCustomRole $tenantCustomRole): RedirectResponse
    {
        abort_unless(User::tenantCustomRbacSchemaReady(), 503, 'Tenant RBAC schema is not available yet.');

        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canAssignRoles($actor), 403);
        abort_unless((int) $tenantCustomRole->tenant_id === (int) $currentTenant->id, 404);

        $allowedPermissions = $this->assignableCustomRolePermissions($actor);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', $allowedPermissions)],
        ]);

        $baseSlug = TenantCustomRole::normalizeSlug($validated['name']);
        $slug = $baseSlug;
        $suffix = 2;
        while (TenantCustomRole::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('slug', $slug)
            ->whereKeyNot($tenantCustomRole->id)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $tenantCustomRole->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncCustomRolePermissions($tenantCustomRole, $validated['permissions'] ?? [], $allowedPermissions);
        $this->resyncUsersForCustomRole($tenantCustomRole, $currentTenant);

        return redirect('/owner/users')->with('success', 'Custom role updated.');
    }

    public function destroyCustomRole(Request $request, TenantCustomRole $tenantCustomRole): RedirectResponse
    {
        abort_unless(User::tenantCustomRbacSchemaReady(), 503, 'Tenant RBAC schema is not available yet.');

        $currentTenant = $this->currentTenantOrFail();
        $actor = $request->user();

        abort_unless($this->canAssignRoles($actor), 403);
        abort_unless((int) $tenantCustomRole->tenant_id === (int) $currentTenant->id, 404);

        $affectedUserIds = User::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('tenant_custom_role_id', $tenantCustomRole->id)
            ->pluck('id');

        User::query()
            ->whereIn('id', $affectedUserIds)
            ->update(['tenant_custom_role_id' => null]);

        $tenantCustomRole->delete();

        User::query()
            ->whereIn('id', $affectedUserIds)
            ->get()
            ->each(function (User $managedUser) use ($currentTenant): void {
                $managedUser->syncEffectiveTenantPermissions($currentTenant);
            });

        return redirect('/owner/users')->with('success', 'Custom role deleted.');
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
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_VIEW);
    }

    private function canCreateUsers(User $actor): bool
    {
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_CREATE);
    }

    private function canEditUsers(User $actor): bool
    {
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_UPDATE);
    }

    private function canToggleUsers(User $actor): bool
    {
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_ACTIVATE);
    }

    private function canAssignRoles(User $actor): bool
    {
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_ASSIGN_ROLES);
    }

    private function canAssignPermissions(User $actor): bool
    {
        return $this->ownerOrTenantAdminWithPermission($actor, User::PERM_USERS_ASSIGN_PERMISSIONS);
    }

    /**
     * Owner has full access; tenant admin access is permission-gated by role template.
     */
    private function ownerOrTenantAdminWithPermission(User $actor, string $permission): bool
    {
        if ($actor->isOwner()) {
            return true;
        }

        return $actor->isAdmin()
            && $this->isTenantScopedActor($actor)
            && $actor->hasPermission($permission);
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
        abort_if((int) $actor->id === (int) $managedUser->id, 403);

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
     * @return list<string>
     */
    private function assignableCustomRolePermissions(User $actor): array
    {
        if (! $this->canAssignRoles($actor)) {
            return [];
        }

        if ($this->tenantManagerEquivalentToOwner($actor)) {
            return User::staffSpatiePermissionNames();
        }

        return $this->assignableStaffPermissions($actor);
    }

    private function validatedTenantCustomRoleId(mixed $candidateId, Tenant $currentTenant): ?int
    {
        if ($candidateId === null || $candidateId === '') {
            return null;
        }

        $tenantCustomRole = TenantCustomRole::query()
            ->where('tenant_id', $currentTenant->id)
            ->whereKey((int) $candidateId)
            ->first();

        if (! $tenantCustomRole) {
            return null;
        }

        return (int) $tenantCustomRole->id;
    }

    /**
     * @return array{0:string,1:?int}
     */
    private function parseRoleSelection(string $selection, Tenant $currentTenant): array
    {
        if (str_starts_with($selection, 'custom:')) {
            $customId = (int) substr($selection, strlen('custom:'));
            $tenantCustomRole = TenantCustomRole::query()
                ->where('tenant_id', $currentTenant->id)
                ->whereKey($customId)
                ->first();

            if (! $tenantCustomRole) {
                return [User::ROLE_CLIENT, null];
            }

            return [$this->baseRoleForCustomTemplate($tenantCustomRole), (int) $tenantCustomRole->id];
        }

        return match ($selection) {
            'core:admin' => [User::ROLE_ADMIN, null],
            'core:client' => [User::ROLE_CLIENT, null],
            default => [User::ROLE_CLIENT, null],
        };
    }

    private function baseRoleForCustomTemplate(TenantCustomRole $tenantCustomRole): string
    {
        return User::ROLE_ADMIN;
    }

    /**
     * @param  list<string>  $selectedPermissions
     * @param  list<string>  $allowedPermissions
     */
    private function syncCustomRolePermissions(
        TenantCustomRole $tenantCustomRole,
        array $selectedPermissions,
        array $allowedPermissions
    ): void {
        $allowedLookup = array_fill_keys($allowedPermissions, true);
        $normalized = array_values(array_unique(array_filter(
            $selectedPermissions,
            static fn ($perm): bool => is_string($perm) && isset($allowedLookup[$perm])
        )));

        $tenantCustomRole->permissions()->delete();

        foreach ($normalized as $permissionName) {
            $tenantCustomRole->permissions()->create([
                'permission_name' => $permissionName,
            ]);
        }
    }

    private function resyncUsersForCustomRole(TenantCustomRole $tenantCustomRole, Tenant $currentTenant): void
    {
        User::query()
            ->where('tenant_id', $currentTenant->id)
            ->where('tenant_custom_role_id', $tenantCustomRole->id)
            ->each(function (User $managedUser) use ($currentTenant): void {
                $managedUser->syncEffectiveTenantPermissions($currentTenant);
            });
    }

    /**
     * Strip mistaken staff direct grants from client rows. Does not add default guest permissions:
     * an empty set is valid (tenant admin disabled all capabilities) and must persist across page loads.
     *
     * @param  Collection<int, User>  $users
     */
    private function ensureVisibleUsersHaveSyncedPermissions(Collection $users, Tenant $currentTenant): void
    {
        foreach ($users as $managedUser) {
            $managedUser->syncEffectiveTenantPermissions($currentTenant);
        }
    }
}
