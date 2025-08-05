<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email : The email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email configuration...");
        $this->info("Recipient: {$email}");
        $this->info("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->info("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->info("From Address: " . config('mail.from.address'));
        
        try {
            Mail::raw('This is a test email from WiFi Management System. If you received this, your email configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - WiFi Management System')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("✅ Test email sent successfully to {$email}");
            $this->info("Check your inbox (and spam folder) for the test email.");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email");
            $this->error("Error: " . $e->getMessage());
            
            $this->newLine();
            $this->warn("Common solutions:");
            $this->line("1. Check your SMTP credentials in .env file");
            $this->line("2. For Gmail, use App Password instead of regular password");
            $this->line("3. Ensure 2FA is enabled on Gmail and generate App Password");
            $this->line("4. Check network connectivity and firewall settings");
        }
    }
}
