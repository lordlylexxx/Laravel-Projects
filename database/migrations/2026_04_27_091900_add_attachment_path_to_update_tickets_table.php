<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $landlord = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($landlord)->table('update_tickets', function (Blueprint $table) use ($landlord): void {
            if (! Schema::connection($landlord)->hasColumn('update_tickets', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        $landlord = (string) config('multitenancy.landlord_database_connection_name', 'landlord');

        Schema::connection($landlord)->table('update_tickets', function (Blueprint $table) use ($landlord): void {
            if (Schema::connection($landlord)->hasColumn('update_tickets', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
