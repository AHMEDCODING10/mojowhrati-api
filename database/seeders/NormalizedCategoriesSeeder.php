<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NormalizedCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'خواتم', 'slug' => 'rings', 'display_order' => 1],
            ['name' => 'سلاسل وعقود', 'slug' => 'necklaces', 'display_order' => 2],
            ['name' => 'أقراط وأخراص', 'slug' => 'earrings', 'display_order' => 3],
            ['name' => 'أساور', 'slug' => 'bracelets', 'display_order' => 4],
            ['name' => 'خلاخيل وانسيالات', 'slug' => 'anklets', 'display_order' => 5],
            ['name' => 'أطقم كاملة', 'slug' => 'full-sets', 'display_order' => 6],
            ['name' => 'سبائك وعملات', 'slug' => 'coins-bars', 'display_order' => 7],
            ['name' => 'ساعات فاخرة', 'slug' => 'luxury-watches', 'display_order' => 8],
            ['name' => 'زمامات', 'slug' => 'piercings', 'display_order' => 9],
            ['name' => 'تصاميم خاصة', 'slug' => 'custom-designs', 'display_order' => 10],
            ['name' => 'إكسسوارات أخرى', 'slug' => 'other-accessories', 'display_order' => 11],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'display_order' => $cat['display_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
