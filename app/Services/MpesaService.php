<?php

namespace App\Services;

use App\Models\MobileMoneyPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $endpoint;
    protected $callbackUrl;

    public function __construct()
    {
        $this->consumerKey = config('services.safaricom.consumer_key');
        $this->consumerSecret = config('services.safaricom.consumer_secret');
        $this->shortcode = config('services.safaricom.shortcode');
        $this->passkey = config('services.safaricom.passkey');
        $this->endpoint = config('services.safaricom.endpoint');
        $this->callbackUrl = config('services.safaricom.callback_url') ?: config('app.url') . '/mpesa/stk-callback';
    }

    /**
     * Get M-Pesa access token
     */
    public function getAccessToken()
    {
        // Check if we have a cached token
        $cacheKey = 'mpesa_access_token';
        $cachedToken = Cache::get($cacheKey);
        
        if ($cachedToken) {
            return $cachedToken;
        }

        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        
        try {
            // Configure HTTP client for sandbox environment
            $http = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ]);
            
            // For sandbox environments, disable SSL verification
            if (str_contains($this->endpoint, 'sandbox')) {
                $http = $http->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ]);
            }
            
            $response = $http->get($this->endpoint . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                
                // Cache the token for slightly less than its expiry time
                Cache::put($cacheKey, $accessToken, $expiresIn - 60);
                
                Log::info('M-Pesa access token generated successfully');
                return $accessToken;
            } else {
                Log::error('Failed to get M-Pesa access token', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate STK Push
     */
    public function stkPush(MobileMoneyPayment $payment)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            throw new \Exception('Failed to get M-Pesa access token');
        }

        // Format phone number (remove + and ensure it starts with 254)
        $phoneNumber = $this->formatPhoneNumber($payment->phone_number);
        
        // Generate timestamp
        $timestamp = now()->format('YmdHis');
        
        // Generate password
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $payment->amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => 'WiFi-' . $payment->id,
            'TransactionDesc' => 'WiFi Voucher Payment - ' . $payment->voucherPlan->name
        ];

        Log::info('Initiating M-Pesa STK Push', [
            'payment_id' => $payment->id,
            'phone_number' => $phoneNumber,
            'amount' => $payment->amount,
            'payload' => $payload
        ]);

        try {
            // Configure HTTP client for STK Push
            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ]);
            
            // For sandbox environments, disable SSL verification
            if (str_contains($this->endpoint, 'sandbox')) {
                $http = $http->withOptions([
                    'verify' => false,
                    'timeout' => 30,
                ]);
            }
            
            $response = $http->post($this->endpoint . '/mpesa/stkpush/v1/processrequest', $payload);

            $responseData = $response->json();

            Log::info('M-Pesa STK Push response', [
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'response' => $responseData
            ]);

            if ($response->successful() && isset($responseData['CheckoutRequestID'])) {
                // Update payment with M-Pesa checkout request ID
                $payment->update([
                    'external_transaction_id' => $responseData['CheckoutRequestID'],
                    'reference_number' => $responseData['MerchantRequestID'] ?? null,
                ]);

                return [
                    'success' => true,
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'merchant_request_id' => $responseData['MerchantRequestID'] ?? null,
                    'response_code' => $responseData['ResponseCode'] ?? null,
                    'response_description' => $responseData['ResponseDescription'] ?? 'STK Push sent successfully',
                    'customer_message' => $responseData['CustomerMessage'] ?? 'Please check your phone for the M-Pesa prompt'
                ];
            } else {
                $errorMessage = $responseData['errorMessage'] ?? $responseData['ResponseDescription'] ?? 'STK Push failed';
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'response_code' => $responseData['ResponseCode'] ?? null
                ];
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to initiate M-Pesa payment: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for M-Pesa
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters except +
        $phoneNumber = preg_replace('/[^+\d]/', '', $phoneNumber);
        
        // Remove + if present
        $phoneNumber = ltrim($phoneNumber, '+');
        
        // If it starts with 0, replace with 254
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }
        
        // If it doesn't start with 254, assume it's missing and add it
        if (substr($phoneNumber, 0, 3) !== '254') {
            $phoneNumber = '254' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Query STK Push status
     */
    public function queryStatus($checkoutRequestId)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            throw new \Exception('Failed to get M-Pesa access token');
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '/mpesa/stkpushquery/v1/query', $payload);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('M-Pesa status query error: ' . $e->getMessage());
            return null;
        }
    }
}
