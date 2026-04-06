<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
/**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        // قمنا بتعطيل الـ FullText لتجاوز قيود TiFlash في النسخة المجانية
        // $table->fullText('title');
        // $table->fullText('description');

        // سنبقي هذه الفهارس لأنها مدعومة وسريعة جداً
        $table->index(['status', 'is_featured']);
        $table->index(['status', 'created_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText(['title', 'description']);
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
