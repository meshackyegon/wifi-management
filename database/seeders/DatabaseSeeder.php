<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            VoucherPlanSeeder::class,
            RouterSeeder::class,
        ]);

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📋 Login Credentials:');
        $this->command->info('👤 Admin: admin@wifimanagement.com / Admin@123');
        $this->command->info('🔧 Agent: agent@wifimanagement.com / Agent@123');
        $this->command->info('👥 Customer: customer@example.com / Customer@123');
        $this->command->info('');
        $this->command->info('🔥 Your WiFi Management System is ready to go!');
    }
}
