<?php

namespace App\Console\Commands;

use App\Models\MobileMoneyPayment;
use App\Models\VoucherPlan;
use App\Models\User;
use App\Services\MobileMoneyService;
use Illuminate\Console\Command;

class TestCashPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cash-payment {phone} {--plan-id=1} {--complete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test cash payment functionality';

    /**
     * Execute the console command.
     */
    public function handle(MobileMoneyService $mobileMoneyService)
    {
        $phone = $this->argument('phone');
        $planId = $this->option('plan-id');
        $complete = $this->option('complete');
        
        $this->info("Testing Cash Payment System");
        $this->info(str_repeat('-', 50));
        
        // Get the plan
        $plan = VoucherPlan::find($planId);
        if (!$plan) {
            $this->error("❌ Plan not found with ID: {$planId}");
            return 1;
        }
        
        $this->info("📋 Plan: {$plan->name} (KES {$plan->price})");
        $this->info("📱 Phone: {$phone}");
        
        if (!$complete) {
            // Create a pending cash payment
            $this->info("\n💰 Creating cash payment request...");
            
            $payment = MobileMoneyPayment::create([
                'transaction_id' => 'CASH_' . time() . '_' . strtoupper(substr(md5(uniqid()), 0, 6)),
                'voucher_plan_id' => $plan->id,
                'phone_number' => $phone,
                'amount' => $plan->price,
                'commission' => $plan->price * 0.03,
                'provider' => 'cash',
                'payment_method' => 'cash',
                'status' => 'pending_cash',
            ]);
            
            $this->info("✅ Cash payment created:");
            $this->info("   Transaction ID: {$payment->transaction_id}");
            $this->info("   Amount: KES {$payment->amount}");
            $this->info("   Status: {$payment->status}");
            
            $this->info("\n💡 To complete this payment, run:");
            $this->info("php artisan test:cash-payment {$phone} --plan-id={$planId} --complete");
            
        } else {
            // Complete an existing pending cash payment
            $this->info("\n🏁 Looking for pending cash payment...");
            
            $payment = MobileMoneyPayment::where('phone_number', $phone)
                ->where('payment_method', 'cash')
                ->where('status', 'pending_cash')
                ->latest()
                ->first();
                
            if (!$payment) {
                $this->error("❌ No pending cash payment found for {$phone}");
                $this->info("💡 Create one first by running without --complete flag");
                return 1;
            }
            
            $this->info("✅ Found pending payment: {$payment->transaction_id}");
            
            // Get admin user
            $admin = User::where('user_type', 'admin')->first();
            if (!$admin) {
                $this->error("❌ No admin user found");
                return 1;
            }
            
            // Simulate cash received (add a bit extra to test change calculation)
            $amountReceived = $payment->amount + 50; // Add 50 for change
            
            $this->info("💵 Simulating cash payment completion...");
            $this->info("   Amount Required: KES {$payment->amount}");
            $this->info("   Amount Received: KES {$amountReceived}");
            $this->info("   Received by: {$admin->name}");
            
            // Mark as cash received
            $payment->markAsCashReceived(
                $admin->id,
                $amountReceived,
                'Test cash payment completed via command'
            );
            
            // Generate voucher
            $voucher = $mobileMoneyService->generateVoucher($payment);
            
            if ($voucher) {
                $payment->update(['voucher_id' => $voucher->id]);
                
                $this->info("✅ Payment completed successfully!");
                $this->info("   Status: {$payment->status}");
                $this->info("   Change Given: KES {$payment->change_given}");
                $this->info("   Voucher Code: {$voucher->code}");
                $this->info("   Username: {$voucher->username}");
                $this->info("   Password: {$voucher->password}");
                
                // Send SMS
                $smsResult = $mobileMoneyService->sendVoucherSms($payment, $voucher);
                if ($smsResult['success']) {
                    $this->info("📱 SMS sent successfully");
                } else {
                    $this->warn("⚠️  SMS failed: " . $smsResult['message']);
                }
                
            } else {
                $this->error("❌ Failed to generate voucher");
                return 1;
            }
        }
        
        $this->info("\n🎉 Test completed successfully!");
        return 0;
    }
}
