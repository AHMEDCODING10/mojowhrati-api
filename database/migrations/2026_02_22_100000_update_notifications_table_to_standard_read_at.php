<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('message');
            }
        });

        // Migrate data from is_read to read_at if is_read exists
        if (Schema::hasColumn('notifications', 'is_read')) {
            DB::table('notifications')
                ->where('is_read', true)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            Schema::table('notifications', function (Blueprint $table) {
               // $table->dropColumn('is_read');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('message');
        });

        DB::table('notifications')
            ->whereNotNull('read_at')
            ->update(['is_read' => true]);

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
