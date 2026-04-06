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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->enum('display_location', ['home_top', 'home_middle', 'home_bottom', 
                                            'category_top', 'product_sidebar', 'all_pages'])->default('home_top');
            $table->string('target_url')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->integer('clicks_count')->default(0);
            $table->integer('impressions_count')->default(0);
            $table->foreignId('created_by')->constrained('Users');
            
            $table->index(['start_date', 'end_date', 'is_active']);
            $table->index('display_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
