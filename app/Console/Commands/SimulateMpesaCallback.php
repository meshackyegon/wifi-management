<?php

namespace App\Console\Commands;

use App\Models\MobileMoneyPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SimulateMpesaCallback extends Command
{
    protected $signature = 'test:mpesa-callback {payment_id}';
    protected $description = 'Simulate M-Pesa STK Push callback for testing';

    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        
        $payment = MobileMoneyPayment::find($paymentId);
        if (!$payment) {
            $this->error("Payment with ID {$paymentId} not found");
            return;
        }

        $this->info("🔄 Simulating M-Pesa callback for payment {$paymentId}");
        $this->info("📱 Phone: {$payment->phone_number}");
        $this->info("💰 Amount: KES {$payment->amount}");
        $this->info("🔍 External ID: {$payment->external_transaction_id}");

        // Create successful callback payload
        $callbackPayload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $payment->reference_number ?: 'TEST-MERCHANT-' . $payment->id,
                    'CheckoutRequestID' => $payment->external_transaction_id,
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            [
                                'Name' => 'Amount',
                                'Value' => (float) $payment->amount
                            ],
                            [
                                'Name' => 'MpesaReceiptNumber',
                                'Value' => 'RGD' . time() . 'X' . rand(1000, 9999)
                            ],
                            [
                                'Name' => 'TransactionDate',
                                'Value' => now()->format('YmdHis')
                            ],
                            [
                                'Name' => 'PhoneNumber',
                                'Value' => ltrim($payment->phone_number, '+')
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            // Send the callback to our local endpoint
            $callbackUrl = 'http://localhost:8000/mpesa/stk-callback';
            $this->info("🌐 Callback URL: {$callbackUrl}");
            $response = Http::post($callbackUrl, $callbackPayload);
            
            if ($response->successful()) {
                $this->info("✅ Callback simulation successful!");
                $this->info("📋 Response: " . $response->body());
                
                // Check if payment was updated
                $payment->refresh();
                $this->info("💳 Payment Status: {$payment->status}");
                
                if ($payment->voucher_id) {
                    $voucher = $payment->voucher;
                    $this->info("🎫 Voucher Generated: {$voucher->code}");
                    $this->info("⏰ Valid Until: {$voucher->expires_at}");
                }
            } else {
                $this->error("❌ Callback simulation failed");
                $this->error("Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
