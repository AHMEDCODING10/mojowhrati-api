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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('screen');
            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'screen']);
        });

        // Seed existing admins with full permissions
        $admins = \DB::table('users')->whereIn('role', ['admin', 'moderator', 'support', 'super_admin'])->get();
        $screens = [
            'dashboard', 'merchants', 'products', 'bookings', 'custom_designs', 
            'categories', 'users', 'currencies', 'banners', 'notifications', 
            'gold_prices', 'contacts', 'settings', 'reports'
        ];

        foreach ($admins as $admin) {
            foreach ($screens as $screen) {
                \DB::table('user_permissions')->insert([
                    'user_id' => $admin->id,
                    'screen' => $screen,
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
