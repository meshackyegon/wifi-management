<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\Voucher;
use AfricasTalking\SDK\AfricasTalking;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $africasTalking;
    protected $twilio;
    protected $defaultProvider;

    public function __construct()
    {
        $this->defaultProvider = config('sms.default_provider', 'africastalking');
        $this->initializeProviders();
    }

    /**
     * Initialize SMS providers
     */
    protected function initializeProviders()
    {
        // Initialize Africa's Talking
        if (config('services.africastalking.api_key')) {
            $this->africasTalking = new AfricasTalking(
                config('services.africastalking.username'),
                config('services.africastalking.api_key')
            );
        }

        // Initialize Twilio
        if (config('services.twilio.sid')) {
            $this->twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }
    }

    /**
     * Send voucher SMS
     */
    public function sendVoucherSMS(string $phoneNumber, Voucher $voucher, string $provider = null)
    {
        $provider = $provider ?: $this->defaultProvider;
        
        $message = $this->buildVoucherMessage($voucher);
        
        return $this->sendSMS($phoneNumber, $message, $provider, $voucher);
    }

    /**
     * Send SMS
     */
    public function sendSMS(string $phoneNumber, string $message, string $provider = null, Voucher $voucher = null)
    {
        $provider = $provider ?: $this->defaultProvider;
        
        // Create SMS log
        $smsLog = SmsLog::create([
            'phone_number' => $phoneNumber,
            'message' => $message,
            'provider' => $provider,
            'voucher_id' => $voucher?->id,
            'status' => 'pending',
        ]);

        try {
            $result = match($provider) {
                'jambopay' => $this->sendViaJamboPay($phoneNumber, $message),
                'africastalking' => $this->sendViaAfricasTalking($phoneNumber, $message),
                'twilio' => $this->sendViaTwilio($phoneNumber, $message),
                default => throw new \Exception('Unsupported SMS provider'),
            };

            if ($result['success']) {
                $smsLog->markAsSent($result['external_id']);
                
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'provider' => $provider,
                    'external_id' => $result['external_id'],
                ]);

                return [
                    'success' => true,
                    'sms_log_id' => $smsLog->id,
                    'external_id' => $result['external_id'],
                    'cost' => $result['cost'] ?? 0,
                ];
            } else {
                $smsLog->markAsFailed($result['error']);
                
                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }
        } catch (\Exception $e) {
            $smsLog->markAsFailed($e->getMessage());
            
            Log::error('SMS sending failed', [
                'phone' => $phoneNumber,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS via Africa's Talking
     */
    protected function sendViaAfricasTalking(string $phoneNumber, string $message)
    {
        if (!$this->africasTalking) {
            throw new \Exception("Africa's Talking not configured");
        }

        try {
            $sms = $this->africasTalking->sms();
            
            $result = $sms->send([
                'to' => $phoneNumber,
                'message' => $message,
                'from' => config('services.africastalking.sender_id'),
            ]);

            $recipient = $result['SMSMessageData']['Recipients'][0] ?? null;
            
            if ($recipient && $recipient['status'] === 'Success') {
                return [
                    'success' => true,
                    'external_id' => $recipient['messageId'],
                    'cost' => $recipient['cost'] ?? 0,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $recipient['status'] ?? 'Unknown error',
                ];
            }
        } catch (\Exception $e) {
            throw new \Exception("Africa's Talking error: " . $e->getMessage());
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $phoneNumber, string $message)
    {
        if (!$this->twilio) {
            throw new \Exception('Twilio not configured');
        }

        try {
            $result = $this->twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );

            return [
                'success' => true,
                'external_id' => $result->sid,
                'cost' => 0, // Twilio cost would need to be calculated separately
            ];
        } catch (\Exception $e) {
            throw new \Exception('Twilio error: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS via JamboPay
     */
    protected function sendViaJamboPay(string $phoneNumber, string $message)
    {
        try {
            // Step 1: Get access token
            $accessToken = $this->getJamboPayAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get JamboPay access token');
            }

            // Step 2: Send SMS
            $response = $this->sendJamboPaySms($phoneNumber, $message, $accessToken);
            
            return $response;
        } catch (\Exception $e) {
            throw new \Exception('JamboPay error: ' . $e->getMessage());
        }
    }

    /**
     * Get JamboPay access token
     */
    protected function getJamboPayAccessToken()
    {
        $curl = curl_init();

        // Use the exact credentials and format from your working code
        $postFields = 'grant_type=client_credentials&client_id=' . 
            config('sms.providers.jambopay.client_id') . 
            '&client_secret=' . 
            config('sms.providers.jambopay.client_secret');

        curl_setopt_array($curl, array(
            CURLOPT_URL => config('sms.providers.jambopay.auth_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_SSL_VERIFYPEER => false,  // Disable SSL verification for development
            CURLOPT_SSL_VERIFYHOST => false,  // Disable SSL verification for development
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if (curl_error($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            Log::error('JamboPay cURL error', ['error' => $error]);
            return null;
        }
        
        curl_close($curl);

        Log::info('JamboPay auth response', ['response' => $response, 'http_code' => $httpCode]);

        if ($httpCode !== 200) {
            Log::error('JamboPay auth failed', ['response' => $response, 'http_code' => $httpCode]);
            return null;
        }

        $data = json_decode($response, true);
        
        if (isset($data['access_token'])) {
            Log::info('JamboPay access token obtained successfully');
            return $data['access_token'];
        }

        Log::error('JamboPay access token not found in response', ['response' => $data]);
        return null;
    }

    /**
     * Send SMS using JamboPay API
     */
    protected function sendJamboPaySms(string $phoneNumber, string $message, string $accessToken)
    {
        // Format phone number for Kenyan numbers
        $formattedPhone = $this->formatKenyanPhoneNumber($phoneNumber);
        
        $payload = [
            'contact' => $formattedPhone,
            'message' => $message,
            'callback' => config('sms.providers.jambopay.callback_url'),
            'sender_name' => config('sms.providers.jambopay.sender_name', 'VESEN')
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('sms.providers.jambopay.send_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false,  // Disable SSL verification for development
            CURLOPT_SSL_VERIFYHOST => false,  // Disable SSL verification for development
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        Log::info('JamboPay SMS response', [
            'phone' => $formattedPhone,
            'response' => $response,
            'http_code' => $httpCode
        ]);

        if ($httpCode === 200 || $httpCode === 201) {
            $data = json_decode($response, true);
            
            return [
                'success' => true,
                'external_id' => $data['message_id'] ?? $data['id'] ?? uniqid('jambopay_'),
                'cost' => 0, // Cost would be tracked by JamboPay
            ];
        } else {
            return [
                'success' => false,
                'error' => 'HTTP ' . $httpCode . ': ' . $response,
            ];
        }
    }

    /**
     * Format Kenyan phone number for SMS
     */
    protected function formatKenyanPhoneNumber(string $phoneNumber)
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with 254
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }
        
        // If doesn't start with 254, add it
        if (substr($phoneNumber, 0, 3) !== '254') {
            $phoneNumber = '254' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Build voucher message
     */
    protected function buildVoucherMessage(Voucher $voucher)
    {
        $plan = $voucher->voucherPlan;
        
        $message = "Your WiFi Voucher:\n";
        $message .= "Code: {$voucher->code}\n";
        
        if ($voucher->password) {
            $message .= "Password: {$voucher->password}\n";
        }
        
        $message .= "Plan: {$plan->name}\n";
        $message .= "Duration: {$plan->formatted_duration}\n";
        $message .= "Data: {$plan->formatted_data_limit}\n";
        
        if ($voucher->expires_at) {
            $message .= "Expires: {$voucher->expires_at->format('M j, Y H:i')}\n";
        }
        
        $message .= "\nConnect to WiFi and enter this code to access internet.";
        
        return $message;
    }

    /**
     * Send bulk SMS
     */
    public function sendBulkSMS(array $recipients, string $message, string $provider = null)
    {
        $provider = $provider ?: $this->defaultProvider;
        $results = [];

        foreach ($recipients as $phoneNumber) {
            $results[] = $this->sendSMS($phoneNumber, $message, $provider);
        }

        return $results;
    }

    /**
     * Handle SMS delivery reports
     */
    public function handleDeliveryReport(string $provider, array $data)
    {
        Log::info('SMS Delivery Report', [
            'provider' => $provider,
            'data' => $data,
        ]);

        $externalId = match($provider) {
            'africastalking' => $data['id'] ?? null,
            'twilio' => $data['MessageSid'] ?? null,
            default => $data['id'] ?? null,
        };

        if (!$externalId) {
            return false;
        }

        $smsLog = SmsLog::where('external_id', $externalId)->first();
        
        if (!$smsLog) {
            return false;
        }

        $status = match($provider) {
            'africastalking' => $data['status'] ?? 'unknown',
            'twilio' => $data['MessageStatus'] ?? 'unknown',
            default => $data['status'] ?? 'unknown',
        };

        if (in_array($status, ['Success', 'delivered', 'Delivered'])) {
            $smsLog->markAsDelivered();
        } elseif (in_array($status, ['Failed', 'failed', 'undelivered'])) {
            $smsLog->markAsFailed($data['failureReason'] ?? 'Delivery failed');
        }

        return true;
    }

    /**
     * Get SMS statistics
     */
    public function getSmsStats(string $period = '30days')
    {
        $startDate = match($period) {
            '24hours' => now()->subDay(),
            '7days' => now()->subWeek(),
            '30days' => now()->subMonth(),
            default => now()->subMonth(),
        };

        return [
            'total_sent' => SmsLog::where('created_at', '>=', $startDate)->count(),
            'successful' => SmsLog::where('created_at', '>=', $startDate)->where('status', 'sent')->count(),
            'delivered' => SmsLog::where('created_at', '>=', $startDate)->where('status', 'delivered')->count(),
            'failed' => SmsLog::where('created_at', '>=', $startDate)->where('status', 'failed')->count(),
            'total_cost' => SmsLog::where('created_at', '>=', $startDate)->sum('cost'),
        ];
    }

    /**
     * Retry failed SMS
     */
    public function retryFailedSMS(SmsLog $smsLog)
    {
        if (!$smsLog->canRetry()) {
            return [
                'success' => false,
                'error' => 'SMS cannot be retried (max retries reached or not failed)',
            ];
        }

        return $this->sendSMS(
            $smsLog->phone_number,
            $smsLog->message,
            $smsLog->provider,
            $smsLog->voucher
        );
    }
}
