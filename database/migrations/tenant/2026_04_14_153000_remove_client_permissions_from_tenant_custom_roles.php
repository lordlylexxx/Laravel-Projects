<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tenant_custom_role_permissions')
            ->whereIn('permission_name', [
                'bookings.self',
                'messages.use',
                'profile.self',
            ])
            ->delete();
    }

    public function down(): void
    {
        // No-op: removed rows cannot be restored safely.
    }
};
