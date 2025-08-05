<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\MobileMoneyPayment;
use App\Models\User;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $agent = '';
    public $commissionRate = 5; // Default 5% commission

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function with()
    {
        // Get agent users
        $agents = User::role('agent')->get();
        
        // Calculate commissions (this is a simplified example)
        $commissions = [];
        
        foreach ($agents as $agent) {
            // In a real scenario, you'd have a proper commission tracking system
            $agentSales = MobileMoneyPayment::whereBetween('created_at', [
                    $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                    $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
                ])
                ->where('status', 'completed')
                ->when($this->agent, function ($query) {
                    $query->where('created_by', $this->agent); // Assuming you track who created the transaction
                })
                ->sum('amount');

            $commissionAmount = ($agentSales * $this->commissionRate) / 100;

            if ($agentSales > 0 || !$this->agent) {
                $commissions[] = (object) [
                    'agent_id' => $agent->id,
                    'agent_name' => $agent->name,
                    'agent_phone' => $agent->phone,
                    'total_sales' => $agentSales,
                    'commission_rate' => $this->commissionRate,
                    'commission_amount' => $commissionAmount,
                    'transaction_count' => MobileMoneyPayment::whereBetween('created_at', [
                            $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                            $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay(),
                        ])
                        ->where('status', 'completed')
                        ->count()
                ];
            }
        }

        // Sort by commission amount
        usort($commissions, function($a, $b) {
            return $b->commission_amount <=> $a->commission_amount;
        });

        $totalCommissions = array_sum(array_column($commissions, 'commission_amount'));
        $totalSales = array_sum(array_column($commissions, 'total_sales'));

        return [
            'commissions' => collect($commissions),
            'agents' => $agents,
            'totalCommissions' => $totalCommissions,
            'totalSales' => $totalSales,
            'averageCommissionRate' => $this->commissionRate,
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

    public function updatingAgent()
    {
        $this->resetPage();
    }

    public function updatingCommissionRate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->agent = '';
        $this->commissionRate = 5;
        $this->resetPage();
    }

    public function exportReport()
    {
        session()->flash('info', 'Commission report export functionality will be implemented here.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Commission Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Commissions</dt>
                            <dd class="text-2xl font-bold text-green-600">UGX {{ number_format($totalCommissions) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                            <dd class="text-2xl font-bold text-blue-600">UGX {{ number_format($totalSales) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Agents</dt>
                            <dd class="text-2xl font-bold text-purple-600">{{ $agents->count() }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Commission Rate</dt>
                            <dd class="text-2xl font-bold text-orange-600">{{ $averageCommissionRate }}%</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Settings & Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Report Settings</h3>
                    
                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                            <label class="block text-sm font-medium text-gray-700">Specific Agent</label>
                            <select wire:model.live="agent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Agents</option>
                                @foreach($agents as $agentOption)
                                    <option value="{{ $agentOption->id }}">{{ $agentOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
                            <input 
                                type="number" 
                                wire:model.live="commissionRate" 
                                min="0" 
                                max="100" 
                                step="0.1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                        <div class="flex items-end">
                            <button wire:click="clearFilters" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Agent Commission Details</h3>
                        <button wire:click="exportReport" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export Report
                        </button>
                    </div>

                    <!-- Commission Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Rate</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Amount</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($commissions as $commission)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b">
                                            <div>
                                                <span class="font-medium text-gray-900">{{ $commission->agent_name }}</span>
                                                <div class="text-sm text-gray-500">ID: {{ $commission->agent_id }}</div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ $commission->agent_phone ?: 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-semibold">UGX {{ number_format($commission->total_sales) }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            {{ number_format($commission->transaction_count) }}
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $commission->commission_rate }}%
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-bold text-green-600">UGX {{ number_format($commission->commission_amount) }}</span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($commission->commission_amount > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    No Sales
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No commission data</h3>
                                                <p class="mt-1 text-sm text-gray-500">No agents or sales found for the selected period.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Footer -->
                    @if($commissions->count() > 0)
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                <div>
                                    <span class="text-sm text-gray-500">Total Agents with Sales</span>
                                    <div class="text-lg font-semibold">{{ $commissions->where('commission_amount', '>', 0)->count() }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Average Commission per Agent</span>
                                    <div class="text-lg font-semibold">UGX {{ $commissions->count() > 0 ? number_format($totalCommissions / $commissions->count()) : 0 }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Commission as % of Sales</span>
                                    <div class="text-lg font-semibold">{{ $totalSales > 0 ? round(($totalCommissions / $totalSales) * 100, 2) : 0 }}%</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Note -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Note</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>This is a basic commission calculation based on total sales. In a production environment, you would implement a proper commission tracking system with detailed transaction attribution to agents.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
