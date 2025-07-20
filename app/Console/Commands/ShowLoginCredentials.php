<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\VoucherPlan;
use App\Models\Router;

class ShowLoginCredentials extends Command
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
    protected $description = 'Display system login credentials and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎉 WiFi Management System - Login Credentials');
        $this->info('===============================================');
        $this->newLine();
        
        // Get users with roles
        $users = User::with('roles')->get();
        
        $this->info('👤 User Accounts:');
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ');
            $this->line("   {$user->name} ({$user->email})");
            $this->line("   Role: {$roles}");
            
            // Show default passwords based on role
            if ($user->hasRole('admin')) {
                $this->line("   Password: Admin@123");
            } elseif ($user->hasRole('agent')) {
                $this->line("   Password: Agent@123");
            } else {
                $this->line("   Password: Customer@123");
            }
            $this->newLine();
        }
        
        $this->info('📊 System Statistics:');
        $this->line('   Users: ' . User::count());
        $this->line('   Voucher Plans: ' . VoucherPlan::count());
        $this->line('   Routers: ' . Router::count());
        $this->newLine();
        
        $this->info('🔗 Access URLs:');
        $this->line('   Dashboard: http://127.0.0.1:8000/dashboard');
        $this->line('   Buy Vouchers: http://127.0.0.1:8000/buy-voucher');
        $this->line('   Admin Login: http://127.0.0.1:8000/login');
        $this->newLine();
        
        $this->info('🚀 Your WiFi Management System is ready to go!');
        
        return 0;
    }
}
