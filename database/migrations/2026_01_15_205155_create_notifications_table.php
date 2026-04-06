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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('Users')->onDelete('cascade');
            $table->enum('type', ['booking_created', 'booking_confirmed', 'booking_rejected', 
                                'product_reviewed', 'merchant_approved', 'price_alert', 
                                'system_announcement', 'new_message'])->default('system_announcement');
            $table->string('title');
            $table->text('message');
            $table->string('related_entity_type')->nullable(); // Product, Booking, etc.
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->json('data')->nullable(); // بيانات إضافية
            
            $table->index(['user_id', 'is_read']);
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
