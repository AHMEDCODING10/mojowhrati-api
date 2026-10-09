<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Delete test products if they exist
        Product::whereIn('title', ['خاتم بحريني ملكي', 'عقد لازوردي مودرن'])->forceDelete();
    }
}
