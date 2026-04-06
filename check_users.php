<?php
use App\Models\User;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = User::all();
foreach ($users as $user) {
    echo "ID: " . $user->id . " | Phone: " . $user->phone . " | Role: " . $user->role . "\n";
}
