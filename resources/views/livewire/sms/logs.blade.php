<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\SmsLog;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function with()
    {
        $query = SmsLog::query()
            ->when($this->search, function ($query) {
                $query->where('phone_number', 'like', '%' . $this->search . '%')
                      ->orWhere('message', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        return [
            'smsLogs' => $query->latest()->paginate(20),
            'stats' => [
                'total' => SmsLog::count(),
                'sent' => SmsLog::where('status', 'sent')->count(),
                'failed' => SmsLog::where('status', 'failed')->count(),
                'pending' => SmsLog::where('status', 'pending')->count(),
                'today' => SmsLog::whereDate('created_at', today())->count(),
                'this_month' => SmsLog::whereMonth('created_at', now()->month)->count(),
            ],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function exportLogs()
    {
        // This would typically generate and download a CSV/Excel file
        session()->flash('info', 'Export functionality will be implemented here.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SMS Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Overview -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-green-500 truncate">Sent</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ number_format($stats['sent']) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-red-500 truncate">Failed</dt>
                            <dd class="text-2xl font-bold text-red-600">{{ number_format($stats['failed']) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-yellow-500 truncate">Pending</dt>
                            <dd class="text-2xl font-bold text-yellow-600">{{ number_format($stats['pending']) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-blue-500 truncate">Today</dt>
                            <dd class="text-2xl font-bold text-blue-600">{{ number_format($stats['today']) }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <dt class="text-sm font-medium text-purple-500 truncate">This Month</dt>
                            <dd class="text-2xl font-bold text-purple-600">{{ number_format($stats['this_month']) }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">SMS Logs History</h3>
                        <div class="flex space-x-2">
                            <button wire:click="exportLogs" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Export
                            </button>
                            <a href="{{ route('sms.send') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Send SMS
                            </a>
                        </div>
                    </div>

                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                            {{ session('info') }}
                        </div>
                    @endif

                    <!-- Advanced Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                        <div>
                            <input 
                                type="text" 
                                wire:model.live="search" 
                                placeholder="Search phone/message..." 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <select 
                                wire:model.live="statusFilter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Status</option>
                                <option value="sent">Sent</option>
                                <option value="failed">Failed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div>
                            <select 
                                wire:model.live="typeFilter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">All Types</option>
                                <option value="general">General</option>
                                <option value="voucher">Voucher</option>
                                <option value="password_reset">Password Reset</option>
                                <option value="notification">Notification</option>
                            </select>
                        </div>
                        <div>
                            <input 
                                type="date" 
                                wire:model.live="dateFrom" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="From Date"
                            >
                        </div>
                        <div>
                            <input 
                                type="date" 
                                wire:model.live="dateTo" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="To Date"
                            >
                        </div>
                        <div>
                            <button 
                                wire:click="clearFilters" 
                                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($smsLogs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b">
                                            <div class="font-medium text-gray-900">{{ $log->phone_number }}</div>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <div class="text-sm text-gray-900 max-w-xs">
                                                <div class="truncate" title="{{ $log->message }}">
                                                    {{ Str::limit($log->message, 50) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($log->type === 'voucher') bg-blue-100 text-blue-800
                                                @elseif($log->type === 'password_reset') bg-yellow-100 text-yellow-800
                                                @elseif($log->type === 'notification') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($log->type ?? 'general') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($log->status === 'sent')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Sent
                                                </span>
                                            @elseif($log->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Failed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($log->response)
                                                <div class="text-sm text-gray-600 max-w-xs">
                                                    <div class="truncate" title="{{ $log->response }}">
                                                        {{ Str::limit($log->response, 30) }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <div class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A10.003 10.003 0 0124 26c4.21 0 7.813 2.602 9.288 6.286M30 14a6 6 0 11-12 0 6 6 0 0112 0zm12 6a4 4 0 11-8 0 4 4 0 018 0zm-28 0a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No SMS logs</h3>
                                                <p class="mt-1 text-sm text-gray-500">Get started by sending your first SMS message.</p>
                                                <div class="mt-6">
                                                    <a href="{{ route('sms.send') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                        Send SMS
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $smsLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
