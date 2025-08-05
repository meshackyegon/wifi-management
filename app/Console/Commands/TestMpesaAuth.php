<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TestMpesaAuth extends Command
{
    protected $signature = 'test:mpesa-auth';
    protected $description = 'Test M-Pesa authentication and configuration';

    public function handle()
    {
        $this->info("Testing M-Pesa Configuration...");
        
        // Check environment variables
        $consumerKey = config('services.safaricom.consumer_key');
        $consumerSecret = config('services.safaricom.consumer_secret');
        $shortcode = config('services.safaricom.shortcode');
        $passkey = config('services.safaricom.passkey');
        $endpoint = config('services.safaricom.endpoint');
        
        $this->info("Consumer Key: " . ($consumerKey ? 'Set ✅' : 'Missing ❌'));
        $this->info("Consumer Secret: " . ($consumerSecret ? 'Set ✅' : 'Missing ❌'));
        $this->info("Shortcode: " . ($shortcode ?: 'Missing ❌'));
        $this->info("Passkey: " . ($passkey ? 'Set ✅' : 'Missing ❌'));
        $this->info("Endpoint: " . ($endpoint ?: 'Missing ❌'));
        
        if (!$consumerKey || !$consumerSecret) {
            $this->error("❌ M-Pesa credentials not configured properly!");
            return;
        }
        
        $this->info("\nTesting M-Pesa Access Token...");
        
        try {
            $client = new Client();
            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
            
            $response = $client->get($endpoint . '/oauth/v1/generate?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'timeout' => 30,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['access_token'])) {
                $this->info("✅ Access token obtained successfully!");
                $this->info("Token: " . substr($data['access_token'], 0, 20) . "...");
                $this->info("Expires in: " . ($data['expires_in'] ?? 'Unknown') . " seconds");
                
                // Test STK Push endpoint availability
                $this->testSTKPushEndpoint($data['access_token'], $endpoint);
                
            } else {
                $this->error("❌ Failed to get access token!");
                $this->error("Response: " . json_encode($data));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Authentication failed: " . $e->getMessage());
            Log::error('M-Pesa Auth Test Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    
    private function testSTKPushEndpoint($accessToken, $endpoint)
    {
        $this->info("\nTesting STK Push endpoint availability...");
        
        try {
            $client = new Client();
            
            // Test with minimal payload to check endpoint
            $testPayload = [
                'BusinessShortCode' => config('services.safaricom.shortcode'),
                'Password' => base64_encode(config('services.safaricom.shortcode') . config('services.safaricom.passkey') . date('YmdHis')),
                'Timestamp' => date('YmdHis'),
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => 1,
                'PartyA' => '254708374149', // Test number
                'PartyB' => config('services.safaricom.shortcode'),
                'PhoneNumber' => '254708374149',
                'CallBackURL' => 'https://example.com/callback',
                'AccountReference' => 'TEST123',
                'TransactionDesc' => 'Test transaction',
            ];
            
            $response = $client->post($endpoint . '/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $testPayload,
                'verify' => false,
                'timeout' => 30,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            $this->info("✅ STK Push endpoint is reachable!");
            $this->info("Response Code: " . ($data['ResponseCode'] ?? 'Unknown'));
            $this->info("Response Description: " . ($data['ResponseDescription'] ?? 'Unknown'));
            
            if (($data['ResponseCode'] ?? null) === '0') {
                $this->info("🎉 STK Push test successful! Check test phone for payment request.");
            } else {
                $this->warn("⚠️  STK Push may have issues. Response: " . json_encode($data));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ STK Push test failed: " . $e->getMessage());
            Log::error('STK Push Test Error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
