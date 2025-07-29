<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\VoucherPlan;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display SMS sending form
     */
    public function index()
    {
        return view('sms.index');
    }

    /**
     * Send SMS
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
            'message' => 'required|string|max:1000',
            'provider' => 'required|in:jambopay,twilio,africas_talking',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $result = $this->smsService->sendSms(
                $request->phone_number,
                $request->message,
                $request->provider
            );

            if ($result['success']) {
                return back()->with('success', 'SMS sent successfully!');
            } else {
                return back()->with('error', 'Failed to send SMS: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending SMS: ' . $e->getMessage());
        }
    }

    /**
     * Display SMS logs
     */
    public function logs(Request $request)
    {
        $query = SmsLog::with(['voucher.voucherPlan']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('provider') && $request->provider !== '') {
            $query->where('provider', $request->provider);
        }

        if ($request->has('phone_number') && $request->phone_number !== '') {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $smsLogs = $query->latest()->paginate(20);
        
        // Calculate statistics
        $stats = [
            'total_sms' => SmsLog::count(),
            'sent_sms' => SmsLog::where('status', 'sent')->count(),
            'failed_sms' => SmsLog::where('status', 'failed')->count(),
            'total_cost' => SmsLog::where('status', 'sent')->sum('cost'),
        ];

        return view('sms.logs', compact('smsLogs', 'stats'));
    }

    /**
     * Resend SMS for a voucher
     */
    public function resendVoucherSms(Request $request, $voucherId)
    {
        $voucher = \App\Models\Voucher::findOrFail($voucherId);
        
        if (!$voucher->phone_number) {
            return response()->json([
                'success' => false,
                'message' => 'No phone number associated with this voucher'
            ], 400);
        }

        try {
            $message = "Your WiFi voucher: Code: {$voucher->code}, Username: {$voucher->username}, Password: {$voucher->password}. Valid for {$voucher->voucherPlan->duration_hours} hours.";
            
            $result = $this->smsService->sendSms(
                $voucher->phone_number,
                $message,
                'jambopay', // Default provider
                $voucher->id
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ], 500);
        }
    }
}
