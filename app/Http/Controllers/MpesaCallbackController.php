<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Voucher;
use App\Services\SmsService;

class MpesaCallbackController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle M-Pesa STK Push callback
     */
    public function stkCallback(Request $request)
    {
        // Log the raw callback for debugging
        Log::info('M-Pesa STK Callback received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw_body' => $request->getContent()
        ]);

        try {
            $callbackData = $request->json('Body.stkCallback');
            
            if (!$callbackData) {
                Log::error('Invalid STK callback data structure');
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid data structure']);
            }

            $merchantRequestId = $callbackData['MerchantRequestID'] ?? null;
            $checkoutRequestId = $callbackData['CheckoutRequestID'] ?? null;
            $resultCode = $callbackData['ResultCode'] ?? null;
            $resultDesc = $callbackData['ResultDesc'] ?? '';

            Log::info('Processing STK callback', [
                'merchant_request_id' => $merchantRequestId,
                'checkout_request_id' => $checkoutRequestId,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc
            ]);

            // Find the payment record
            $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();

            if (!$payment) {
                Log::error('Payment not found for checkout request', ['checkout_request_id' => $checkoutRequestId]);
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment not found']);
            }

            if ($resultCode == 0) {
                // Payment successful
                $callbackMetadata = $callbackData['CallbackMetadata']['Item'] ?? [];
                $paymentDetails = $this->extractPaymentDetails($callbackMetadata);

                $payment->update([
                    'status' => 'completed',
                    'mpesa_receipt_number' => $paymentDetails['mpesa_receipt'],
                    'transaction_date' => $paymentDetails['transaction_date'],
                    'phone_number' => $paymentDetails['phone_number'],
                    'amount' => $paymentDetails['amount']
                ]);

                // Generate voucher
                $voucher = $this->generateVoucher($payment);

                // Send SMS with voucher details
                $this->sendVoucherSms($payment, $voucher);

                Log::info('Payment completed successfully', [
                    'payment_id' => $payment->id,
                    'voucher_id' => $voucher->id,
                    'mpesa_receipt' => $paymentDetails['mpesa_receipt']
                ]);

            } else {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $resultDesc
                ]);

                Log::info('Payment failed', [
                    'payment_id' => $payment->id,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);

        } catch (\Exception $e) {
            Log::error('Error processing STK callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Internal server error']);
        }
    }

    /**
     * Handle M-Pesa validation callback
     */
    public function validation(Request $request)
    {
        Log::info('M-Pesa Validation callback received', $request->all());

        // For now, accept all transactions
        // You can add validation logic here if needed
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }

    /**
     * Handle M-Pesa confirmation callback
     */
    public function confirmation(Request $request)
    {
        Log::info('M-Pesa Confirmation callback received', $request->all());

        try {
            // Process the confirmation data
            $transactionType = $request->input('TransactionType');
            $transId = $request->input('TransID');
            $transTime = $request->input('TransTime');
            $transAmount = $request->input('TransAmount');
            $businessShortCode = $request->input('BusinessShortCode');
            $billRefNumber = $request->input('BillRefNumber');
            $invoiceNumber = $request->input('InvoiceNumber');
            $orgAccountBalance = $request->input('OrgAccountBalance');
            $thirdPartyTransId = $request->input('ThirdPartyTransID');
            $msisdn = $request->input('MSISDN');
            $firstName = $request->input('FirstName');
            $middleName = $request->input('MiddleName');
            $lastName = $request->input('LastName');

            // Log the transaction details
            Log::info('M-Pesa transaction confirmed', [
                'trans_id' => $transId,
                'amount' => $transAmount,
                'phone' => $msisdn,
                'reference' => $billRefNumber
            ]);

            // You can add logic here to handle direct payments to your paybill

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing confirmation callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Internal server error'
            ]);
        }
    }

    /**
     * Extract payment details from callback metadata
     */
    private function extractPaymentDetails(array $metadata)
    {
        $details = [
            'amount' => null,
            'mpesa_receipt' => null,
            'transaction_date' => null,
            'phone_number' => null
        ];

        foreach ($metadata as $item) {
            switch ($item['Name']) {
                case 'Amount':
                    $details['amount'] = $item['Value'];
                    break;
                case 'MpesaReceiptNumber':
                    $details['mpesa_receipt'] = $item['Value'];
                    break;
                case 'TransactionDate':
                    $details['transaction_date'] = $item['Value'];
                    break;
                case 'PhoneNumber':
                    $details['phone_number'] = $item['Value'];
                    break;
            }
        }

        return $details;
    }

    /**
     * Generate voucher for successful payment
     */
    private function generateVoucher(Payment $payment)
    {
        $plan = $payment->plan;
        
        $voucher = Voucher::create([
            'code' => 'V' . strtoupper(uniqid()),
            'username' => 'user_' . uniqid(),
            'password' => strtoupper(substr(md5(uniqid()), 0, 8)),
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
            'expires_at' => now()->addDays($plan->duration_days),
            'data_limit' => $plan->data_limit,
            'time_limit' => $plan->time_limit,
            'status' => 'active'
        ]);

        return $voucher;
    }

    /**
     * Send SMS with voucher details
     */
    private function sendVoucherSms(Payment $payment, Voucher $voucher)
    {
        $message = "WiFi Voucher: Code: {$voucher->code}, Username: {$voucher->username}, Password: {$voucher->password}. Valid for {$payment->plan->duration_days} days. Enjoy your internet!";
        
        try {
            $this->smsService->send($payment->phone_number, $message);
            Log::info('Voucher SMS sent successfully', ['payment_id' => $payment->id]);
        } catch (\Exception $e) {
            Log::error('Failed to send voucher SMS', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
