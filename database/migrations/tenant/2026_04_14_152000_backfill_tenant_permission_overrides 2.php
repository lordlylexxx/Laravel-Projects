<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('tenant_permission_grants')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $role = (string) ($row->role ?? User::ROLE_CLIENT);
                    $grants = match ($role) {
                        User::ROLE_OWNER => User::defaultOwnerSpatiePermissions(),
                        User::ROLE_ADMIN => User::defaultTenantAdminSpatiePermissions(),
                        User::ROLE_CLIENT => User::defaultClientSpatiePermissions(),
                        default => [],
                    };

                    DB::table('users')
                        ->where('id', $row->id)
                        ->update([
                            'tenant_permission_grants' => json_encode(array_values($grants)),
                            'tenant_permission_revokes' => json_encode([]),
                        ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('users')->update([
            'tenant_permission_grants' => null,
            'tenant_permission_revokes' => null,
        ]);
    }
};
