<?php

namespace App\Console\Commands;

use App\Services\MpesaService;
use Illuminate\Console\Command;

class TestMpesaAuth extends Command
{
    protected $signature = 'mpesa:test-auth';
    protected $description = 'Test M-Pesa authentication and configuration';

    public function handle()
    {
        $this->info('🔍 Testing M-Pesa Configuration...');
        $this->newLine();

        // Check configuration values
        $consumerKey = config('services.safaricom.consumer_key');
        $consumerSecret = config('services.safaricom.consumer_secret');
        $shortcode = config('services.safaricom.shortcode');
        $passkey = config('services.safaricom.passkey');
        $endpoint = config('services.safaricom.endpoint');

        $this->info('📋 Configuration Check:');
        $this->line("   Consumer Key: " . ($consumerKey ? 'Set (' . substr($consumerKey, 0, 10) . '...)' : 'NOT SET'));
        $this->line("   Consumer Secret: " . ($consumerSecret ? 'Set (' . substr($consumerSecret, 0, 10) . '...)' : 'NOT SET'));
        $this->line("   Shortcode: " . ($shortcode ?: 'NOT SET'));
        $this->line("   Passkey: " . ($passkey ? 'Set (' . substr($passkey, 0, 10) . '...)' : 'NOT SET'));
        $this->line("   Endpoint: " . ($endpoint ?: 'NOT SET'));
        $this->newLine();

        if (!$consumerKey || !$consumerSecret || !$shortcode || !$passkey || !$endpoint) {
            $this->error('❌ M-Pesa configuration is incomplete!');
            $this->line('Please check your .env file and ensure all M-Pesa credentials are set.');
            return 1;
        }

        // Test authentication
        $this->info('🔐 Testing M-Pesa Authentication...');
        
        try {
            $mpesaService = new MpesaService();
            $accessToken = $mpesaService->getAccessToken();

            if ($accessToken) {
                $this->info('✅ M-Pesa authentication successful!');
                $this->line("   Access Token: " . substr($accessToken, 0, 20) . '...');
            } else {
                $this->error('❌ M-Pesa authentication failed!');
                $this->line('Check the Laravel logs for more details.');
            }
        } catch (\Exception $e) {
            $this->error('❌ M-Pesa authentication error: ' . $e->getMessage());
        }

        return 0;
    }
}
