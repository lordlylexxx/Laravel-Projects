<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure Spatie permission exists and merge into explicit client grant overrides when present.
     */
    public function up(): void
    {
        Permission::query()->firstOrCreate(
            ['name' => User::PERM_UPDATES_TICKETS_USE, 'guard_name' => 'web'],
        );

        $perm = User::PERM_UPDATES_TICKETS_USE;

        DB::table('users')->where('role', User::ROLE_CLIENT)->orderBy('id')->chunkById(200, function ($rows) use ($perm): void {
            foreach ($rows as $row) {
                $raw = $row->tenant_permission_grants ?? null;
                $decoded = $raw ? json_decode((string) $raw, true) : null;
                if (! is_array($decoded)) {
                    continue;
                }
                if (in_array($perm, $decoded, true)) {
                    continue;
                }
                $decoded[] = $perm;
                DB::table('users')->where('id', $row->id)->update([
                    'tenant_permission_grants' => json_encode(array_values(array_unique($decoded))),
                ]);
            }
        });
    }

    public function down(): void
    {
        $perm = User::PERM_UPDATES_TICKETS_USE;

        DB::table('users')->where('role', User::ROLE_CLIENT)->orderBy('id')->chunkById(200, function ($rows) use ($perm): void {
            foreach ($rows as $row) {
                $raw = $row->tenant_permission_grants ?? null;
                $decoded = $raw ? json_decode((string) $raw, true) : null;
                if (! is_array($decoded)) {
                    continue;
                }
                $next = array_values(array_filter($decoded, static fn ($p) => $p !== $perm));
                if ($next === $decoded) {
                    continue;
                }
                DB::table('users')->where('id', $row->id)->update([
                    'tenant_permission_grants' => json_encode($next),
                ]);
            }
        });
    }
};
