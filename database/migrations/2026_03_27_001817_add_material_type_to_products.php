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
            $table->enum('material_type', ['gold', 'silver', 'gemstone'])->default('gold')->after('category_id');
            $table->string('stone_type')->nullable()->after('material_type');
            $table->string('clarity')->nullable()->after('stone_type');
            $table->string('cut')->nullable()->after('clarity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['material_type', 'stone_type', 'clarity', 'cut']);
        });
    }
};
