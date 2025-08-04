<?php

namespace App\Services;

use App\Models\MobileMoneyPayment;
use App\Models\VoucherPlan;
use App\Models\Voucher;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MobileMoneyService
{
    protected $client;
    protected $voucherService;
    protected $smsService;

    public function __construct(VoucherService $voucherService, SmsService $smsService)
    {
        $this->client = new Client();
        $this->voucherService = $voucherService;
        $this->smsService = $smsService;
    }

    /**
     * Initiate mobile money payment
     */
    public function initiatePayment(VoucherPlan $plan, string $phoneNumber, string $provider)
    {
        $payment = new MobileMoneyPayment();
        $payment->generateTransactionId();
        $payment->voucher_plan_id = $plan->id;
        $payment->phone_number = $phoneNumber;
        $payment->amount = $plan->price;
        $payment->commission = $plan->price * 0.03; // 3% commission
        $payment->provider = $provider;
        $payment->status = 'pending';
        $payment->save();

        try {
            $response = $this->sendPaymentRequest($payment);
            
            if ($response['success']) {
                $payment->external_transaction_id = $response['transaction_id'] ?? null;
                $payment->reference_number = $response['reference'] ?? null;
                $payment->save();

                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'message' => 'Payment initiated successfully. Please complete payment on your phone.',
                    'reference' => $payment->reference_number,
                ];
            } else {
                $payment->markAsFailed($response['message'] ?? 'Payment initiation failed');
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Payment failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Mobile Money Payment Error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            $payment->markAsFailed($e->getMessage());

            return [
                'success' => false,
                'message' => 'Payment service temporarily unavailable',
            ];
        }
    }

    /**
     * Send payment request to mobile money provider
     */
    protected function sendPaymentRequest(MobileMoneyPayment $payment)
    {
        $config = $this->getProviderConfig($payment->provider);
        
        if (!$config) {
            throw new \Exception('Provider configuration not found for: ' . $payment->provider);
        }

        Log::info('Sending payment request', [
            'provider' => $payment->provider,
            'config_endpoint' => $config['endpoint'],
            'payment_id' => $payment->id,
        ]);

        $payload = $this->buildPaymentPayload($payment, $config);
        
        Log::info('Payment payload', [
            'provider' => $payment->provider,
            'payload' => $payload,
        ]);
        
        try {
            // For sandbox testing, simulate successful response for M-Pesa
            if ($payment->provider === 'safaricom_mpesa' && config('mobile-money.sandbox_mode', true)) {
                return $this->simulateMpesaResponse($payment);
            }

            $response = $this->client->post($config['endpoint'], [
                'headers' => $config['headers'],
                'json' => $payload,
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Provider response', [
                'provider' => $payment->provider,
                'response' => $data,
            ]);
            
            return $this->parseProviderResponse($data, $payment->provider);
        } catch (\Exception $e) {
            Log::error('Payment request failed', [
                'provider' => $payment->provider,
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);
            throw new \Exception('Failed to connect to payment provider: ' . $e->getMessage());
        }
    }

    /**
     * Simulate M-Pesa response for sandbox testing
     */
    protected function simulateMpesaResponse(MobileMoneyPayment $payment)
    {
        Log::info('Simulating M-Pesa sandbox response', [
            'payment_id' => $payment->id,
            'phone' => $payment->phone_number,
        ]);

        // Simulate different responses based on test phone numbers
        $testNumbers = [
            '+254708374149' => 'success',
            '+254708374150' => 'insufficient_funds', 
            '+254708374151' => 'invalid_account',
        ];

        $result = $testNumbers[$payment->phone_number] ?? 'success';

        if ($result === 'success') {
            return [
                'success' => true,
                'transaction_id' => 'ws_CO_' . date('dmY') . '_' . time(),
                'message' => 'STK Push initiated successfully',
            ];
        } else {
            return [
                'success' => false,
                'message' => $result === 'insufficient_funds' ? 'Insufficient funds' : 'Invalid account',
            ];
        }
    }

    /**
     * Build payment payload for different providers
     */
    protected function buildPaymentPayload(MobileMoneyPayment $payment, array $config)
    {
        $basePayload = [
            'amount' => $payment->amount,
            'phone_number' => $payment->phone_number,
            'reference' => $payment->transaction_id,
            'description' => "WiFi Voucher - {$payment->voucherPlan->name}",
        ];

        return match($payment->provider) {
            'mtn_mobile_money' => $this->buildMTNPayload($basePayload, $config),
            'airtel_money' => $this->buildAirtelPayload($basePayload, $config),
            'safaricom_mpesa' => $this->buildSafaricomPayload($basePayload, $config),
            'vodacom_mpesa' => $this->buildVodacomPayload($basePayload, $config),
            'tigo_pesa' => $this->buildTigoPesaPayload($basePayload, $config),
            default => $basePayload,
        };
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(string $provider, array $data)
    {
        Log::info('Mobile Money Callback', [
            'provider' => $provider,
            'data' => $data,
        ]);

        $payment = $this->findPaymentFromCallback($provider, $data);
        
        if (!$payment) {
            Log::warning('Payment not found for callback', [
                'provider' => $provider,
                'data' => $data,
            ]);
            return false;
        }

        $status = $this->parseCallbackStatus($provider, $data);
        
        if ($status === 'success') {
            return $this->processSuccessfulPayment($payment, $data);
        } elseif ($status === 'failed') {
            $payment->markAsFailed($data['message'] ?? 'Payment failed');
        }

        return true;
    }

    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment(MobileMoneyPayment $payment, array $callbackData)
    {
        try {
            // Mark payment as successful
            $payment->markAsSuccessful(
                $callbackData['external_transaction_id'] ?? null,
                $callbackData
            );

            // Generate voucher
            $vouchers = $this->voucherService->generateVouchers(
                $payment->voucherPlan,
                1,
                null,
                null
            );

            $voucher = $vouchers[0];
            $payment->voucher_id = $voucher->id;
            $payment->save();

            // Send SMS with voucher code to customer
            $this->smsService->sendVoucherSMS($payment->phone_number, $voucher);

            // Send SMS notification to admin about successful purchase
            $adminPhone = config('sms.admin_notification_phone');
            if ($adminPhone) {
                $adminMessage = "New voucher purchased!\n";
                $adminMessage .= "Plan: {$payment->voucherPlan->name}\n";
                $adminMessage .= "Amount: KES {$payment->amount}\n";
                $adminMessage .= "Customer: {$payment->phone_number}\n";
                $adminMessage .= "Code: {$voucher->code}\n";
                $adminMessage .= "Time: " . now()->format('Y-m-d H:i:s');
                
                $this->smsService->sendSMS($adminPhone, $adminMessage);
            }

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'voucher_code' => $voucher->code,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error processing successful payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get provider configuration
     */
    protected function getProviderConfig(string $provider)
    {
        $configs = [
            'mtn_mobile_money' => [
                'endpoint' => config('services.mtn.endpoint') . '/collection/v1_0/requesttopay',
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.mtn.api_key'),
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => config('services.mtn.subscription_key'),
                ],
            ],
            'airtel_money' => [
                'endpoint' => config('services.airtel.endpoint') . '/merchant/v1/payments',
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.airtel.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ],
            'safaricom_mpesa' => [
                'endpoint' => config('services.safaricom.endpoint') . '/mpesa/stkpush/v1/processrequest',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getSafaricomAccessToken(),
                    'Content-Type' => 'application/json',
                ],
            ],
            'vodacom_mpesa' => [
                'endpoint' => config('services.vodacom.endpoint') . '/ipg/v2/vodacomTZN/c2bPayment/singleStage',
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.vodacom.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ],
            'tigo_pesa' => [
                'endpoint' => config('services.tigo.endpoint') . '/v1/tigo/payments',
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.tigo.api_key'),
                    'Content-Type' => 'application/json',
                ],
            ],
        ];

        return $configs[$provider] ?? null;
    }

    /**
     * Get Safaricom access token
     */
    protected function getSafaricomAccessToken()
    {
        try {
            $consumerKey = config('services.safaricom.consumer_key');
            $consumerSecret = config('services.safaricom.consumer_secret');
            $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

            $response = $this->client->get(config('services.safaricom.endpoint') . '/oauth/v1/generate?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Failed to get Safaricom access token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Provider-specific payload builders
     */
    protected function buildMTNPayload(array $base, array $config)
    {
        return [
            'amount' => (string) $base['amount'],
            'currency' => 'UGX', // or other currency
            'externalId' => $base['reference'],
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $base['phone_number'],
            ],
            'payerMessage' => $base['description'],
            'payeeNote' => $base['description'],
        ];
    }

    protected function buildAirtelPayload(array $base, array $config)
    {
        return [
            'reference' => $base['reference'],
            'subscriber' => [
                'country' => 'UG', // or other country
                'currency' => 'UGX',
                'msisdn' => $base['phone_number'],
            ],
            'transaction' => [
                'amount' => $base['amount'],
                'country' => 'UG',
                'currency' => 'UGX',
                'id' => $base['reference'],
            ],
        ];
    }

    protected function buildSafaricomPayload(array $base, array $config)
    {
        return [
            'BusinessShortCode' => config('services.safaricom.shortcode'),
            'Password' => base64_encode(config('services.safaricom.shortcode') . config('services.safaricom.passkey') . date('YmdHis')),
            'Timestamp' => date('YmdHis'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $base['amount'],
            'PartyA' => $base['phone_number'],
            'PartyB' => config('services.safaricom.shortcode'),
            'PhoneNumber' => $base['phone_number'],
            'CallBackURL' => route('mobile-money.callback', 'safaricom_mpesa'),
            'AccountReference' => $base['reference'],
            'TransactionDesc' => $base['description'],
        ];
    }

    protected function buildVodacomPayload(array $base, array $config)
    {
        // Similar to Safaricom but with Vodacom specifics
        return $this->buildSafaricomPayload($base, $config);
    }

    protected function buildTigoPesaPayload(array $base, array $config)
    {
        return [
            'MasterMerchant' => config('services.tigo.merchant_id'),
            'MerchantReference' => $base['reference'],
            'Amount' => $base['amount'],
            'MSISDNNumber' => $base['phone_number'],
            'Description' => $base['description'],
        ];
    }

    /**
     * Parse provider response
     */
    protected function parseProviderResponse(array $data, string $provider)
    {
        return match($provider) {
            'mtn_mobile_money' => [
                'success' => !isset($data['error']),
                'transaction_id' => $data['referenceId'] ?? null,
                'message' => $data['error']['message'] ?? 'Payment initiated',
            ],
            'safaricom_mpesa' => [
                'success' => ($data['ResponseCode'] ?? null) === '0',
                'transaction_id' => $data['CheckoutRequestID'] ?? null,
                'message' => $data['ResponseDescription'] ?? 'Payment initiated',
            ],
            default => [
                'success' => true,
                'transaction_id' => $data['transaction_id'] ?? null,
                'message' => 'Payment initiated',
            ],
        };
    }

    /**
     * Find payment from callback data
     */
    protected function findPaymentFromCallback(string $provider, array $data)
    {
        $reference = match($provider) {
            'mtn_mobile_money' => $data['externalId'] ?? null,
            'safaricom_mpesa' => $data['Body']['stkCallback']['CheckoutRequestID'] ?? null,
            default => $data['reference'] ?? null,
        };

        if (!$reference) {
            return null;
        }

        return MobileMoneyPayment::where('transaction_id', $reference)
            ->orWhere('external_transaction_id', $reference)
            ->first();
    }

    /**
     * Parse callback status
     */
    protected function parseCallbackStatus(string $provider, array $data)
    {
        return match($provider) {
            'mtn_mobile_money' => isset($data['status']) && $data['status'] === 'SUCCESSFUL' ? 'success' : 'failed',
            'safaricom_mpesa' => ($data['Body']['stkCallback']['ResultCode'] ?? null) === 0 ? 'success' : 'failed',
            default => ($data['status'] ?? 'failed') === 'success' ? 'success' : 'failed',
        };
    }
}
