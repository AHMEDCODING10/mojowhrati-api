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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('merchant_id')->constrained('merchants');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'expired', 'completed', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('contact_visible')->default(false);
            $table->text('customer_notes')->nullable();
            $table->text('merchant_notes')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->index(['product_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['merchant_id', 'status']);
            $table->index('expires_at');
            $table->unique(['product_id'])->where('status', 'pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
