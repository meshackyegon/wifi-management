<?php

use Livewire\Volt\Component;
use App\Models\VoucherPlan;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'plans' => VoucherPlan::orderBy('price')->paginate(10),
        ];
    }

    public function delete($id)
    {
        $plan = VoucherPlan::findOrFail($id);
        $plan->delete();
        
        session()->flash('success', 'Voucher plan deleted successfully.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Voucher Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Manage Voucher Plans</h3>
                        <a href="{{ route('voucher-plans.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Plan
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Name</th>
                                    <th class="py-2 px-4 border-b text-left">Price (KES)</th>
                                    <th class="py-2 px-4 border-b text-left">Duration</th>
                                    <th class="py-2 px-4 border-b text-left">Data Limit</th>
                                    <th class="py-2 px-4 border-b text-left">Speed Limit</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $plan->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ number_format($plan->price, 2) }}</td>
                                        <td class="py-2 px-4 border-b">{{ $plan->duration_hours }} hours</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($plan->data_limit_mb)
                                                {{ $plan->data_limit_mb }} MB
                                            @else
                                                Unlimited
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($plan->speed_limit_kbps)
                                                {{ $plan->speed_limit_kbps }} Kbps
                                            @else
                                                No Limit
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('voucher.buy.plan', $plan) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm no-underline">
                                                    Select Plan
                                                </a>
                                                <a href="{{ route('voucher-plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                <button wire:click="delete({{ $plan->id }})" wire:confirm="Are you sure you want to delete this plan?" class="text-red-600 hover:text-red-900">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                            No voucher plans found. <a href="{{ route('voucher-plans.create') }}" class="text-blue-600">Create one now</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $plans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
