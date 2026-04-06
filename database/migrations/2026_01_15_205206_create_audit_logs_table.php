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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('entity_type'); // Users, Products, Bookings, etc.
            $table->unsignedBigInteger('entity_id');
            $table->enum('action', ['create', 'update', 'delete', 'login', 'logout', 
                                'approve', 'reject', 'status_change'])->default('update');
            $table->foreignId('actor_id')->constrained('Users');
            $table->enum('actor_type', ['customer', 'merchant', 'admin', 'system'])->default('admin');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_id', 'actor_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
