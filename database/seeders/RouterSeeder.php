<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Router;

class RouterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routers = [
            [
                'name' => 'Main Reception Router',
                'location' => 'Reception Area - Ground Floor',
                'ip_address' => '192.168.1.1',
                'api_username' => 'admin',
                'api_password' => 'mikrotik123',
                'api_port' => 8728,
                'type' => 'mikrotik',
                'is_active' => true,
                'max_concurrent_users' => 50,
                'description' => 'Main router for reception and lobby area',
                'redirect_url' => 'http://192.168.1.1/login',
            ],
            [
                'name' => 'Conference Room Router',
                'location' => 'Conference Room - 2nd Floor',
                'ip_address' => '192.168.2.1',
                'api_username' => 'admin',
                'api_password' => 'mikrotik456',
                'api_port' => 8728,
                'type' => 'mikrotik',
                'is_active' => true,
                'max_concurrent_users' => 25,
                'description' => 'Dedicated router for conference and meeting rooms',
                'redirect_url' => 'http://192.168.2.1/login',
            ],
            [
                'name' => 'Cafeteria Router',
                'location' => 'Cafeteria - Ground Floor',
                'ip_address' => '192.168.3.1',
                'api_username' => 'admin',
                'api_password' => 'mikrotik789',
                'api_port' => 8728,
                'type' => 'mikrotik',
                'is_active' => true,
                'max_concurrent_users' => 100,
                'description' => 'High-capacity router for cafeteria and dining area',
                'redirect_url' => 'http://192.168.3.1/login',
            ],
            [
                'name' => 'Guest Room Router',
                'location' => 'Guest Rooms - 3rd Floor',
                'ip_address' => '192.168.4.1',
                'api_username' => 'admin',
                'api_password' => 'mikrotik321',
                'api_port' => 8728,
                'type' => 'mikrotik',
                'is_active' => true,
                'max_concurrent_users' => 30,
                'description' => 'Router dedicated to guest room access',
                'redirect_url' => 'http://192.168.4.1/login',
            ],
            [
                'name' => 'Outdoor Area Router',
                'location' => 'Garden & Outdoor Seating',
                'ip_address' => '192.168.5.1',
                'api_username' => 'admin',
                'api_password' => 'mikrotik654',
                'api_port' => 8728,
                'type' => 'mikrotik',
                'is_active' => true,
                'max_concurrent_users' => 40,
                'description' => 'Weather-resistant router for outdoor coverage',
                'redirect_url' => 'http://192.168.5.1/login',
            ],
        ];

        foreach ($routers as $router) {
            Router::firstOrCreate(
                ['ip_address' => $router['ip_address']],
                $router
            );
        }

        $this->command->info('Routers created successfully!');
        $this->command->info('Created ' . count($routers) . ' routers');
    }
}
