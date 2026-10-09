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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onDelete('cascade');
            $table->string('type'); // category, product, merchant_store, banner
            $table->unsignedBigInteger('target_id')->nullable(); // category_id or product_id
            $table->string('title')->nullable();
            $table->string('placement')->default('HOME_TOP'); // HOME_TOP, CATEGORY_TOP, SEARCH_BOOST, FEATURED_SLIDER
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('priority')->default(0);
            $table->decimal('fee_amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('paid'); // paid, unpaid, waived
            $table->string('status')->default('active'); // active, paused, expired
            $table->unsignedBigInteger('impressions_count')->default(0);
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status', 'start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
