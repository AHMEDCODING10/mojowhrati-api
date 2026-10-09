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
        Schema::create('app_updates', function (Blueprint $table) {
            $table->id();
            $table->string('version_number');
            $table->text('release_notes');
            $table->enum('target_audience', ['all', 'customer', 'merchant'])->default('all');
            $table->string('apk_file_url')->nullable();
            $table->string('play_store_url')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_force_update')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_updates');
    }
};
