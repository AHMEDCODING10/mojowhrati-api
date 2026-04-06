<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Who this notification is targeted at (for broadcast tracking)
            $table->string('target')->default('all')->after('type')
                ->comment('all, merchants, customers, staff');

            // For scheduled delivery
            $table->timestamp('scheduled_at')->nullable()->after('target');

            // Whether this notification has been dispatched (for scheduled ones)
            $table->boolean('is_dispatched')->default(true)->after('scheduled_at');

            // Make notifiable_id/type nullable if they exist
            // (they might not exist — this is safe)
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['target', 'scheduled_at', 'is_dispatched']);
        });
    }
};
