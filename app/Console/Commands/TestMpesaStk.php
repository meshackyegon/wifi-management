<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MobileMoneyService;
use App\Models\VoucherPlan;
use Illuminate\Support\Facades\Log;

class TestMpesaSTK extends Command
{
    protected $signature = 'test:mpesa-stk {phone} {--amount=1}';
    protected $description = 'Test M-Pesa STK Push with real API call';

    public function handle()
    {
        $phone = $this->argument('phone');
        $amount = $this->option('amount');
        
        $this->info("Testing M-Pesa STK Push...");
        $this->info("Phone: {$phone}");
        $this->info("Amount: KES {$amount}");
        
        // Get or create a voucher plan for testing
        $plan = VoucherPlan::where('price', $amount)->first();
        if (!$plan) {
            $plan = VoucherPlan::first();
            if (!$plan) {
                $this->error('No voucher plans found. Please run the seeder first.');
                return;
            }
        }
        
        $this->info("Using plan: {$plan->name} (KES {$plan->price})");
        
        try {
            $mobileMoneyService = app(MobileMoneyService::class);
            
            $result = $mobileMoneyService->initiatePayment(
                $plan,
                $phone,
                'safaricom_mpesa'
            );
            
            if ($result['success']) {
                $this->info("✅ STK Push sent successfully!");
                $this->info("Transaction ID: " . $result['transaction_id']);
                $this->info("Message: " . $result['message']);
                $this->info("Check your phone for the M-Pesa payment request.");
            } else {
                $this->error("❌ STK Push failed!");
                $this->error("Message: " . $result['message']);
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            Log::error('M-Pesa STK Test Error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
