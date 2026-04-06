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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['manufacturer_price', 'base_price', 'final_price', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('manufacturer_price', 15, 2)->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('final_price', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
        });
    }
};
