<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\MobileMoneyPayment;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $provider = '';
    public $status = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function with()
    {
        $query = MobileMoneyPayment::query()
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->provider, function ($query) {
                $query->where('provider', $this->provider);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            });

        $payments = $query->latest()->paginate(20);

        // Calculate totals
        $totalRevenue = $query->where('status', 'completed')->sum('amount');
        $totalTransactions = $query->count();
        $successfulTransactions = $query->where('status', 'completed')->count();
        $failedTransactions = $query->where('status', 'failed')->count();

        // Revenue by day
        $revenueByDay = MobileMoneyPayment::whereBetween('created_at', [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
            ])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by provider
        $revenueByProvider = MobileMoneyPayment::whereBetween('created_at', [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
            ])
            ->where('status', 'completed')
            ->selectRaw('provider, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('provider')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'successfulTransactions' => $successfulTransactions,
            'failedTransactions' => $failedTransactions,
            'successRate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 1) : 0,
            'revenueByDay' => $revenueByDay,
            'revenueByProvider' => $revenueByProvider,
            'providers' => MobileMoneyPayment::distinct()->pluck('provider'),
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

    public function updatingProvider()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->provider = '';
        $this->status = '';
        $this->resetPage();
    }

    public function exportReport()
    {
        session()->flash('info', 'Revenue report export functionality will be implemented here.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Revenue Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-2xl font-bold text-green-600">UGX {{ number_format($totalRevenue) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Transactions</dt>
                            <dd class="text-2xl font-bold text-blue-600">{{ number_format($totalTransactions) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Successful</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ number_format($successfulTransactions) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Failed</dt>
                            <dd class="text-2xl font-bold text-red-600">{{ number_format($failedTransactions) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Success Rate</dt>
                            <dd class="text-2xl font-bold text-purple-600">{{ $successRate }}%</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Revenue by Day -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Daily Revenue</h3>
                        <div class="space-y-2">
                            @forelse($revenueByDay as $day)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium">{{ Carbon::parse($day->date)->format('M d, Y') }}</span>
                                        <div class="text-sm text-gray-500">{{ $day->count }} transactions</div>
                                    </div>
                                    <span class="text-green-600 font-bold">UGX {{ number_format($day->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No revenue data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Revenue by Provider -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Revenue by Provider</h3>
                        <div class="space-y-2">
                            @forelse($revenueByProvider as $provider)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                    <div>
                                        <span class="font-medium capitalize">{{ $provider->provider }}</span>
                                        <div class="text-sm text-gray-500">{{ $provider->count }} transactions</div>
                                    </div>
                                    <span class="text-blue-600 font-bold">UGX {{ number_format($provider->total) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No provider data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Transaction Details</h3>
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
                            <label class="block text-sm font-medium text-gray-700">Provider</label>
                            <select wire:model.live="provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Providers</option>
                                @foreach($providers as $prov)
                                    <option value="{{ $prov }}">{{ ucfirst($prov) }}</option>
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

                    <!-- Transactions Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-mono text-sm">{{ $payment->transaction_id ?: 'N/A' }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ $payment->phone_number }}
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($payment->provider) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-semibold">UGX {{ number_format($payment->amount) }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($payment->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($payment->status === 'failed')
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
                                            <div class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                            No transactions found for the selected criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
