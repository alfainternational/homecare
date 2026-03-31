<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'code'       => 'wallet',
                'name_ar'    => 'المحفظة الإلكترونية',
                'name_en'    => 'Wallet',
                'icon'       => '👛',
                'is_enabled' => true,
                'mode'       => 'live',
                'sort_order' => 1,
            ],
            [
                'code'       => 'mada',
                'name_ar'    => 'مدى',
                'name_en'    => 'Mada',
                'icon'       => '💳',
                'is_enabled' => false,
                'mode'       => 'test',
                'sort_order' => 2,
                'min_amount' => 1,
                'max_amount' => 30000,
            ],
            [
                'code'       => 'apple_pay',
                'name_ar'    => 'Apple Pay',
                'name_en'    => 'Apple Pay',
                'icon'       => '🍎',
                'is_enabled' => false,
                'mode'       => 'test',
                'sort_order' => 3,
            ],
            [
                'code'       => 'tabby',
                'name_ar'    => 'تابي — 4 أقساط بدون فوائد',
                'name_en'    => 'Tabby — 4 installments',
                'icon'       => '🛍️',
                'is_enabled' => false,
                'mode'       => 'test',
                'sort_order' => 4,
                'min_amount' => 200,
                'max_amount' => 5000,
            ],
            [
                'code'       => 'tamara',
                'name_ar'    => 'تمارا — ادفع لاحقاً',
                'name_en'    => 'Tamara — Pay Later',
                'icon'       => '⏳',
                'is_enabled' => false,
                'mode'       => 'test',
                'sort_order' => 5,
                'min_amount' => 100,
                'max_amount' => 5000,
            ],
            [
                'code'       => 'stc_pay',
                'name_ar'    => 'STC Pay',
                'name_en'    => 'STC Pay',
                'icon'       => '📱',
                'is_enabled' => false,
                'mode'       => 'test',
                'sort_order' => 6,
            ],
            [
                'code'       => 'bank',
                'name_ar'    => 'تحويل بنكي',
                'name_en'    => 'Bank Transfer',
                'icon'       => '🏦',
                'is_enabled' => false,
                'mode'       => 'live',
                'sort_order' => 7,
            ],
        ];

        foreach ($gateways as $data) {
            PaymentGateway::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
