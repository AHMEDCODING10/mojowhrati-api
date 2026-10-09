<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class YemenGoldSeeder extends Seeder
{
    public function run(): void
    {
        // Materials (Gold Karats only, without price)
        $materials = [
            ['name' => 'ذهب عيار 24', 'unit' => 'gram'],
            ['name' => 'ذهب عيار 22', 'unit' => 'gram'],
            ['name' => 'ذهب عيار 21', 'unit' => 'gram'],
            ['name' => 'ذهب عيار 18', 'unit' => 'gram'],
        ];

        foreach ($materials as $mat) {
            Material::updateOrCreate(
                ['name' => $mat['name']],
                [
                    'unit' => $mat['unit'],
                ]
            );
        }
    }
}
