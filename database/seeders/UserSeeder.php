<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TechnicianProfile;
use App\Models\Wallet;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@warmconcierge.com'],
            ['name' => 'مدير النظام', 'password' => Hash::make('password'), 'role' => 'admin', 'phone' => '0501234567', 'email_verified_at' => now()]
        );
        Wallet::firstOrCreate(['user_id' => $admin->id], ['balance' => 0]);

        // Technician
        $tech = User::updateOrCreate(
            ['email' => 'tech@warmconcierge.com'],
            ['name' => 'م. خالد القحطاني', 'password' => Hash::make('password'), 'role' => 'technician', 'phone' => '0507654321', 'email_verified_at' => now()]
        );
        TechnicianProfile::firstOrCreate(
            ['user_id' => $tech->id],
            ['specializations' => json_encode(['plumbing', 'electrical']), 'rating_average' => 4.9, 'total_ratings' => 38, 'status' => 'available', 'experience_years' => 8, 'bio' => 'فني صيانة معتمد بخبرة 8 سنوات في السباكة والكهرباء']
        );
        Wallet::firstOrCreate(['user_id' => $tech->id], ['balance' => 0]);

        // Client
        $client = User::updateOrCreate(
            ['email' => 'client@warmconcierge.com'],
            ['name' => 'أحمد العلي', 'password' => Hash::make('password'), 'role' => 'client', 'phone' => '0512345678', 'email_verified_at' => now()]
        );
        Wallet::firstOrCreate(['user_id' => $client->id], ['balance' => 450.00]);

        // Create subscription for client
        $plan = Plan::where('name', 'Advanced')->first();
        if ($plan && !$client->subscriptions()->exists()) {
            Subscription::create([
                'user_id'       => $client->id,
                'plan_id'       => $plan->id,
                'status'        => 'active',
                'starts_at'     => now(),
                'ends_at'       => now()->addYear(),
                'visits_used'   => 4,
                'visits_total'  => 48,
                'auto_renew'    => true,
            ]);
        }
    }
}
