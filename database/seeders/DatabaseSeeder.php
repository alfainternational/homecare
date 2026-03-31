<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            CategorySeeder::class,
            ServiceCategorySeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            PaymentGatewaySeeder::class,
            PlatformSettingSeeder::class,
        ]);
    }
}
