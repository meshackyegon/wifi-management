<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherPlan;
use App\Models\MobileMoneyPayment;
use App\Models\Router;
use App\Models\Transaction;
use App\Models\SmsLog;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get statistics based on user type
        $stats = $this->getDashboardStats($user);
        
        // Recent activity
        $recentVouchers = $this->getRecentVouchers($user);
        $recentPayments = $this->getRecentPayments($user);
        $recentTransactions = $this->getRecentTransactions($user);
        
        // Charts data
        $chartsData = $this->getChartsData($user);

        return view('dashboard', compact(
            'stats',
            'recentVouchers',
            'recentPayments',
            'recentTransactions',
            'chartsData'
        ));
    }

    protected function getDashboardStats($user)
    {
        $stats = [];

        if ($user->isAdmin()) {
            // Admin stats - system-wide
            $stats = [
                'total_vouchers' => Voucher::count(),
                'active_vouchers' => Voucher::where('status', 'active')->count(),
                'used_vouchers' => Voucher::where('status', 'used')->count(),
                'total_revenue' => MobileMoneyPayment::where('status', 'success')->sum('amount'),
                'total_commission' => Transaction::where('type', 'commission')->sum('amount'),
                'total_users' => \App\Models\User::count(),
                'active_routers' => Router::where('is_active', true)->count(),
                'sms_sent_today' => SmsLog::whereDate('created_at', today())->count(),
                'revenue_today' => MobileMoneyPayment::where('status', 'success')
                    ->whereDate('created_at', today())->sum('amount'),
                'vouchers_generated_today' => Voucher::whereDate('created_at', today())->count(),
            ];
        } elseif ($user->isAgent()) {
            // Agent stats - their own data
            $stats = [
                'my_vouchers' => Voucher::where('user_id', $user->id)->count(),
                'vouchers_sold' => Voucher::where('user_id', $user->id)->where('status', 'used')->count(),
                'my_commission' => $user->balance,
                'monthly_commission' => Transaction::where('user_id', $user->id)
                    ->where('type', 'commission')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'vouchers_printed' => Voucher::where('user_id', $user->id)->where('is_printed', true)->count(),
                'pending_vouchers' => Voucher::where('user_id', $user->id)->where('status', 'active')->count(),
            ];
        } else {
            // Customer stats - basic info
            $stats = [
                'vouchers_purchased' => MobileMoneyPayment::where('phone_number', $user->phone)
                    ->where('status', 'success')->count(),
                'total_spent' => MobileMoneyPayment::where('phone_number', $user->phone)
                    ->where('status', 'success')->sum('amount'),
                'active_vouchers' => Voucher::whereHas('mobileMoneyPayments', function($query) use ($user) {
                    $query->where('phone_number', $user->phone)->where('status', 'success');
                })->where('status', 'active')->count(),
            ];
        }

        return $stats;
    }

    protected function getRecentVouchers($user)
    {
        $query = Voucher::with(['voucherPlan', 'user', 'router']);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->latest()->take(10)->get();
    }

    protected function getRecentPayments($user)
    {
        $query = MobileMoneyPayment::with(['voucherPlan', 'voucher']);

        if ($user->isCustomer()) {
            $query->where('phone_number', $user->phone);
        }

        return $query->latest()->take(10)->get();
    }

    protected function getRecentTransactions($user)
    {
        $query = Transaction::with(['user', 'transactionable']);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->latest()->take(10)->get();
    }

    protected function getChartsData($user)
    {
        $data = [];

        // Revenue chart (last 30 days)
        $revenueData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = MobileMoneyPayment::where('status', 'success')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            $revenueData[] = [
                'date' => $date->format('M j'),
                'amount' => $revenue,
            ];
        }
        $data['revenue'] = $revenueData;

        // Voucher usage chart (last 7 days)
        $voucherData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $generated = Voucher::whereDate('created_at', $date)->count();
            $used = Voucher::whereDate('used_at', $date)->count();
            
            $voucherData[] = [
                'date' => $date->format('M j'),
                'generated' => $generated,
                'used' => $used,
            ];
        }
        $data['vouchers'] = $voucherData;

        // Provider distribution
        $providerData = MobileMoneyPayment::where('status', 'success')
            ->selectRaw('provider, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('provider')
            ->get()
            ->map(function ($item) {
                return [
                    'provider' => $item->provider_display_name,
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            });
        $data['providers'] = $providerData;

        return $data;
    }
}
