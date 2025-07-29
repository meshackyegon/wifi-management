<?php

namespace App\Console\Commands;

use App\Models\VoucherPlan;
use App\Services\MobileMoneyService;
use Illuminate\Console\Command;

class CreateTestPurchases extends Command
{
    protected $signature = 'test:create-purchases';
    protected $description = 'Create test purchases for dashboard display';

    public function handle()
    {
        $this->info('Creating test purchases...');

        $service = app(MobileMoneyService::class);
        $plans = VoucherPlan::where('is_active', true)->get();
        
        $testCustomers = [
            ['+254722617738', 'Basic 1 Hour'],
            ['+254722617739', 'Standard 3 Hours'],
            ['+254722617740', 'Premium 24 Hours'],
        ];

        foreach ($testCustomers as [$phone, $planName]) {
            $plan = $plans->where('name', $planName)->first();
            
            if ($plan) {
                $this->info("Creating purchase for {$phone} - {$planName}");
                
                // Initiate payment
                $result = $service->initiatePayment($plan, $phone, 'safaricom_mpesa');
                
                if ($result['success']) {
                    $payment = \App\Models\MobileMoneyPayment::find($result['payment_id']);
                    
                    // Complete payment
                    $reflection = new \ReflectionClass($service);
                    $method = $reflection->getMethod('processSuccessfulPayment');
                    $method->setAccessible(true);
                    
                    $callbackData = [
                        'external_transaction_id' => 'MPESA_' . time() . '_' . rand(1000, 9999),
                        'status' => 'completed'
                    ];
                    
                    $method->invoke($service, $payment, $callbackData);
                    
                    $payment->refresh();
                    $this->line("  ✅ Purchase completed - Voucher: {$payment->voucher->code}");
                }
            }
        }

        $this->info("\n🎉 Test purchases created! Check the admin dashboard:");
        $this->line("http://127.0.0.1:8000/dashboard");
        $this->line("Login: admin@wifimanagement.com / Admin@123");

        return 0;
    }
}
