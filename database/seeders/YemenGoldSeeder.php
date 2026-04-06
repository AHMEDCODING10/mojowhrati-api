<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Material;
use Illuminate\Support\Str;

class YemenGoldSeeder extends Seeder
{
    public function run(): void
    {
        // Hierarchical Categories
        $categories = [
            'خواتم' => [
                'خواتم نسائية',
                'خواتم رجالية',
                'خواتم خطوبة',
                'خواتم زواج',
                'خواتم ألماس',
            ],
            'أطقم كاملة' => [],
            'قلائد وسلاسل' => [],
            'أساور وبناجر' => [],
            'أقراط (تراكي)' => [],
            'مجوهرات أطفال' => [],
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName, '-')],
                [
                    'name' => $parentName,
                    'is_active' => true,
                ]
            );

            foreach ($children as $childName) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($childName, '-')],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Materials (Gold Rates)
        $materials = [
            ['name' => 'ذهب عيار 24', 'unit' => 'gram', 'current_rate' => 65.50],
            ['name' => 'ذهب عيار 22', 'unit' => 'gram', 'current_rate' => 60.20],
            ['name' => 'ذهب عيار 21', 'unit' => 'gram', 'current_rate' => 57.40],
            ['name' => 'ذهب عيار 18', 'unit' => 'gram', 'current_rate' => 49.30],
        ];

        foreach ($materials as $mat) {
            Material::updateOrCreate(
                ['name' => $mat['name']],
                $mat
            );
        }
    }
}
