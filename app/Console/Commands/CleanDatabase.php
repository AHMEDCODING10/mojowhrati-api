<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the database of all non-essential data (products, bookings, non-admin users) while preserving categories and admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full factory reset...');

        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        $tablesToTruncate = [
            'products',
            'product_images',
            'merchants',
            'bookings',
            'favorites',
            'reviews',
            'custom_design_orders',
            'reports',
            'contacts',
            'advertisements',
            'banners',
            'notifications',
            'audit_logs',
            'api_logs',
            'visitors',
            'gold_price_alerts',
            'transactions',
            'personal_access_tokens',
            'password_resets',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("Truncated table: $table");
            }
        }

        // --- Selective cleanup for Users ---
        $primaryEmail = 'ahmedtamis00@gmail.com';
        $primaryPassword = '770291452';

        // 1. Delete all users EXCEPT the primary one
        $deletedUsersCount = User::where('email', '!=', $primaryEmail)->delete();
        $this->info("Deleted $deletedUsersCount other users.");

        // 2. Ensure primary user exists or update it
        $admin = User::firstOrNew(['email' => $primaryEmail]);
        $admin->name = $admin->name ?? 'Super Admin';
        $admin->phone = $admin->phone ?? '770291452';
        $admin->password = bcrypt($primaryPassword);
        $admin->role = User::ROLE_SUPER_ADMIN;
        $admin->status = 'active';
        $admin->save();

        $this->info("Primary Admin ($primaryEmail) has been preserved/updated with new credentials.");

        // 3. Cleanup User Permissions - Recreate for Super Admin
        DB::table('user_permissions')->truncate();
        
        $screens = [
            'dashboard', 'merchants', 'products', 'bookings', 'custom_designs', 
            'categories', 'users', 'currencies', 'banners', 'notifications', 
            'gold_prices', 'contacts', 'settings', 'reports'
        ];

        foreach ($screens as $screen) {
            DB::table('user_permissions')->insert([
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
        $this->info("Permissions recreated for Super Admin.");

        // Clear Cache
        $this->call('cache:clear');
        $this->info('Application cache cleared.');

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->info('FULL FACTORY RESET COMPLETED!');
    }
}
