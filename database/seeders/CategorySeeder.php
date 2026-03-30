<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::truncate();

        $categories = [
            ['name' => 'Plumbing',   'name_ar' => 'السباكة',  'icon' => '🔧', 'slug' => 'plumbing'],
            ['name' => 'Electrical', 'name_ar' => 'الكهرباء', 'icon' => '⚡', 'slug' => 'electrical'],
            ['name' => 'HVAC',       'name_ar' => 'التكييف',  'icon' => '❄️', 'slug' => 'hvac'],
            ['name' => 'Tools',      'name_ar' => 'أدوات',    'icon' => '🔨', 'slug' => 'tools'],
        ];

        foreach ($categories as $cat) {
            Category::create([...$cat, 'is_active' => true]);
        }
    }
}
