<?php

use Livewire\Volt\Component;
use App\Models\VoucherPlan;

new class extends Component {
    public VoucherPlan $plan;
    public $name = '';
    public $description = '';
    public $price = '';
    public $duration_hours = '';
    public $data_limit_mb = '';
    public $speed_limit_kbps = '';
    public $is_active = true;

    public function mount(VoucherPlan $plan): void
    {
        $this->plan = $plan;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->duration_hours = $plan->duration_hours;
        $this->data_limit_mb = $plan->data_limit_mb;
        $this->speed_limit_kbps = $plan->speed_limit_kbps;
        $this->is_active = $plan->is_active;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'data_limit_mb' => 'nullable|integer|min:1',
            'speed_limit_kbps' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->plan->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_hours' => $this->duration_hours,
            'data_limit_mb' => $this->data_limit_mb ?: null,
            'speed_limit_kbps' => $this->speed_limit_kbps ?: null,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Voucher plan updated successfully.');
        
        return redirect()->route('admin.voucher-plans.index');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Voucher Plan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Plan Name</label>
                                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price (KES)</label>
                                <input type="number" step="0.01" wire:model="price" id="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="duration_hours" class="block text-sm font-medium text-gray-700">Duration (Hours)</label>
                                <input type="number" wire:model="duration_hours" id="duration_hours" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('duration_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="data_limit_mb" class="block text-sm font-medium text-gray-700">Data Limit (MB) - Optional</label>
                                <input type="number" wire:model="data_limit_mb" id="data_limit_mb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Leave empty for unlimited">
                                @error('data_limit_mb') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="speed_limit_kbps" class="block text-sm font-medium text-gray-700">Speed Limit (Kbps) - Optional</label>
                                <input type="number" wire:model="speed_limit_kbps" id="speed_limit_kbps" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Leave empty for no limit">
                                @error('speed_limit_kbps') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea wire:model="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('admin.voucher-plans.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
