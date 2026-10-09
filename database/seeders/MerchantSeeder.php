<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use App\Models\User;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        // Delete test merchant user if it exists
        $user = User::where('email', 'merchant@jewelry.com')->first();
        if ($user) {
            Merchant::where('user_id', $user->id)->delete();
            $user->delete();
        }
    }
}
