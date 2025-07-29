<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestJamboPayAuth extends Command
{
    protected $signature = 'jambopay:test-auth';
    protected $description = 'Test JamboPay authentication directly';

    public function handle()
    {
        $this->info('Testing JamboPay Authentication...');

        $clientId = config('sms.providers.jambopay.client_id');
        $clientSecret = config('sms.providers.jambopay.client_secret');
        $authUrl = config('sms.providers.jambopay.auth_url');

        $this->info("Client ID: {$clientId}");
        $this->info("Auth URL: {$authUrl}");

        $curl = curl_init();

        $postFields = "grant_type=client_credentials&client_id={$clientId}&client_secret={$clientSecret}";
        
        $this->info("Post Fields: {$postFields}");

        curl_setopt_array($curl, array(
            CURLOPT_URL => $authUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_SSL_VERIFYPEER => false,  // Disable SSL verification for development
            CURLOPT_SSL_VERIFYHOST => false,  // Disable SSL verification for development
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $this->info("HTTP Code: {$httpCode}");
        $this->info("Response: {$response}");
        
        if ($error) {
            $this->error("cURL Error: {$error}");
        }

        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['access_token'])) {
                $this->info("✅ Success! Access token obtained: " . substr($data['access_token'], 0, 50) . '...');
            } else {
                $this->error("❌ No access token in response");
            }
        } else {
            $this->error("❌ Authentication failed");
        }

        return 0;
    }
}
