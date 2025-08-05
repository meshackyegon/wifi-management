<?php

use Livewire\Volt\Component;
use App\Models\MobileMoneyPayment;
use App\Models\User;
use App\Models\SmsLog;
use Carbon\Carbon;

new class extends Component {
    public $period = 'this_month';
    
    public function with()
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        return [
            'totalRevenue' => $this->getTotalRevenue($startDate, $endDate),
            'totalPayments' => $this->getTotalPayments($startDate, $endDate),
            'totalUsers' => User::count(),
            'totalSmsCount' => SmsLog::whereBetween('created_at', [$startDate, $endDate])->count(),
            'revenueByProvider' => $this->getRevenueByProvider($startDate, $endDate),
            'paymentsOverTime' => $this->getPaymentsOverTime($startDate, $endDate),
            'topPaymentMethods' => $this->getTopPaymentMethods($startDate, $endDate),
        ];
    }

    private function getStartDate()
    {
        switch ($this->period) {
            case 'today':
                return now()->startOfDay();
            case 'this_week':
                return now()->startOfWeek();
            case 'this_month':
                return now()->startOfMonth();
            case 'last_month':
                return now()->subMonth()->startOfMonth();
            case 'this_year':
                return now()->startOfYear();
            default:
                return now()->startOfMonth();
        }
    }

    private function getTotalRevenue($startDate, $endDate)
    {
        return MobileMoneyPayment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');
    }

    private function getTotalPayments($startDate, $endDate)
    {
        return MobileMoneyPayment::whereBetween('created_at', [$startDate, $endDate])->count();
    }

    private function getRevenueByProvider($startDate, $endDate)
    {
        return MobileMoneyPayment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('provider, SUM(amount) as total')
            ->groupBy('provider')
            ->orderBy('total', 'desc')
            ->get();
    }

    private function getPaymentsOverTime($startDate, $endDate)
    {
        return MobileMoneyPayment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopPaymentMethods($startDate, $endDate)
    {
        return MobileMoneyPayment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('provider, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('provider')
            ->orderBy('count', 'desc')
            ->get();
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Period Filter -->
            <div class="mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">Report Period</h3>
                            <select wire:model.live="period" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="today">Today</option>
                                <option value="this_week">This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                                    <dd class="text-lg font-medium text-gray-900">UGX {{ number_format($totalRevenue) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalPayments) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalUsers) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">SMS Sent</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalSmsCount) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue by Provider -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Revenue by Provider</h3>
                        <div class="space-y-3">
                            @forelse($revenueByProvider as $provider)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <span class="font-medium capitalize">{{ $provider->provider }}</span>
                                    <span class="text-green-600 font-bold">UGX {{ number_format($provider->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No revenue data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Payment Methods</h3>
                        <div class="space-y-3">
                            @forelse($topPaymentMethods as $method)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium capitalize">{{ $method->provider }}</span>
                                        <div class="text-sm text-gray-500">{{ $method->count }} transactions</div>
                                    </div>
                                    <span class="text-blue-600 font-bold">UGX {{ number_format($method->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No payment data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('reports.revenue') }}" class="block p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-blue-900">Revenue Report</h4>
                                    <p class="text-sm text-blue-700">Detailed revenue analysis</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('reports.vouchers') }}" class="block p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 11-1-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-green-900">Voucher Report</h4>
                                    <p class="text-sm text-green-700">Voucher sales and usage</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('reports.commissions') }}" class="block p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-purple-900">Commission Report</h4>
                                    <p class="text-sm text-purple-700">Agent commissions</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
