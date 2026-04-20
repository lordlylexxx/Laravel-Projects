<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->update([
            'tenant_permission_grants' => json_encode([]),
            'tenant_permission_revokes' => json_encode([]),
        ]);
    }

    public function down(): void
    {
        DB::table('users')->update([
            'tenant_permission_grants' => null,
            'tenant_permission_revokes' => null,
        ]);
    }
};
