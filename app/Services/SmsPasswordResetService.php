<?php

namespace App\Services;

use App\Models\SmsPasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SmsPasswordResetService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send password reset SMS to phone number
     */
    public function sendPasswordResetSms(string $phone): array
    {
        try {
            // Clean phone number format
            $phone = $this->cleanPhoneNumber($phone);

            // Find user by phone
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'No account found with this phone number.'
                ];
            }

            // Generate temporary password and token
            $temporaryPassword = $this->generateTemporaryPassword();
            $token = Str::random(6); // 6-digit token

            // Delete old tokens for this phone
            SmsPasswordResetToken::where('phone', $phone)->delete();

            // Create new token
            $resetToken = SmsPasswordResetToken::create([
                'phone' => $phone,
                'token' => $token,
                'password' => Hash::make($temporaryPassword),
                'expires_at' => now()->addMinutes(15), // 15 minutes expiry
            ]);

            // Send SMS
            $message = "Your WiFi Management password reset:\n";
            $message .= "Temporary Password: {$temporaryPassword}\n";
            $message .= "Verification Code: {$token}\n";
            $message .= "Valid for 15 minutes. Login and change your password immediately.";

            $smsResult = $this->smsService->sendSms($phone, $message);

            if ($smsResult['success']) {
                return [
                    'success' => true,
                    'message' => 'Password reset SMS sent successfully. Check your phone for temporary password and verification code.',
                    'token_id' => $resetToken->id
                ];
            } else {
                // Delete token if SMS failed
                $resetToken->delete();
                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $smsResult['message']
                ];
            }

        } catch (\Exception $e) {
            \Log::error('SMS Password Reset Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ];
        }
    }

    /**
     * Reset password using temporary password and token
     */
    public function resetPassword(string $phone, string $token, string $temporaryPassword, string $newPassword): array
    {
        try {
            // Clean phone number
            $phone = $this->cleanPhoneNumber($phone);

            // Find valid reset token
            $resetToken = SmsPasswordResetToken::getValidToken($phone, $token);
            if (!$resetToken) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired verification code.'
                ];
            }

            // Verify temporary password
            if (!Hash::check($temporaryPassword, $resetToken->password)) {
                return [
                    'success' => false,
                    'message' => 'Invalid temporary password.'
                ];
            }

            // Find user
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.'
                ];
            }

            // Update user password
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // Mark token as used
            $resetToken->markAsUsed();

            // Clean up expired tokens
            SmsPasswordResetToken::cleanExpired();

            return [
                'success' => true,
                'message' => 'Password reset successfully. You can now login with your new password.'
            ];

        } catch (\Exception $e) {
            \Log::error('Password Reset Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while resetting your password.'
            ];
        }
    }

    /**
     * Generate a temporary password
     */
    private function generateTemporaryPassword(): string
    {
        // Generate a 8-character temporary password with letters and numbers
        return strtoupper(Str::random(4)) . rand(1000, 9999);
    }

    /**
     * Clean and format phone number
     */
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
