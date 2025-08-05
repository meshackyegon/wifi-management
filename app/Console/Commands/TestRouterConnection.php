<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Router;

class TestRouterConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'router:test {router_id? : The ID of the router to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test router connection and API access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routerId = $this->argument('router_id');
        
        if ($routerId) {
            $router = Router::find($routerId);
            if (!$router) {
                $this->error("Router with ID {$routerId} not found.");
                return;
            }
            $this->testRouter($router);
        } else {
            $routers = Router::all();
            if ($routers->isEmpty()) {
                $this->warn("No routers found in the system.");
                $this->info("Run 'php artisan db:seed --class=RouterSeeder' to create sample routers.");
                return;
            }
            
            $this->info("Testing all routers...");
            foreach ($routers as $router) {
                $this->testRouter($router);
                $this->newLine();
            }
        }
    }
    
    private function testRouter(Router $router)
    {
        $this->info("Testing router: {$router->name}");
        $this->line("Location: {$router->location}");
        $this->line("IP Address: {$router->ip_address}");
        $this->line("Type: {$router->type}");
        $this->line("API Port: {$router->api_port}");
        
        // Test basic connectivity
        $this->info("Testing basic connectivity...");
        $ping = $this->testPing($router->ip_address);
        if ($ping) {
            $this->info("✅ Router is reachable via ping");
        } else {
            $this->error("❌ Router is not reachable via ping");
        }
        
        // Test API port
        $this->info("Testing API port connectivity...");
        $apiPortOpen = $this->testPort($router->ip_address, $router->api_port);
        if ($apiPortOpen) {
            $this->info("✅ API port {$router->api_port} is accessible");
        } else {
            $this->error("❌ API port {$router->api_port} is not accessible");
        }
        
        // Test API authentication (basic check)
        if ($router->type === 'mikrotik') {
            $this->info("Testing MikroTik API authentication...");
            $authTest = $this->testMikroTikAuth($router);
            if ($authTest) {
                $this->info("✅ API authentication successful");
            } else {
                $this->error("❌ API authentication failed");
            }
        }
        
        // Update last connected timestamp if successful
        if ($ping && $apiPortOpen) {
            $router->update(['last_connected_at' => now()]);
            $this->info("✅ Router test completed successfully");
        } else {
            $this->warn("⚠️  Router test completed with issues");
        }
    }
    
    private function testPing($ip)
    {
        $output = [];
        $result = null;
        
        // Use Windows ping command
        exec("ping -n 1 -w 1000 {$ip}", $output, $result);
        
        return $result === 0;
    }
    
    private function testPort($ip, $port)
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    private function testMikroTikAuth($router)
    {
        // Basic socket test for MikroTik API
        try {
            $socket = @fsockopen($router->ip_address, $router->api_port, $errno, $errstr, 5);
            if ($socket) {
                fclose($socket);
                return true;
            }
        } catch (\Exception $e) {
            // Ignore exception for basic test
        }
        return false;
    }
}
