<?php
use App\Models\User;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = User::all();
echo "--- Users in Database ---\n";
foreach ($users as $user) {
    echo "ID: " . $user->id . " | Phone: " . $user->phone . " | Role: " . $user->role . " | Status: " . $user->status . "\n";
}

// Automatically activate and set password for the merchant for testing
$merchantUser = User::where('role', 'merchant')->first();
if ($merchantUser) {
    $merchantUser->status = 'active';
    $merchantUser->password = \Illuminate\Support\Facades\Hash::make('12345678');
    $merchantUser->save();
    echo "\n--- TEST MERCHANT UPDATED ---\n";
    echo "Phone: " . $merchantUser->phone . "\n";
    echo "New Password: 12345678\n";
    echo "Status: active\n";
}
