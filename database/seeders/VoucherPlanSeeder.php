<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VoucherPlan;

class VoucherPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic 1 Hour',
                'description' => 'Perfect for quick browsing and social media',
                'duration_hours' => 1,
                'data_limit_mb' => 500,
                'price' => 1.00,
                'bandwidth_limit_kbps' => 5120, // 5 Mbps
                'is_active' => true,
            ],
            [
                'name' => 'Standard 3 Hours',
                'description' => 'Great for work and entertainment',
                'duration_hours' => 3,
                'data_limit_mb' => 1500,
                'price' => 2.50,
                'bandwidth_limit_kbps' => 10240, // 10 Mbps
                'is_active' => true,
            ],
            [
                'name' => 'Premium 24 Hours',
                'description' => 'Full day unlimited access',
                'duration_hours' => 24,
                'data_limit_mb' => null, // Unlimited
                'price' => 5.00,
                'bandwidth_limit_kbps' => null, // Unlimited
                'is_active' => true,
            ],
            [
                'name' => 'Weekly Plan',
                'description' => 'Perfect for extended stays',
                'duration_hours' => 168, // 7 days
                'data_limit_mb' => null, // Unlimited
                'price' => 20.00,
                'bandwidth_limit_kbps' => null, // Unlimited
                'is_active' => true,
            ],
            [
                'name' => 'Student Special',
                'description' => 'Affordable 6-hour access for students',
                'duration_hours' => 6,
                'data_limit_mb' => 2000,
                'price' => 3.00,
                'bandwidth_limit_kbps' => 8192, // 8 Mbps
                'is_active' => true,
            ],
            [
                'name' => 'Business Plan',
                'description' => 'High-speed access for business users',
                'duration_hours' => 8,
                'data_limit_mb' => null, // Unlimited
                'price' => 8.00,
                'bandwidth_limit_kbps' => null, // Unlimited
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            VoucherPlan::firstOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        $this->command->info('Voucher plans created successfully!');
        $this->command->info('Created ' . count($plans) . ' voucher plans');
    }
}
