<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherPlan;
use App\Models\Router;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Display vouchers listing
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Voucher::with(['voucherPlan', 'user', 'router']);

        // Filter by user type
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('plan_id') && $request->plan_id !== '') {
            $query->where('voucher_plan_id', $request->plan_id);
        }

        if ($request->has('router_id') && $request->router_id !== '') {
            $query->where('router_id', $request->router_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $vouchers = $query->latest()->paginate(20);

        // Get filter options
        $plans = VoucherPlan::active()->get();
        $routers = Router::active()->get();

        return view('vouchers.index', compact('vouchers', 'plans', 'routers'));
    }

    /**
     * Show voucher creation form
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && !$user->isAgent()) {
            abort(403, 'Unauthorized');
        }

        $plans = VoucherPlan::active()->get();
        $routers = Router::active()->get();

        return view('vouchers.create', compact('plans', 'routers'));
    }

    /**
     * Generate vouchers
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && !$user->isAgent()) {
            abort(403, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'voucher_plan_id' => 'required|exists:voucher_plans,id',
            'quantity' => 'required|integer|min:1|max:1000',
            'router_id' => 'nullable|exists:routers,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $plan = VoucherPlan::findOrFail($request->voucher_plan_id);
            $router = $request->router_id ? Router::findOrFail($request->router_id) : null;

            $vouchers = $this->voucherService->generateVouchers(
                $plan,
                $request->quantity,
                $user,
                $router
            );

            return redirect()->route('vouchers.index')->with('success', 
                "Successfully generated {$request->quantity} voucher(s)");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate vouchers: ' . $e->getMessage());
        }
    }

    /**
     * Show voucher details
     */
    public function show(Voucher $voucher)
    {
        $user = Auth::user();
        
        // Check access permissions
        if (!$user->isAdmin() && $voucher->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $voucher->load(['voucherPlan', 'user', 'router', 'mobileMoneyPayments', 'smsLogs']);

        return view('vouchers.show', compact('voucher'));
    }

    /**
     * Print vouchers
     */
    public function print(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_ids' => 'required|array',
            'voucher_ids.*' => 'exists:vouchers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid voucher selection',
            ], 422);
        }

        $user = Auth::user();
        $voucherIds = $request->voucher_ids;

        // Get vouchers with access check
        $query = Voucher::whereIn('id', $voucherIds)->with(['voucherPlan', 'router']);
        
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $vouchers = $query->get();

        if ($vouchers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No vouchers found or access denied',
            ], 404);
        }

        // Mark vouchers as printed
        $this->voucherService->markVouchersAsPrinted($vouchers->pluck('id')->toArray());

        // Generate print view
        return view('vouchers.print', compact('vouchers'));
    }

    /**
     * Bulk generate vouchers
     */
    public function bulkGenerate(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $file = $request->file('csv_file');
            $data = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($data);

            // Expected CSV format: plan_id, quantity, router_id (optional)
            $bulkData = [];
            foreach ($data as $row) {
                if (count($row) >= 2) {
                    $bulkData[] = [
                        'plan_id' => $row[0],
                        'quantity' => (int) $row[1],
                        'router_id' => isset($row[2]) && !empty($row[2]) ? $row[2] : null,
                    ];
                }
            }

            $vouchers = $this->voucherService->generateBulkVouchers($bulkData, $user);

            return back()->with('success', 
                'Successfully generated ' . count($vouchers) . ' vouchers from CSV');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process CSV: ' . $e->getMessage());
        }
    }

    /**
     * Export vouchers
     */
    public function export(Request $request, string $format = 'csv')
    {
        $user = Auth::user();
        
        $query = Voucher::with(['voucherPlan', 'user', 'router']);

        // Filter by user type
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('plan_id') && $request->plan_id !== '') {
            $query->where('voucher_plan_id', $request->plan_id);
        }

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $vouchers = $query->get();

        if ($format === 'csv') {
            return $this->exportCsv($vouchers);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($vouchers);
        }

        abort(404, 'Export format not supported');
    }

    /**
     * Export vouchers as CSV
     */
    protected function exportCsv($vouchers)
    {
        $csvData = [];
        $csvData[] = [
            'Code',
            'Password',
            'Plan',
            'Price',
            'Status',
            'Router',
            'Created At',
            'Used At',
            'Expires At',
            'Generated By',
        ];

        foreach ($vouchers as $voucher) {
            $csvData[] = [
                $voucher->code,
                $voucher->password,
                $voucher->voucherPlan->name,
                $voucher->price,
                $voucher->status,
                $voucher->router?->name ?? '',
                $voucher->created_at->format('Y-m-d H:i:s'),
                $voucher->used_at?->format('Y-m-d H:i:s') ?? '',
                $voucher->expires_at?->format('Y-m-d H:i:s') ?? '',
                $voucher->user?->name ?? '',
            ];
        }

        $filename = 'vouchers_' . date('Y-m-d_H-i-s') . '.csv';

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

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export vouchers as PDF
     */
    protected function exportPdf($vouchers)
    {
        // This would require a PDF library like DomPDF or wkhtmltopdf
        // For now, we'll return a view that can be printed as PDF
        return view('vouchers.export-pdf', compact('vouchers'));
    }

    /**
     * Check voucher status (public API)
     */
    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher code is required',
            ], 422);
        }

        try {
            $result = $this->voucherService->checkVoucherStatus($request->code);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check voucher status',
            ], 500);
        }
    }

    /**
     * Use voucher (public API)
     */
    public function useVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'phone' => 'nullable|string',
            'mac_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $voucher = $this->voucherService->useVoucher(
                $request->code,
                $request->phone,
                $request->mac_address
            );

            return response()->json([
                'success' => true,
                'message' => 'Voucher used successfully',
                'voucher' => [
                    'code' => $voucher->code,
                    'plan' => $voucher->voucherPlan->name,
                    'duration' => $voucher->voucherPlan->formatted_duration,
                    'data_limit' => $voucher->voucherPlan->formatted_data_limit,
                    'expires_at' => $voucher->expires_at?->toISOString(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get voucher statistics
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        
        $stats = $this->voucherService->getVoucherStats($user);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark vouchers as printed
     */
    public function markPrinted(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_ids' => 'required|array',
            'voucher_ids.*' => 'exists:vouchers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid voucher selection',
            ], 422);
        }

        try {
            $this->voucherService->markVouchersAsPrinted($request->voucher_ids);
            
            return response()->json([
                'success' => true,
                'message' => 'Vouchers marked as printed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark vouchers as printed',
            ], 500);
        }
    }

    /**
     * Send voucher via SMS
     */
    public function sendSms(Request $request, Voucher $voucher)
    {
        $user = Auth::user();
        
        // Check access permissions
        if (!$user->isAdmin() && $voucher->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+[1-9]\d{1,14}$/',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $smsService = app(\App\Services\SmsService::class);
            $result = $smsService->sendVoucherSMS($request->phone_number, $voucher);

            if ($result['success']) {
                return back()->with('success', 'SMS sent successfully to ' . $request->phone_number);
            } else {
                return back()->with('error', 'Failed to send SMS: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }
}
