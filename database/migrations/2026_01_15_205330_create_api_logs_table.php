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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('api_name'); // gold_price_api, payment_gateway, etc.
            $table->string('endpoint');
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->integer('status_code');
            $table->float('response_time'); // بالثواني
            $table->string('ip_address', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('Users');
            
            $table->index(['api_name', 'status_code']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
