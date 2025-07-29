<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartNgrok extends Command
{
    protected $signature = 'ngrok:start {--port=8000 : The port to expose}';
    protected $description = 'Start NGrok tunnel for M-Pesa callbacks';

    public function handle()
    {
        $port = $this->option('port');
        
        $this->info("Starting NGrok tunnel on port {$port}...");
        $this->info('');
        $this->info('🔗 M-Pesa Callback URLs that will be available:');
        $this->info('');
        $this->info('STK Push Callback: https://YOUR-NGROK-URL.ngrok.io/mpesa/stk-callback');
        $this->info('Validation URL: https://YOUR-NGROK-URL.ngrok.io/mpesa/validation');
        $this->info('Confirmation URL: https://YOUR-NGROK-URL.ngrok.io/mpesa/confirmation');
        $this->info('');
        $this->info('📋 Instructions:');
        $this->info('1. Copy the HTTPS URL from NGrok output below');
        $this->info('2. Update your M-Pesa app configuration with the callback URLs above');
        $this->info('3. Replace YOUR-NGROK-URL with the actual NGrok URL');
        $this->info('');
        $this->warn('⚠️  Keep this terminal open while testing M-Pesa callbacks');
        $this->info('');
        
        // Start NGrok
        $command = "ngrok http {$port}";
        
        $this->info("Executing: {$command}");
        $this->info('');
        
        // Execute NGrok (this will block)
        passthru($command);
    }
}
