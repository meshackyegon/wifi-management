<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowSystemCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:show-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all system login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== WiFi Management System - Login Credentials ===');
        $this->newLine();
        
        // Admin credentials
        $admin = User::where('email', 'admin@wifimanagement.com')->first();
        if ($admin) {
            $this->info('🔑 ADMIN LOGIN:');
            $this->line('   Email: admin@wifimanagement.com');
            $this->line('   Password: Admin@123');
            $this->line('   URL: ' . url('/login'));
        }
        
        $this->newLine();
        
        // Agent credentials
        $agent = User::where('email', 'agent@wifimanagement.com')->first();
        if ($agent) {
            $this->info('👤 AGENT LOGIN:');
            $this->line('   Email: agent@wifimanagement.com');
            $this->line('   Password: Agent@123');
            $this->line('   URL: ' . url('/login'));
        }
        
        $this->newLine();
        
        // Customer voucher purchase
        $this->info('🛒 CUSTOMER VOUCHER PURCHASE:');
        $this->line('   URL: ' . url('/buy-voucher'));
        $this->line('   Test Phone (M-Pesa): +254708374149');
        $this->line('   Test Phone (M-Pesa): +254722617737');
        
        $this->newLine();
        
        // API endpoints
        $this->info('🔗 API ENDPOINTS:');
        $this->line('   Payment Initiate: POST ' . url('/mobile-money/initiate'));
        $this->line('   Payment Status: POST ' . url('/mobile-money/check-status'));
        $this->line('   M-Pesa Callback: POST ' . url('/mobile-money/callback/safaricom_mpesa'));
        
        $this->newLine();
        
        // Development URLs
        if (config('app.env') === 'local') {
            $this->info('🌐 DEVELOPMENT URLs:');
            $this->line('   Local Server: http://localhost:8000');
            $this->line('   NGrok URL: ' . config('app.ngrok_url', 'Not configured'));
        }
        
        $this->newLine();
        $this->warn('⚠️  Keep these credentials secure and change default passwords in production!');
    }
}
