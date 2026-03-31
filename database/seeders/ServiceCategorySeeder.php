<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar'         => 'سباكة',
                'name_en'         => 'Plumbing',
                'icon'            => '🔧',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 1,
            ],
            [
                'name_ar'         => 'كهرباء',
                'name_en'         => 'Electrical',
                'icon'            => '⚡',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 2,
            ],
            [
                'name_ar'         => 'تكييف وتبريد',
                'name_en'         => 'HVAC',
                'icon'            => '❄️',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 3,
            ],
            [
                'name_ar'         => 'نجارة',
                'name_en'         => 'Carpentry',
                'icon'            => '🪵',
                'is_subscription' => false,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 4,
            ],
            [
                'name_ar'         => 'دهانات',
                'name_en'         => 'Painting',
                'icon'            => '🎨',
                'is_subscription' => false,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 5,
            ],
            [
                'name_ar'         => 'تنظيف المنزل',
                'name_en'         => 'Home Cleaning',
                'icon'            => '🧹',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => false,
                'is_active'       => true,
                'sort_order'      => 6,
            ],
            [
                'name_ar'         => 'تنظيف الكنب',
                'name_en'         => 'Sofa Cleaning',
                'icon'            => '🛋️',
                'is_subscription' => false,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 7,
            ],
            [
                'name_ar'         => 'حدائق ومناظر طبيعية',
                'name_en'         => 'Gardening',
                'icon'            => '🌿',
                'is_subscription' => false,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 8,
            ],
            [
                'name_ar'         => 'مكافحة حشرات',
                'name_en'         => 'Pest Control',
                'icon'            => '🐛',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 9,
            ],
            [
                'name_ar'         => 'صيانة عامة',
                'name_en'         => 'General Maintenance',
                'icon'            => '🏠',
                'is_subscription' => true,
                'is_on_demand'    => true,
                'is_marketplace'  => true,
                'is_active'       => true,
                'sort_order'      => 10,
            ],
        ];

        foreach ($categories as $data) {
            ServiceCategory::firstOrCreate(['name_en' => $data['name_en']], $data);
        }
    }
}
