<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = Merchant::first();
        $category = Category::whereNotNull('parent_id')->first() ?: Category::first();
        $material = Material::where('karat', 21)->first() ?: Material::first();

        if (!$merchant || !$category || !$material) {
            return;
        }

        $products = [
            [
                'merchant_id' => $merchant->id,
                'category_id' => $category->id,
                'material_id' => $material->id,
                'title' => 'خاتم بحريني ملكي',
                'slug' => Str::slug('خاتم بحريني ملكي ' . Str::random(5)),
                'description' => 'خاتم ذهب عيار 21 بتصميم بحريني تراثي فاخر',
                'weight' => 5.5,
                'type' => 'خاتم',
                'quantity' => 10,
                'service_fee' => 3500,
                'manufacturer_price' => 5000,
                'base_price' => 55000,
                'final_price' => (5.5 * 58500),
                'status' => 'published',
            ],
            [
                'merchant_id' => $merchant->id,
                'category_id' => $category->id,
                'material_id' => $material->id,
                'title' => 'عقد لازوردي مودرن',
                'slug' => Str::slug('عقد لازوردي مودرن ' . Str::random(5)),
                'description' => 'عقد ذهب ناعم بتصميم عصري من تشكيلة لازوردي',
                'weight' => 12.0,
                'type' => 'عقد',
                'quantity' => 5,
                'service_fee' => 4500,
                'manufacturer_price' => 8000,
                'base_price' => 55000,
                'final_price' => (12.0 * 59500),
                'status' => 'published',
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
