<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove previous admin accounts if any exist
        User::whereIn('email', ['ahmedsuper@gmail.com', 'osama@gmail.com'])->delete();

        // Single Super Admin
        User::updateOrCreate(
            ['email' => 'osamaalabarh83@gmail.con'],
            [
                'name' => 'admin',
                'phone' => '779997699',
                'password' => Hash::make('123456'),
                'role' => 'super_admin',
                'status' => 'active'
            ]
        );
    }
}
