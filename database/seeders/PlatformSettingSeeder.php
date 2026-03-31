<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Marketplace
            ['key' => 'marketplace_enabled',   'value' => '1',    'type' => 'boolean', 'group' => 'marketplace', 'label_ar' => 'تفعيل السوق الحر'],
            ['key' => 'technician_model',       'value' => 'both', 'type' => 'string',  'group' => 'marketplace', 'label_ar' => 'نموذج الفنيين'],
            ['key' => 'commission_rate',        'value' => '10',   'type' => 'integer', 'group' => 'marketplace', 'label_ar' => 'نسبة العمولة (%)'],
            ['key' => 'monthly_sub_price',      'value' => '99',   'type' => 'integer', 'group' => 'marketplace', 'label_ar' => 'سعر الاشتراك الشهري'],
            ['key' => 'annual_sub_price',       'value' => '899',  'type' => 'integer', 'group' => 'marketplace', 'label_ar' => 'سعر الاشتراك السنوي'],
            ['key' => 'post_expiry_days',       'value' => '14',   'type' => 'integer', 'group' => 'marketplace', 'label_ar' => 'مدة صلاحية المنشور (أيام)'],
            ['key' => 'max_bids_per_post',      'value' => '10',   'type' => 'integer', 'group' => 'marketplace', 'label_ar' => 'الحد الأقصى للعروض على المنشور'],
            ['key' => 'require_post_review',    'value' => '0',    'type' => 'boolean', 'group' => 'marketplace', 'label_ar' => 'مراجعة المنشورات قبل النشر'],

            // General
            ['key' => 'site_name',              'value' => 'هوم كير', 'type' => 'string',  'group' => 'general', 'label_ar' => 'اسم الموقع'],
            ['key' => 'support_phone',          'value' => '+966500000000', 'type' => 'string', 'group' => 'general', 'label_ar' => 'رقم الدعم'],
            ['key' => 'vat_rate',               'value' => '15',   'type' => 'integer', 'group' => 'general', 'label_ar' => 'نسبة الضريبة (%)'],

            // Referral
            ['key' => 'referral_reward',        'value' => '25',   'type' => 'integer', 'group' => 'referral', 'label_ar' => 'مكافأة الإحالة (ر.س)'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
