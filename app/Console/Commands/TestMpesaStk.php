<?php

namespace App\Console\Commands;

use App\Models\VoucherPlan;
use App\Models\MobileMoneyPayment;
use App\Services\MobileMoneyService;
use App\Services\VoucherService;
use App\Services\SmsService;
use App\Services\MpesaService;
use Illuminate\Console\Command;

class TestMpesaStk extends Command
{
    protected $signature = 'test:mpesa-stk {phone} {amount?} {plan?}';
    protected $description = 'Test real M-Pesa STK Push';

    public function handle(VoucherService $voucherService, SmsService $smsService, MpesaService $mpesaService)
    {
        $phone = $this->argument('phone');
        $amount = $this->argument('amount') ?? 1;
        $planName = $this->argument('plan') ?? 'mini';

        // Format phone number
        if (!str_starts_with($phone, '254')) {
            $phone = '254' . ltrim($phone, '0');
        }

        $this->info("🚀 Testing Real M-Pesa STK Push");
        $this->info("📱 Phone: +{$phone}");
        $this->info("💰 Amount: KES {$amount}");

        // Find voucher plan
        $voucherPlan = VoucherPlan::where('name', 'like', "%{$planName}%")->first();
        if (!$voucherPlan) {
            $voucherPlan = VoucherPlan::where('price', $amount)->first();
        }
        if (!$voucherPlan) {
            $voucherPlan = VoucherPlan::first();
        }

        $this->info("📋 Plan: {$voucherPlan->name} (KES {$voucherPlan->price})");

        $this->info("💳 Initiating payment through MobileMoneyService...");

        try {
            $mobileMoneyService = new MobileMoneyService($voucherService, $smsService, $mpesaService);
            $result = $mobileMoneyService->initiatePayment($voucherPlan, "+{$phone}", 'safaricom_mpesa');

            if ($result['success']) {
                $this->info("✅ STK Push sent successfully!");
                $this->info("🔍 Payment ID: {$result['payment_id']}");
                if (isset($result['checkout_request_id'])) {
                    $this->info("📝 Checkout Request ID: {$result['checkout_request_id']}");
                }
                $this->info("📱 Check your phone for the M-Pesa prompt");
                $this->info("⏰ Waiting for callback...");
                
                // Display callback URL
                $this->info("🔗 Callback URL: " . config('app.url') . '/mobile-money/callback/safaricom_mpesa');
                $this->info("📝 To test with ngrok, make sure your callback URL is set correctly");
            } else {
                $this->error("❌ STK Push failed: {$result['message']}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
        }
    }
}
