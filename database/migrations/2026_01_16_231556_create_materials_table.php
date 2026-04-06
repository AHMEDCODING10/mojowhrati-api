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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ذهب، فضة، ألماس
            $table->integer('karat')->nullable(); // 21, 18, 24
            $table->decimal('current_rate', 12, 2)->default(0); // سعر الجرام الحالي
            $table->string('unit')->default('gram');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
