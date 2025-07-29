<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // Create permissions
        $permissions = [
            'manage-users',
            'manage-vouchers',
            'manage-plans',
            'manage-routers',
            'manage-settings',
            'view-reports',
            'manage-sms',
            'manage-payments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions);
        $agentRole->syncPermissions([
            'manage-vouchers',
            'view-reports',
            'manage-sms',
        ]);
        $customerRole->syncPermissions([]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@wifimanagement.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@wifimanagement.com',
                'password' => Hash::make('Admin@123'),
                'phone' => '+256701234567',
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create Agent User
        $agent = User::firstOrCreate(
            ['email' => 'agent@wifimanagement.com'],
            [
                'name' => 'WiFi Agent',
                'email' => 'agent@wifimanagement.com',
                'password' => Hash::make('Agent@123'),
                'phone' => '+256702345678',
                'user_type' => 'agent',
                'email_verified_at' => now(),
            ]
        );
        $agent->assignRole('agent');

        // Create a sample customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Sample Customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('Customer@123'),
                'phone' => '+256703456789',
                'user_type' => 'customer',
                'email_verified_at' => now(),
            ]
        );
        $customer->assignRole('customer');

        $this->command->info('Users created successfully:');
        $this->command->info('Admin: admin@wifimanagement.com / Admin@123');
        $this->command->info('Agent: agent@wifimanagement.com / Agent@123');
        $this->command->info('Customer: customer@example.com / Customer@123');
    }
}
