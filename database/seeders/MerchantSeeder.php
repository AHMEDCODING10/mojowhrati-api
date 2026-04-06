<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'مجوهرات النخبة',
            'email' => 'merchant@jewelry.com',
            'phone' => '771234567',
            'password' => Hash::make('password'),
            'role' => 'merchant',
        ]);

        Merchant::create([
            'user_id' => $user->id,
            'store_name' => 'مجوهرات النخبة - صنعاء',
            'contact_number' => '771234567',
            'whatsapp_number' => '967771234567',
            'address' => 'صنعاء - شارع حده',
            'store_description' => 'أرقى المجوهرات والحلي الذهبية عيار 21 و 18',
            'store_status' => 'active',
            'approved' => true,
        ]);
    }
}
