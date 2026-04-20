<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\PermissionRegistrar;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->assertTenantGuestMayEditProfile();
        $this->assertTenantAdminHasAnyPermission($user);

        return view('profile.new-edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->assertTenantGuestMayEditProfile();
        $this->assertTenantAdminHasAnyPermission($user);

        // Update basic info
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle additional profile fields
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('address')) {
            $user->address = $request->address;
        }

        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = basename($avatarPath);
        }

        $user->notification_preferences = [
            'booking_updates' => (bool) $request->boolean('notify_booking_updates'),
            'messages' => (bool) $request->boolean('notify_messages'),
            'marketing' => (bool) $request->boolean('notify_marketing'),
        ];

        $user->save();

        return Redirect::to('/profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->assertTenantGuestMayEditProfile();
        $this->assertTenantAdminHasAnyPermission($user);

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function assertTenantAdminHasAnyPermission(User $user): void
    {
        $tenant = Tenant::current();

        if (! $tenant || ! $user->isAdmin()) {
            return;
        }

        if ((int) ($user->tenant_id ?? 0) !== (int) $tenant->id) {
            return;
        }

        $permissions = [
            User::PERM_USERS_VIEW,
            User::PERM_ACCOMMODATIONS_CREATE,
            User::PERM_ACCOMMODATIONS_UPDATE,
            User::PERM_ACCOMMODATIONS_DELETE,
            User::PERM_BOOKINGS_MANAGE,
            User::PERM_MESSAGES_MANAGE,
            User::PERM_REPORTS_VIEW,
        ];

        $allowed = collect($permissions)->contains(fn (string $permission): bool => $user->hasPermission($permission));
        if (! $allowed) {
            RbacCatalog::ensurePermissionsExist();
            RbacCatalog::ensureRolesAndGrantPermissions();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $user->syncRbacFromLegacyRole();
            $user->syncEffectiveTenantPermissions($tenant);
            $user->refresh();
            $allowed = collect($permissions)->contains(fn (string $permission): bool => $user->hasPermission($permission));
        }

        abort_unless($allowed, 403);
    }
}
