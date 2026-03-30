<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::truncate();

        $plans = [
            [
                'name'           => 'Basic',
                'name_ar'        => 'الباقة الأساسية',
                'type'           => 'residential',
                'price'          => 199.00,
                'visits_per_year'=> 24,
                'features'       => json_encode(['زيارتان شهرياً', 'صيانة كهرباء وسباكة', 'ضمان 30 يوم على العمل']),
                'is_featured'    => false,
                'is_active'      => true,
            ],
            [
                'name'           => 'Advanced',
                'name_ar'        => 'الباقة المتقدمة',
                'type'           => 'residential',
                'price'          => 349.00,
                'visits_per_year'=> 48,
                'features'       => json_encode(['4 زيارات شهرياً', 'صيانة المكيفات تنظيف + فحص', 'أولوية في الطلبات الطارئة', 'خصم 20% على قطع الغيار', 'دعم 24/7']),
                'is_featured'    => true,
                'is_active'      => true,
            ],
            [
                'name'           => 'Premium',
                'name_ar'        => 'الباقة الشاملة',
                'type'           => 'residential',
                'price'          => 599.00,
                'visits_per_year'=> 0,
                'features'       => json_encode(['زيارات غير محدودة', 'كافة خدمات الصيانة والترميم', 'مدير حساب خاص لمنزلك', 'أولوية قصوى 24/7', 'تقرير صحة منزلي ربع سنوي']),
                'is_featured'    => false,
                'is_active'      => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
