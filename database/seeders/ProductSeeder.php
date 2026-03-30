<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::truncate();

        $plumbing   = Category::where('slug', 'plumbing')->first()?->id   ?? 1;
        $electrical = Category::where('slug', 'electrical')->first()?->id ?? 2;
        $hvac       = Category::where('slug', 'hvac')->first()?->id       ?? 3;

        $products = [
            ['category_id' => $plumbing,   'name' => 'Faucet Repair Kit',        'name_ar' => 'طقم إصلاح الحنفية',                    'price' => 45.00,  'stock' => 50, 'brand' => 'Grohe',     'sku' => 'PLM-001', 'is_featured' => false],
            ['category_id' => $plumbing,   'name' => 'Water Valve 1/2',           'name_ar' => 'محبس مياه سيراميك 1/2 بوصة',           'price' => 145.00, 'stock' => 30, 'brand' => 'Grohe',     'sku' => 'PLM-002', 'is_featured' => true],
            ['category_id' => $plumbing,   'name' => 'PVC Pipe Fitting Set',      'name_ar' => 'طقم وصلات مواسير PVC',                  'price' => 89.00,  'stock' => 20, 'brand' => 'Schneider', 'sku' => 'PLM-003', 'is_featured' => false],
            ['category_id' => $electrical, 'name' => 'Circuit Breaker 63A',       'name_ar' => 'قاطع كهربائي ثلاثي 63 أمبير',          'price' => 89.00,  'stock' => 2,  'brand' => 'Schneider', 'sku' => 'ELC-001', 'is_featured' => true],
            ['category_id' => $electrical, 'name' => 'Double Socket Outlet',      'name_ar' => 'وصلة كهربائية مزدوجة مع أرضي',         'price' => 35.00,  'stock' => 100,'brand' => 'Legrand',   'sku' => 'ELC-002', 'is_featured' => false],
            ['category_id' => $hvac,       'name' => 'AC Filter High Density',    'name_ar' => 'فلتر هواء مكيف سبليت عالي الكثافة',    'price' => 65.00,  'stock' => 45, 'brand' => 'LG',        'sku' => 'HVC-001', 'is_featured' => false],
            ['category_id' => $hvac,       'name' => 'AC Refrigerant Gas R410A',  'name_ar' => 'غاز تبريد مكيف R410A',                  'price' => 220.00, 'stock' => 0,  'brand' => 'Honeywell', 'sku' => 'HVC-002', 'is_featured' => false],
        ];

        foreach ($products as $p) {
            Product::create([...$p, 'is_active' => true]);
        }
    }
}
