<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestConfig extends Command
{
    protected $signature = 'config:test-sms';
    protected $description = 'Test SMS configuration';

    public function handle()
    {
        $this->info('Testing SMS Configuration:');
        $this->info('Client ID: ' . config('sms.providers.jambopay.client_id'));
        $this->info('Client Secret: ' . substr(config('sms.providers.jambopay.client_secret'), 0, 10) . '...');
        $this->info('Auth URL: ' . config('sms.providers.jambopay.auth_url'));
        $this->info('Send URL: ' . config('sms.providers.jambopay.send_url'));
        $this->info('Sender Name: ' . config('sms.providers.jambopay.sender_name'));

        return 0;
    }
}
