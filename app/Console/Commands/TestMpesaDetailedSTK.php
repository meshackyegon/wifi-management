<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MobileMoneyService;
use App\Models\VoucherPlan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TestMpesaDetailedSTK extends Command
{
    protected $signature = 'test:mpesa-detailed-stk {phone} {--amount=1} {--plan=}';
    protected $description = 'Test M-Pesa STK Push with detailed logging';

    public function handle()
    {
        $phone = $this->argument('phone');
        $amount = (float) $this->option('amount');
        $planId = $this->option('plan');

        $this->info('Testing M-Pesa STK Push with detailed logging...');
        $this->info("Phone: {$phone}");
        $this->info("Amount: KES {$amount}");

        // Get or create voucher plan
        if ($planId) {
            $plan = VoucherPlan::find($planId);
        } else {
            $plan = VoucherPlan::where('price', '<=', $amount)->first();
        }

        if (!$plan) {
            $this->error('No suitable voucher plan found');
            return;
        }

        $this->info("Using plan: {$plan->name} (KES {$plan->price})");

        try {
            // Get access token first
            $this->info('Step 1: Getting M-Pesa access token...');
            $tokenResponse = $this->getAccessToken();
            
            if (!$tokenResponse) {
                $this->error('Failed to get access token');
                return;
            }
            
            $this->info('✅ Access token obtained successfully');

            // Prepare STK Push payload
            $this->info('Step 2: Preparing STK Push payload...');
            $payload = $this->buildSTKPayload($phone, $amount, $plan);
            $this->info('Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));

            // Send STK Push
            $this->info('Step 3: Sending STK Push request...');
            $response = $this->sendSTKPush($tokenResponse['access_token'], $payload);
            
            if ($response) {
                $this->info('✅ STK Push sent successfully!');
                $this->info('Response: ' . json_encode($response, JSON_PRETTY_PRINT));
                
                if (isset($response['CheckoutRequestID'])) {
                    $this->info("CheckoutRequestID: {$response['CheckoutRequestID']}");
                    $this->info('Check your phone for the M-Pesa payment request.');
                    
                    // Wait for user to complete or cancel
                    $this->info('Waiting 60 seconds for payment completion...');
                    sleep(60);
                    
                    // Check status
                    $this->info('Step 4: Checking payment status...');
                    $statusResponse = $this->checkSTKStatus($tokenResponse['access_token'], $response['CheckoutRequestID'], $payload);
                    
                    if ($statusResponse) {
                        $this->info('Status Response: ' . json_encode($statusResponse, JSON_PRETTY_PRINT));
                    }
                }
            } else {
                $this->error('Failed to send STK Push');
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('M-Pesa STK Test Error', [
                'phone' => $phone,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function getAccessToken()
    {
        $credentials = base64_encode(
            config('services.safaricom.consumer_key') . ':' . 
            config('services.safaricom.consumer_secret')
        );

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json',
        ])
        ->withOptions(['verify' => false])
        ->get(config('services.safaricom.endpoint') . '/oauth/v1/generate?grant_type=client_credentials');

        if ($response->successful()) {
            return $response->json();
        }

        $this->error('Token Error: ' . $response->body());
        return null;
    }

    private function buildSTKPayload($phone, $amount, $plan)
    {
        $shortcode = config('services.safaricom.shortcode');
        $passkey = config('services.safaricom.passkey');
        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        // Format phone number for M-Pesa (remove + and ensure proper format)
        $formattedPhone = $this->formatKenyanPhoneNumber($phone);

        // Use NGrok URL if available, otherwise use a valid HTTPS dummy URL for testing
        $ngrokUrl = env('NGROK_URL', 'https://demo.ngrok.io');
        $callbackUrl = $ngrokUrl . '/mobile-money/callback/safaricom_mpesa';

        return [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $formattedPhone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => 'WiFi' . time(),
            'TransactionDesc' => "WiFi Voucher - {$plan->name}",
        ];
    }

    private function sendSTKPush($accessToken, $payload)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])
        ->withOptions(['verify' => false])
        ->post(config('services.safaricom.endpoint') . '/mpesa/stkpush/v1/processrequest', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        $this->error('STK Push Error: ' . $response->body());
        return null;
    }

    private function checkSTKStatus($accessToken, $checkoutRequestId, $originalPayload)
    {
        $payload = [
            'BusinessShortCode' => $originalPayload['BusinessShortCode'],
            'Password' => $originalPayload['Password'],
            'Timestamp' => $originalPayload['Timestamp'],
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])
        ->withOptions(['verify' => false])
        ->post(config('services.safaricom.endpoint') . '/mpesa/stkpushquery/v1/query', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        $this->error('Status Check Error: ' . $response->body());
        return null;
    }

    /**
     * Format Kenyan phone number for M-Pesa API
     * Converts +254722617737 or 0722617737 to 254722617737
     */
    private function formatKenyanPhoneNumber(string $phoneNumber): string
    {
        // Remove any spaces, dashes, or other characters
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove leading + if present
        if (str_starts_with($phoneNumber, '+')) {
            $phoneNumber = substr($phoneNumber, 1);
        }
        
        // Convert local format (0722617737) to international (254722617737)
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }
        
        // Ensure it starts with 254 for Kenya
        if (!str_starts_with($phoneNumber, '254')) {
            // If it's a 9-digit number (like 722617737), add 254
            if (strlen($phoneNumber) === 9) {
                $phoneNumber = '254' . $phoneNumber;
            }
        }
        
        return $phoneNumber;
    }
}
