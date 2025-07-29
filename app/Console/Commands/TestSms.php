<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsService;

class TestSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone=0722617737} {message="Test SMS from WiFi System"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS functionality with JamboPay';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message');

        $this->info("Testing SMS to: {$phone}");
        $this->info("Message: {$message}");
        $this->info("Provider: " . config('sms.default_provider'));

        try {
            $result = $this->smsService->sendSMS($phone, $message);

            if ($result['success']) {
                $this->info("✅ SMS sent successfully!");
                $this->info("SMS Log ID: " . $result['sms_log_id']);
                $this->info("External ID: " . ($result['external_id'] ?? 'N/A'));
            } else {
                $this->error("❌ SMS failed: " . $result['error']);
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
        }
    }
}
