<?php

namespace App\Console\Commands;

use App\Models\MobileMoneyPayment;
use App\Services\MobileMoneyService;
use Illuminate\Console\Command;

class SimulatePaymentSuccess extends Command
{
    protected $signature = 'payment:simulate-success {payment_id}';
    protected $description = 'Simulate successful payment completion for testing';

    public function handle()
    {
        $paymentId = $this->argument('payment_id');
        
        $payment = MobileMoneyPayment::find($paymentId);
        
        if (!$payment) {
            $this->error("Payment with ID {$paymentId} not found!");
            return 1;
        }

        if ($payment->status !== 'pending') {
            $this->error("Payment {$paymentId} is not pending (current status: {$payment->status})");
            return 1;
        }

        $this->info("Simulating successful payment for:");
        $this->info("Payment ID: {$payment->id}");
        $this->info("Phone: {$payment->phone_number}");
        $this->info("Amount: KES {$payment->amount}");
        $this->info("Plan: {$payment->voucherPlan->name}");

        // Simulate M-Pesa callback data
        $callbackData = [
            'external_transaction_id' => 'MPESA_' . time(),
            'phone_number' => $payment->phone_number,
            'amount' => $payment->amount,
            'transaction_code' => 'QHX' . strtoupper(substr(md5(time()), 0, 6)),
            'status' => 'completed',
            'timestamp' => now()->toISOString(),
        ];

        // Use reflection to access the protected method
        $service = app(MobileMoneyService::class);
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('processSuccessfulPayment');
        $method->setAccessible(true);
        
        $result = $method->invoke($service, $payment, $callbackData);

        if ($result) {
            $this->info("✅ Payment simulation successful!");
            $this->info("📱 SMS notifications sent");
            $this->info("🎫 Voucher generated");
            
            // Refresh payment to get updated data
            $payment->refresh();
            
            if ($payment->voucher) {
                $this->info("Voucher Code: {$payment->voucher->code}");
            }
        } else {
            $this->error("❌ Payment simulation failed!");
        }

        return 0;
    }
}
