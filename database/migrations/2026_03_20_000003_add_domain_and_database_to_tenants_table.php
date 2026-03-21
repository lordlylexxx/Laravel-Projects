<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('domain')->nullable()->after('slug');
            $table->string('database')->nullable()->after('domain');
            $table->string('db_host')->nullable()->after('database');
            $table->unsignedSmallInteger('db_port')->nullable()->after('db_host');
            $table->string('db_username')->nullable()->after('db_port');
            $table->text('db_password')->nullable()->after('db_username');

            $table->unique('domain');
            $table->index('database');
        });

        $baseDomain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        DB::table('tenants')
            ->select(['id', 'slug', 'name'])
            ->orderBy('id')
            ->chunkById(100, function ($tenants) use ($baseDomain) {
                foreach ($tenants as $tenant) {
                    $slug = $tenant->slug ?: Str::slug($tenant->name ?: 'tenant-' . $tenant->id);
                    $safeSlug = Str::slug($slug ?: ('tenant-' . $tenant->id));

                    $dbName = str_replace('-', '_', $safeSlug);
                    
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update([
                            'domain' => $safeSlug . '.' . $baseDomain,
                            'database' => $dbName,
                            'db_host' => config('database.connections.tenant.host', config('database.connections.mysql.host', '127.0.0.1')),
                            'db_port' => (int) config('database.connections.tenant.port', config('database.connections.mysql.port', 3306)),
                            'db_username' => config('database.connections.tenant.username', config('database.connections.mysql.username', 'root')),
                            'db_password' => Crypt::encryptString((string) config('database.connections.tenant.password', config('database.connections.mysql.password', ''))),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('tenants_database_index');
            $table->dropUnique('tenants_domain_unique');
            $table->dropColumn(['domain', 'database', 'db_host', 'db_port', 'db_username', 'db_password']);
        });
    }
};
