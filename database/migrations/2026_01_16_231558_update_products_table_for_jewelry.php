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
            $table->foreignId('material_id')->nullable()->after('category_id')->constrained('materials');
            $table->integer('quantity')->default(1)->after('type');
            $table->decimal('service_fee', 12, 2)->default(0)->after('manufacturer_price'); // أجرة المصنعية للقطعة أو للجرام
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn(['material_id', 'quantity', 'service_fee']);
        });
    }

};
