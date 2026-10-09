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
        Schema::create('promotion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
            $table->string('type'); // product, category, all_categories
            $table->unsignedBigInteger('category_id')->nullable();
            $table->json('product_ids')->nullable();
            $table->integer('duration_weeks')->default(1);
            $table->decimal('fee_amount', 10, 2)->default(0.00);
            $table->string('payment_receipt_url')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_requests');
    }
};
