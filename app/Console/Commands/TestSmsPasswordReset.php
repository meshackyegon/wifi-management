<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SmsPasswordResetService;
use Illuminate\Console\Command;

class TestSmsPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sms-password-reset {phone} {--create-user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS password reset functionality';

    /**
     * Execute the console command.
     */
    public function handle(SmsPasswordResetService $smsPasswordResetService)
    {
        $phone = $this->argument('phone');
        
        $this->info("Testing SMS Password Reset for: {$phone}");
        $this->info(str_repeat('-', 50));

        // Check if --create-user flag is provided
        if ($this->option('create-user')) {
            $this->createTestUser($phone);
        }

        // Check if user exists
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            $this->error("❌ No user found with phone number: {$phone}");
            $this->info("💡 Use --create-user flag to create a test user");
            return;
        }

        $this->info("✅ User found: {$user->name} ({$user->email})");

        // Test sending SMS
        $this->info("\n📱 Sending password reset SMS...");
        $result = $smsPasswordResetService->sendPasswordResetSms($phone);

        if ($result['success']) {
            $this->info("✅ " . $result['message']);
            
            // Get the latest token for display
            $latestToken = \App\Models\SmsPasswordResetToken::where('phone', $phone)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($latestToken) {
                $this->info("\n📋 Reset Details:");
                $this->info("Token: {$latestToken->token}");
                $this->info("Expires: {$latestToken->expires_at->format('Y-m-d H:i:s')}");
                $this->info("Phone: {$latestToken->phone}");
                
                $this->info("\n🔧 Test the reset process at:");
                $this->info(route('password.sms.reset.form') . "?phone=" . urlencode($phone));
            }
        } else {
            $this->error("❌ " . $result['message']);
        }

        return 0;
    }

    private function createTestUser($phone)
    {
        $this->info("👤 Creating test user...");
        
        $cleanPhone = $this->cleanPhoneNumber($phone);
        
        $user = User::updateOrCreate(
            ['phone' => $cleanPhone],
            [
                'name' => 'Test User',
                'email' => 'test.user.' . time() . '@example.com',
                'password' => bcrypt('password123'),
                'user_type' => 'customer',
                'is_active' => true,
            ]
        );

        $this->info("✅ Test user created/updated: {$user->name} ({$user->email})");
        return $user;
    }

    private function cleanPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, and other non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // If it starts with +254, keep it
        if (str_starts_with($phone, '+254')) {
            return $phone;
        }
        
        // If it starts with 254, add +
        if (str_starts_with($phone, '254')) {
            return '+' . $phone;
        }
        
        // If it starts with 0, replace with +254
        if (str_starts_with($phone, '0')) {
            return '+254' . substr($phone, 1);
        }
        
        // If it's 9 digits, assume it's missing country code
        if (strlen($phone) === 9) {
            return '+254' . $phone;
        }
        
        return $phone;
    }
}
