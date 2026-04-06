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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('weight', 10, 3)->nullable(); // بالجرام
            // removed redundant purity field as it is in material table
            $table->string('type'); // خاتم، سوار، قلادة
            $table->string('manufacturer')->nullable(); // اسم الصانع
            $table->decimal('manufacturer_price', 12, 2)->nullable(); // تكلفة التصنيع
            $table->decimal('base_price', 12, 2)->nullable(); // سعر أساسي
            $table->decimal('final_price', 12, 2)->nullable(); // السعر النهائي بعد حساب سعر الذهب
            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected', 'sold', 'archived'])->default('draft');
            $table->text('review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->json('specifications')->nullable(); // مواصفات إضافية
            
            $table->index(['merchant_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['weight']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
