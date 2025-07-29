<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MobileMoneyPayment;
use App\Services\MobileMoneyService;

class MpesaCallbackController extends Controller
{
    protected $mobileMoneyService;

    public function __construct(MobileMoneyService $mobileMoneyService)
    {
        $this->mobileMoneyService = $mobileMoneyService;
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

            // Find the payment record using checkout request ID
            $payment = MobileMoneyPayment::where('transaction_id', $checkoutRequestId)
                ->orWhere('reference', $checkoutRequestId)
                ->first();

            if (!$payment) {
                Log::warning('Payment record not found for callback', [
                    'checkout_request_id' => $checkoutRequestId,
                    'merchant_request_id' => $merchantRequestId
                ]);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Payment not found but acknowledged']);
            }

            // Process the callback result
            if ($resultCode == 0) {
                // Success
                $callbackMetadata = $callbackData['CallbackMetadata']['Item'] ?? [];
                $mpesaReceiptNumber = null;
                $transactionDate = null;
                $phoneNumber = null;
                $amount = null;

                // Extract metadata
                foreach ($callbackMetadata as $item) {
                    switch ($item['Name']) {
                        case 'MpesaReceiptNumber':
                            $mpesaReceiptNumber = $item['Value'];
                            break;
                        case 'TransactionDate':
                            $transactionDate = $item['Value'];
                            break;
                        case 'PhoneNumber':
                            $phoneNumber = $item['Value'];
                            break;
                        case 'Amount':
                            $amount = $item['Value'];
                            break;
                    }
                }

                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'provider_response' => json_encode($callbackData),
                    'external_id' => $mpesaReceiptNumber,
                    'completed_at' => now()
                ]);

                Log::info('Payment completed successfully', [
                    'payment_id' => $payment->id,
                    'mpesa_receipt' => $mpesaReceiptNumber,
                    'amount' => $amount,
                    'phone' => $phoneNumber
                ]);

                // Complete the voucher purchase
                $this->mobileMoneyService->completeVoucherPurchase($payment);

            } else {
                // Failed
                $payment->update([
                    'status' => 'failed',
                    'provider_response' => json_encode($callbackData),
                    'failed_at' => now()
                ]);

                Log::info('Payment failed', [
                    'payment_id' => $payment->id,
                    'result_code' => $resultCode,
                    'result_desc' => $resultDesc
                ]);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('Error processing M-Pesa callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Internal server error']);
        }
    }

    /**
     * Handle M-Pesa timeout callback
     */
    public function timeoutCallback(Request $request)
    {
        Log::info('M-Pesa Timeout Callback received', [
            'body' => $request->all()
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Timeout callback received']);
    }

    /**
     * Handle M-Pesa validation callback
     */
    public function validation(Request $request)
    {
        Log::info('M-Pesa Validation callback received', $request->all());

        // For now, accept all transactions
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
            $transactionDetails = [
                'transaction_type' => $request->input('TransactionType'),
                'trans_id' => $request->input('TransID'),
                'trans_time' => $request->input('TransTime'),
                'trans_amount' => $request->input('TransAmount'),
                'business_short_code' => $request->input('BusinessShortCode'),
                'bill_ref_number' => $request->input('BillRefNumber'),
                'invoice_number' => $request->input('InvoiceNumber'),
                'org_account_balance' => $request->input('OrgAccountBalance'),
                'third_party_trans_id' => $request->input('ThirdPartyTransID'),
                'msisdn' => $request->input('MSISDN'),
                'first_name' => $request->input('FirstName'),
                'middle_name' => $request->input('MiddleName'),
                'last_name' => $request->input('LastName')
            ];

            // Log the transaction details
            Log::info('M-Pesa transaction confirmed', $transactionDetails);

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
}
