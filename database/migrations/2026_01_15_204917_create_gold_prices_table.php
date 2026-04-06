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
        Schema::create('gold_prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('purity', 4, 2); // 24.00, 22.00, 21.00, 18.00
            $table->decimal('price_per_gram_usd', 10, 4);
            $table->string('currency_code', 3)->default('USD');
            $table->string('source'); // اسم مصدر البيانات
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_updated');
            $table->json('raw_data')->nullable(); // البيانات الخام من API
            
            $table->index(['purity', 'is_active']);
            $table->index('last_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};
