<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SyncLiveProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clean wiping
        DB::statement('PRAGMA foreign_keys = OFF;');

        ProductImage::query()->delete();
        Product::query()->delete();
        Category::query()->delete();

        DB::statement('PRAGMA foreign_keys = ON;');

        // 1. Ensure Materials exist
        $mat21 = Material::firstOrCreate(['name' => 'ذهب عيار 21'], ['current_rate' => 57.40, 'unit' => 'gram']);
        $mat18 = Material::firstOrCreate(['name' => 'ذهب عيار 18'], ['current_rate' => 49.30, 'unit' => 'gram']);

        // 2. Real Merchants from live system
        $merchant1User = User::firstOrCreate(
            ['phone' => '774938200'],
            [
                'name' => 'دار مجوهراتي للذهب والمجوهرات',
                'email' => 'darmjawharaty@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'status' => 'active'
            ]
        );

        $merchant1 = Merchant::firstOrCreate(
            ['user_id' => $merchant1User->id],
            [
                'store_name' => 'دار مجوهراتي للذهب والمجوهرات',
                'contact_number' => '774938200',
                'whatsapp_number' => '774938200',
                'store_description' => 'متجر ذهب ومجوهرات',
                'store_status' => 'active',
                'approved' => true,
            ]
        );

        $merchant2User = User::firstOrCreate(
            ['phone' => '777828608'],
            [
                'name' => 'مجوهرات اوبرا هاوس',
                'email' => 'operahouse@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'merchant',
                'status' => 'active'
            ]
        );

        $merchant2 = Merchant::firstOrCreate(
            ['user_id' => $merchant2User->id],
            [
                'store_name' => 'مجوهرات اوبرا هاوس',
                'contact_number' => '777828608',
                'whatsapp_number' => '777828608',
                'store_description' => 'مجوهرات وحلي فاخرة',
                'store_status' => 'active',
                'approved' => true,
            ]
        );

        // 3. Real 15 Categories from live system
        $liveCategoriesData = [
            ['name' => 'الاطقم', 'slug' => 'alatkm', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422234_4vYQQ_aJ02HfIHY.jpg', 'display_order' => 1],
            ['name' => 'الاساور', 'slug' => 'alasaor', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422330_vUxGY_nBHxDNBbt.jpg', 'display_order' => 2],
            ['name' => 'الاحزمة', 'slug' => 'alahzm', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422408_wrrGb_kcJCufKFb.jpg', 'display_order' => 3],
            ['name' => 'الخواتم', 'slug' => 'alkhoatm', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422468_PnIko_kjl9cqVLP.jpg', 'display_order' => 4],
            ['name' => 'الانسيالات', 'slug' => 'alansyalat', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422526_v80xW_CbSwSSPlt.jpg', 'display_order' => 5],
            ['name' => 'العقود', 'slug' => 'alaakod', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422586_ML5R5_FVb65yN5L.jpg', 'display_order' => 6],
            ['name' => 'الشوالي', 'slug' => 'alshoaly', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776422682_V8hMB_d7prUP9tq.jpg', 'display_order' => 7],
            ['name' => 'السلاسل', 'slug' => 'alslasl', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776423657_knDZ1_hMQP288ZX.jpg', 'display_order' => 8],
            ['name' => 'التعاليق', 'slug' => 'altaaalyk', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776423340_6IpZ2_SkEDlrF-n.jpg', 'display_order' => 9],
            ['name' => 'الكفوف', 'slug' => 'alkfof', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776423721_777Cu_f9qrxKb-E.jpg', 'display_order' => 10],
            ['name' => 'الجنيهات', 'slug' => 'algnyhat', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776423883_PNhHx_glBYRP9eh.jpg', 'display_order' => 11],
            ['name' => 'الزمامات', 'slug' => 'alzmamat', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776423949_er0DG_iKoUAhtJD.jpg', 'display_order' => 12],
            ['name' => 'اخرئ', 'slug' => 'akhry', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776452568_81tP0_h5wpz16N7.jpg', 'display_order' => 13],
            ['name' => 'الإنصاف', 'slug' => 'alansaf', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776463741_eyqkZ_JarOlg_u9.jpg', 'display_order' => 14],
            ['name' => 'السبائك', 'slug' => 'alsbayk', 'image' => 'https://ik.imagekit.io/6vzmizcti/1776463926_NzWJ7_cEKwAh08H.jpg', 'display_order' => 15],
        ];

        $categories = [];
        foreach ($liveCategoriesData as $catData) {
            $cat = Category::create([
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'image' => $catData['image'],
                'display_order' => $catData['display_order'],
                'is_active' => true,
            ]);
            $categories[$catData['slug']] = $cat;
        }

        // 4. Real Products from live system
        $realProducts = [
            [
                'title' => 'أسواره مرجان ايطالي معى ذهب خليجي',
                'slug' => 'asoarh-mrgan-aytaly-maa-thhb-khlygy',
                'description' => 'مرجان ايطالي',
                'weight' => 4.3,
                'stone_weight' => 13.69,
                'type' => 'jewelry',
                'status' => 'published',
                'purity' => '21',
                'material_type' => 'gemstone',
                'stone_type' => 'مرجان ايطالي',
                'stock_quantity' => 5,
                'merchant_id' => $merchant1->id,
                'category_id' => $categories['alasaor']->id,
                'material_id' => $mat21->id,
                'images' => [
                    'https://ik.imagekit.io/6vzmizcti/1776464342_XCTUC_q9AkHxg-E.jpg',
                    'https://ik.imagekit.io/6vzmizcti/1776464343_LWzZZ_pczoZb_N7.jpg'
                ]
            ],
            [
                'title' => 'عقد مرجان ايطالي معى ذهب خليجي',
                'slug' => 'aakd-mrgan-aytaly-maa-thhb-khlygy',
                'description' => 'مرجان ايطالي',
                'weight' => 7.88,
                'stone_weight' => 22.6,
                'type' => 'jewelry',
                'status' => 'published',
                'purity' => '21',
                'material_type' => 'gemstone',
                'stone_type' => 'مرجان ايطالي',
                'stock_quantity' => 2,
                'merchant_id' => $merchant1->id,
                'category_id' => $categories['alaakod']->id,
                'material_id' => $mat21->id,
                'images' => [
                    'https://ik.imagekit.io/6vzmizcti/1776464460_W8Tmc_NbsV9BZ1i.jpg'
                ]
            ],
            [
                'title' => 'عقد ذهب ايطالي معى فصوص الماس',
                'slug' => 'aakd-thhb-aytaly-maa-fsos-almas',
                'description' => 'عقد ذهب ايطالي معى الماس طبيعي',
                'weight' => 14.2,
                'stone_weight' => 16.9,
                'type' => 'jewelry',
                'status' => 'published',
                'purity' => '18',
                'material_type' => 'gemstone',
                'stone_type' => 'الماس طبيعي مضمون',
                'stock_quantity' => 3,
                'merchant_id' => $merchant1->id,
                'category_id' => $categories['alaakod']->id,
                'material_id' => $mat18->id,
                'images' => [
                    'https://ik.imagekit.io/6vzmizcti/1776464595_AwZ0y_ZSpA2XA1K.jpg',
                    'https://ik.imagekit.io/6vzmizcti/1776464596_iN75e_uZHkp0VoB.jpg'
                ]
            ],
            [
                'title' => 'خاتم تركي',
                'slug' => 'khatm-trky',
                'description' => 'ذهب خليجي',
                'weight' => 9.9,
                'stone_weight' => 0.0,
                'type' => 'jewelry',
                'status' => 'published',
                'purity' => '21',
                'material_type' => 'gold',
                'stone_type' => null,
                'stock_quantity' => 2,
                'merchant_id' => $merchant2->id,
                'category_id' => $categories['alkhoatm']->id,
                'material_id' => $mat21->id,
                'images' => [
                    'https://ik.imagekit.io/6vzmizcti/1776864506_O9uOh_OPv6YbppoJ.jpg'
                ]
            ],
            [
                'title' => 'زمام ذهب خليجي',
                'slug' => 'zmam-thhb-khlygy',
                'description' => 'زمامات ذهب خليجي',
                'weight' => 0.1,
                'stone_weight' => 0.0,
                'type' => 'jewelry',
                'status' => 'published',
                'purity' => '21',
                'material_type' => 'gold',
                'stone_type' => null,
                'stock_quantity' => 10,
                'merchant_id' => $merchant1->id,
                'category_id' => $categories['alzmamat']->id,
                'material_id' => $mat21->id,
                'images' => [
                    'https://ik.imagekit.io/6vzmizcti/1776464250_opLJi_dXBQFO54Q_.jpg'
                ]
            ],
        ];

        foreach ($realProducts as $pData) {
            $imageUrls = $pData['images'];
            unset($pData['images']);

            $product = Product::create($pData);

            foreach ($imageUrls as $index => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $url,
                    'is_primary' => ($index === 0),
                    'display_order' => $index + 1,
                ]);
            }
        }
    }
}
