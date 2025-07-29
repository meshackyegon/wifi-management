<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateNgrokUrl extends Command
{
    protected $signature = 'ngrok:update-url {url : The NGrok URL (e.g., https://abc123.ngrok.io)}';
    protected $description = 'Update NGrok URL in .env file for M-Pesa callbacks';

    public function handle()
    {
        $url = $this->argument('url');
        
        // Remove trailing slash if present
        $url = rtrim($url, '/');
        
        // Validate URL format
        if (!filter_var($url, FILTER_VALIDATE_URL) || !str_contains($url, 'ngrok.io')) {
            $this->error('Invalid NGrok URL format. Expected: https://abc123.ngrok.io');
            return 1;
        }

        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return 1;
        }

        $envContent = File::get($envPath);
        
        // Update NGROK_URL
        $envContent = preg_replace(
            '/^NGROK_URL=.*/m',
            "NGROK_URL={$url}",
            $envContent
        );
        
        File::put($envPath, $envContent);
        
        $this->info("✅ NGrok URL updated to: {$url}");
        $this->info('');
        $this->info('🔗 Your M-Pesa callback URLs are now:');
        $this->info("STK Push Callback: {$url}/mpesa/stk-callback");
        $this->info("Validation URL: {$url}/mpesa/validation");
        $this->info("Confirmation URL: {$url}/mpesa/confirmation");
        $this->info('');
        $this->info('📋 Next steps:');
        $this->info('1. Copy these URLs to your M-Pesa app configuration');
        $this->info('2. Run: php artisan config:cache (to refresh config cache)');
        $this->info('3. Test your M-Pesa integration');

        return 0;
    }
}
