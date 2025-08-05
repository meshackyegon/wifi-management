<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\MobileMoneyPayment;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $status = '';
    public $plan = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function with()
    {
        $query = MobileMoneyPayment::with('voucherPlan')
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->plan, function ($query) {
                $query->where(function($q) {
                    $q->where('voucher_plan', $this->plan)
                      ->orWhereHas('voucherPlan', function($planQuery) {
                          $planQuery->where('name', $this->plan);
                      });
                });
            });

        $vouchers = $query->latest()->paginate(20);

        // Calculate statistics
        $totalVouchers = $query->count();
        $completedVouchers = $query->where('status', 'completed')->count();
        $pendingVouchers = $query->where('status', 'pending')->count();
        $failedVouchers = $query->where('status', 'failed')->count();
        $totalValue = $query->where('status', 'completed')->sum('amount');

        // Vouchers by plan
        $vouchersByPlan = MobileMoneyPayment::with('voucherPlan')
            ->whereBetween('created_at', [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
            ])
            ->where('status', 'completed')
            ->get()
            ->groupBy(function($payment) {
                return $payment->voucherPlan?->name ?? $payment->voucher_plan ?? 'Unknown Plan';
            })
            ->map(function($group) {
                return (object) [
                    'voucher_plan' => $group->first()->voucherPlan?->name ?? $group->first()->voucher_plan ?? 'Unknown Plan',
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            })
            ->sortByDesc('count')
            ->values();

        // Daily voucher sales
        $dailySales = MobileMoneyPayment::whereBetween('created_at', [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
            ])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'vouchers' => $vouchers,
            'totalVouchers' => $totalVouchers,
            'completedVouchers' => $completedVouchers,
            'pendingVouchers' => $pendingVouchers,
            'failedVouchers' => $failedVouchers,
            'totalValue' => $totalValue,
            'completionRate' => $totalVouchers > 0 ? round(($completedVouchers / $totalVouchers) * 100, 1) : 0,
            'vouchersByPlan' => $vouchersByPlan,
            'dailySales' => $dailySales,
            'availablePlans' => MobileMoneyPayment::with('voucherPlan')
                ->whereNotNull('voucher_plan_id')
                ->orWhereNotNull('voucher_plan')
                ->get()
                ->map(function($payment) {
                    return $payment->voucherPlan?->name ?? $payment->voucher_plan ?? null;
                })
                ->filter()
                ->unique()
                ->values(),
        ];
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPlan()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->status = '';
        $this->plan = '';
        $this->resetPage();
    }

    public function exportReport()
    {
        session()->flash('info', 'Voucher report export functionality will be implemented here.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Voucher Sales Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Vouchers</dt>
                            <dd class="text-2xl font-bold text-blue-600">{{ number_format($totalVouchers) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ number_format($completedVouchers) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-2xl font-bold text-yellow-600">{{ number_format($pendingVouchers) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Failed</dt>
                            <dd class="text-2xl font-bold text-red-600">{{ number_format($failedVouchers) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Success Rate</dt>
                            <dd class="text-2xl font-bold text-purple-600">{{ $completionRate }}%</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-gradient-to-r from-green-400 to-blue-500 overflow-hidden shadow rounded-lg mb-6">
                <div class="p-6">
                    <div class="text-center">
                        <dt class="text-lg font-medium text-white">Total Voucher Revenue</dt>
                        <dd class="text-4xl font-bold text-white">UGX {{ number_format($totalValue) }}</dd>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Daily Sales -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Daily Voucher Sales</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse($dailySales as $day)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium">{{ Carbon::parse($day->date)->format('M d, Y') }}</span>
                                        <div class="text-sm text-gray-500">{{ $day->count }} vouchers</div>
                                    </div>
                                    <span class="text-green-600 font-bold">UGX {{ number_format($day->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No sales data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Vouchers by Plan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Sales by Plan</h3>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse($vouchersByPlan as $planData)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium">{{ $planData->voucher_plan ?: 'Unknown Plan' }}</span>
                                        <div class="text-sm text-gray-500">{{ $planData->count }} vouchers</div>
                                    </div>
                                    <span class="text-blue-600 font-bold">UGX {{ number_format($planData->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No plan data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Voucher List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Voucher Transaction Details</h3>
                        <button wire:click="exportReport" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export Report
                        </button>
                    </div>

                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                            {{ session('info') }}
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Date</label>
                            <input 
                                type="date" 
                                wire:model.live="dateFrom" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">To Date</label>
                            <input 
                                type="date" 
                                wire:model.live="dateTo" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plan</label>
                            <select wire:model.live="plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Plans</option>
                                @foreach($availablePlans as $planOption)
                                    <option value="{{ $planOption }}">{{ $planOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model.live="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="clearFilters" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Voucher Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vouchers as $voucher)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-mono text-sm">{{ $voucher->transaction_id ?: 'N/A' }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ $voucher->phone_number }}
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $voucher->voucherPlan?->name ?? $voucher->voucher_plan ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($voucher->provider) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-semibold">UGX {{ number_format($voucher->amount) }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($voucher->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($voucher->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Failed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <div class="text-sm text-gray-900">{{ $voucher->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $voucher->created_at->format('h:i A') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                            No voucher transactions found for the selected criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $vouchers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
