<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->after('status')->comment('رمز الإحالة من مسوق');
            $table->foreignId('marketer_id')->nullable()->after('referral_code')
                  ->constrained('marketers')->onDelete('set null')
                  ->comment('المسوق الذي أضاف هذا المستخدم');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['marketer_id']);
            $table->dropColumn(['referral_code', 'marketer_id']);
        });
    }
};
