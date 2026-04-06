<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $col) {
            $col->string('type')->default('image')->after('title');
            $col->string('video_url')->nullable()->after('type');
            $col->string('target')->default('all')->after('video_url');
            $col->string('placement')->default('home_top')->after('target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $col) {
            $col->dropColumn(['type', 'video_url', 'target', 'placement']);
        });
    }
};
