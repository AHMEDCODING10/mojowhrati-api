<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── جدول المسوقين الأساسي ─────────────────────────────
        Schema::create('marketers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('code')->unique()->comment('رمز الإحالة الخاص بالمسوق');
            $table->enum('target_type', ['merchant', 'customer', 'both'])->default('both')->comment('نوع الحسابات التي يضيفها');
            $table->string('currency', 10)->default('SAR')->comment('عملة العمولة');
            $table->decimal('commission_per_merchant', 10, 2)->default(0)->comment('عمولة إضافة تاجر واحد');
            $table->decimal('commission_per_customer', 10, 2)->default(0)->comment('عمولة إضافة عميل واحد');
            $table->decimal('total_earned', 14, 2)->default(0)->comment('إجمالي العمولات المكتسبة');
            $table->decimal('total_paid', 14, 2)->default(0)->comment('إجمالي المبالغ المدفوعة للمسوق');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ─── جدول سجل العمولات ────────────────────────────────
        Schema::create('marketer_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->constrained('marketers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('المستخدم الذي تم إضافته');
            $table->enum('user_type', ['merchant', 'customer']);
            $table->decimal('amount', 10, 2)->comment('قيمة العمولة');
            $table->string('currency', 10)->default('SAR');
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketer_commissions');
        Schema::dropIfExists('marketers');
    }
};
