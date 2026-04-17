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
        // Fix audit_logs actor_id
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
            $table->foreign('actor_id')->references('id')->on('Users')->onDelete('cascade');
        });

        // Fix merchants approved_by
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
            $table->foreign('actor_id')->references('id')->on('Users');
        });

        Schema::table('merchants', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }
};
