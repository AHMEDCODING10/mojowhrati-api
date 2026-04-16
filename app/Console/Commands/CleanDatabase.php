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
        $this->info('Starting database cleanup...');

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

        // Selective cleanup for Users
        $adminRoles = [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_MODERATOR,
            User::ROLE_SUPPORT
        ];

        $deletedUsersCount = User::whereNotIn('role', $adminRoles)->delete();
        $this->info("Deleted $deletedUsersCount non-admin users.");

        // Cleanup User Permissions if they belong to deleted users
        $staffIds = User::whereIn('role', $adminRoles)->pluck('id');
        $deletedPermissionsCount = DB::table('user_permissions')->whereNotIn('user_id', $staffIds)->delete();
        $this->info("Deleted $deletedPermissionsCount orphaned user permissions.");

        // Clear Cache
        $this->call('cache:clear');
        $this->info('Application cache cleared.');

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->info('Database cleanup completed successfully!');
    }
}
