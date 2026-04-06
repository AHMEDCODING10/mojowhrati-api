<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        \App\Models\User::updateOrCreate(
            ['email' => 'ahmedsuper@gmail.com'],
            [
                'name' => 'المدير العام (أحمد)',
                'phone' => '777777777',
                'password' => bcrypt('123456'),
                'role' => 'super_admin',
                'status' => 'active'
            ]
        );

        // Admin
        \App\Models\User::updateOrCreate(
            ['email' => 'osama@gmail.com'],
            [
                'name' => 'مدير النظام (أسامة)',
                'phone' => '777000000',
                'password' => bcrypt('123456'),
                'role' => 'admin',
                'status' => 'active'
            ]
        );
    }
}
