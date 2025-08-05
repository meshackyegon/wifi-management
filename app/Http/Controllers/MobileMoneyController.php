<?php

namespace App\Http\Controllers;

use App\Models\VoucherPlan;
use App\Models\MobileMoneyPayment;
use App\Services\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MobileMoneyController extends Controller
{
    protected $mobileMoneyService;

    public function __construct(MobileMoneyService $mobileMoneyService)
    {
        $this->mobileMoneyService = $mobileMoneyService;
    }

    /**
     * Show mobile money payment form
     */
    public function showPaymentForm(VoucherPlan $plan = null)
    {
        if ($plan) {
            // Show payment form for specific plan
            $selectedPlan = $plan;
            $plans = null;
        } else {
            // Show plan selection
            $plans = VoucherPlan::active()->orderBy('price', 'asc')->get();
            $selectedPlan = null;
        }

        return view('mobile-money.payment-form', compact('plans', 'selectedPlan'));
    }

    /**
     * Initiate mobile money payment
     */
    public function initiatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:voucher_plans,id',
            'phone_number' => 'required|string|min:8|max:15',
            'provider' => 'required|in:mtn_mobile_money,airtel_money,safaricom_mpesa,vodacom_mpesa,tigo_pesa,orange_money,cash',
            'customer_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|size:2',
        ]);

        if ($validator->fails()) {
            Log::warning('Payment validation failed', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan = VoucherPlan::findOrFail($request->plan_id);
        
        if (!$plan->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This plan is not currently available',
            ], 400);
        }

        try {
            // Handle cash payment
            if ($request->provider === 'cash') {
                return $this->initiateCashPayment($plan, $request->phone_number, $request->customer_name);
            }

            // Handle mobile money payment
            $result = $this->mobileMoneyService->initiatePayment(
                $plan,
                $request->phone_number,
                $request->provider
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Payment initiation error', [
                'plan_id' => $request->plan_id,
                'phone' => $request->phone_number,
                'provider' => $request->provider,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to process payment at this time. Please try again later.',
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID is required',
            ], 422);
        }

        $payment = MobileMoneyPayment::where('transaction_id', $request->transaction_id)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'payment' => [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'provider' => $payment->provider_display_name,
                'created_at' => $payment->created_at->toISOString(),
                'voucher_code' => $payment->voucher?->code,
            ],
        ]);
    }

    /**
     * Handle payment callbacks from providers
     */
    public function handleCallback(Request $request, string $provider)
    {
        Log::info('Received payment callback', [
            'provider' => $provider,
            'data' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        try {
            $result = $this->mobileMoneyService->handleCallback($provider, $request->all());

            if ($result) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false], 400);
            }
        } catch (\Exception $e) {
            Log::error('Callback processing error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Show payment history
     */
    public function paymentHistory(Request $request)
    {
        $user = auth()->user();
        
        $query = MobileMoneyPayment::with(['voucherPlan', 'voucher']);

        // Filter by user type
        if ($user->isCustomer()) {
            $query->where('phone_number', $user->phone);
        } elseif (!$user->isAdmin()) {
            // For agents, show payments for vouchers they generated
            $query->whereHas('voucher', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('provider') && $request->provider !== '') {
            $query->where('provider', $request->provider);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        return view('mobile-money.history', compact('payments'));
    }

    /**
     * Retry failed payment
     */
    public function retryPayment(Request $request, MobileMoneyPayment $payment)
    {
        if (!$payment->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment cannot be retried',
            ], 400);
        }

        try {
            $result = $this->mobileMoneyService->initiatePayment(
                $payment->voucherPlan,
                $payment->phone_number,
                $payment->provider
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Payment retry error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retry payment at this time',
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $period = $request->get('period', '30days');
        
        $startDate = match($period) {
            '24hours' => now()->subDay(),
            '7days' => now()->subWeek(),
            '30days' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '1year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $stats = [
            'total_payments' => MobileMoneyPayment::where('created_at', '>=', $startDate)->count(),
            'successful_payments' => MobileMoneyPayment::where('created_at', '>=', $startDate)
                ->where('status', 'success')->count(),
            'failed_payments' => MobileMoneyPayment::where('created_at', '>=', $startDate)
                ->where('status', 'failed')->count(),
            'pending_payments' => MobileMoneyPayment::where('created_at', '>=', $startDate)
                ->where('status', 'pending')->count(),
            'total_revenue' => MobileMoneyPayment::where('created_at', '>=', $startDate)
                ->where('status', 'success')->sum('amount'),
            'total_commission' => MobileMoneyPayment::where('created_at', '>=', $startDate)
                ->where('status', 'success')->sum('commission'),
        ];

        // Provider breakdown
        $providerStats = MobileMoneyPayment::where('created_at', '>=', $startDate)
            ->selectRaw('provider, 
                COUNT(*) as total_count,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as success_amount')
            ->groupBy('provider')
            ->get()
            ->map(function($item) {
                return [
                    'provider' => $item->provider,
                    'provider_name' => (new MobileMoneyPayment(['provider' => $item->provider]))->provider_display_name,
                    'total_count' => $item->total_count,
                    'success_count' => $item->success_count,
                    'success_rate' => $item->total_count > 0 ? ($item->success_count / $item->total_count) * 100 : 0,
                    'success_amount' => $item->success_amount,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'provider_stats' => $providerStats,
        ]);
    }

    /**
     * Export payment data
     */
    public function exportPayments(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = MobileMoneyPayment::with(['voucherPlan', 'voucher']);

        // Apply date filters
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->get();

        $csvData = [];
        $csvData[] = [
            'Transaction ID',
            'Phone Number',
            'Amount',
            'Provider',
            'Status',
            'Plan',
            'Voucher Code',
            'Created At',
            'Paid At',
        ];

        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->transaction_id,
                $payment->phone_number,
                $payment->amount,
                $payment->provider_display_name,
                $payment->status,
                $payment->voucherPlan->name,
                $payment->voucher?->code ?? '',
                $payment->created_at->format('Y-m-d H:i:s'),
                $payment->paid_at?->format('Y-m-d H:i:s') ?? '',
            ];
        }

        $filename = 'mobile_money_payments_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Initiate cash payment
     */
    private function initiateCashPayment($plan, $phoneNumber, $customerName = null)
    {
        // Create pending cash payment record
        $payment = MobileMoneyPayment::create([
            'transaction_id' => 'CASH_' . time() . '_' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'voucher_plan_id' => $plan->id,
            'phone_number' => $phoneNumber,
            'amount' => $plan->price,
            'commission' => $plan->price * (config('mobile_money.service_fee', 3.0) / 100),
            'provider' => 'cash',
            'payment_method' => 'cash',
            'status' => 'pending_cash',
        ]);

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'amount' => $plan->price,
            'message' => 'Cash payment initiated. Please visit our location to complete payment.',
            'payment_type' => 'cash',
            'instructions' => 'Please bring this transaction ID: ' . $payment->transaction_id . ' when visiting our location.',
        ]);
    }

    /**
     * Complete cash payment (for admin/agent use)
     */
    public function completeCashPayment(Request $request, $paymentId)
    {
        $validator = Validator::make($request->all(), [
            'amount_received' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payment = MobileMoneyPayment::where('id', $paymentId)
            ->where('payment_method', 'cash')
            ->where('status', 'pending_cash')
            ->firstOrFail();

        if ($request->amount_received < $payment->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Amount received is less than required payment amount.',
            ], 400);
        }

        try {
            // Mark payment as received
            $payment->markAsCashReceived(
                auth()->id(),
                $request->amount_received,
                $request->notes
            );

            // Generate voucher
            $voucher = $this->mobileMoneyService->generateVoucher($payment);
            
            if ($voucher) {
                $payment->update(['voucher_id' => $voucher->id]);
                
                // Send SMS notification
                $this->mobileMoneyService->sendVoucherSms($payment, $voucher);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cash payment completed successfully',
                'voucher_code' => $voucher?->code,
                'change_given' => $payment->change_given,
            ]);

        } catch (\Exception $e) {
            Log::error('Cash payment completion error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to complete cash payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Show cash payments management page
     */
    public function showCashPayments(Request $request)
    {
        $query = MobileMoneyPayment::where('payment_method', 'cash')
            ->with(['voucherPlan', 'voucher', 'cashReceiver'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);

        $stats = [
            'pending' => MobileMoneyPayment::where('payment_method', 'cash')->where('status', 'pending_cash')->count(),
            'completed' => MobileMoneyPayment::where('payment_method', 'cash')->where('status', 'success')->count(),
            'total_amount' => MobileMoneyPayment::where('payment_method', 'cash')->where('status', 'success')->sum('amount'),
        ];

        return view('mobile-money.cash-payments', compact('payments', 'stats'));
    }
}
