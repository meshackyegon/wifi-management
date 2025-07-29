<?php

use Livewire\Volt\Component;
use App\Models\VoucherPlan;

new class extends Component {
    public $name = '';
    public $description = '';
    public $price = '';
    public $duration_hours = '';
    public $data_limit_mb = '';
    public $speed_limit_kbps = '';
    public $is_active = true;
    public $is_popular = false;

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
            'is_popular' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        VoucherPlan::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_hours' => $this->duration_hours,
            'data_limit_mb' => $this->data_limit_mb ?: null,
            'speed_limit_kbps' => $this->speed_limit_kbps ?: null,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
        ]);

        session()->flash('success', 'Voucher plan created successfully.');
        
        return redirect()->route('voucher-plans.index');
    }

    public function loadPreset($preset)
    {
        switch($preset) {
            case 'basic':
                $this->name = 'Basic Plan';
                $this->description = 'Perfect for light browsing and social media';
                $this->price = 50;
                $this->duration_hours = 24;
                $this->data_limit_mb = 1024; // 1GB
                $this->speed_limit_kbps = 2048; // 2Mbps
                break;
            case 'standard':
                $this->name = 'Standard Plan';
                $this->description = 'Great for streaming and downloads';
                $this->price = 100;
                $this->duration_hours = 24;
                $this->data_limit_mb = 5120; // 5GB
                $this->speed_limit_kbps = 5120; // 5Mbps
                break;
            case 'premium':
                $this->name = 'Premium Plan';
                $this->description = 'Unlimited high-speed internet access';
                $this->price = 200;
                $this->duration_hours = 24;
                $this->data_limit_mb = null; // Unlimited
                $this->speed_limit_kbps = null; // No limit
                $this->is_popular = true;
                break;
        }
    }
}; ?>

<div>
    <div class="container-fluid px-4 py-4">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-success text-white shadow-lg border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h2 mb-2 text-white">✨ Create New Voucher Plan</h1>
                                <p class="mb-0 text-white-75">Design flexible WiFi plans that meet your customers' needs</p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Plans
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Presets -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-gradient-info text-white border-0">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-magic me-2"></i>Quick Presets
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Start with a preset and customize as needed</p>
                        <div class="d-grid gap-2">
                            <button wire:click="loadPreset('basic')" class="btn btn-outline-primary">
                                <i class="fas fa-signal me-2"></i>Basic Plan (1GB, 24h)
                            </button>
                            <button wire:click="loadPreset('standard')" class="btn btn-outline-warning">
                                <i class="fas fa-wifi me-2"></i>Standard Plan (5GB, 24h)
                            </button>
                            <button wire:click="loadPreset('premium')" class="btn btn-outline-success">
                                <i class="fas fa-crown me-2"></i>Premium Plan (Unlimited)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Plan Preview -->
                <div class="card shadow border-0 mt-4">
                    <div class="card-header bg-gradient-secondary text-white border-0">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-eye me-2"></i>Plan Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h5 class="text-primary">{{ $name ?: 'Plan Name' }}</h5>
                            <div class="display-6 fw-bold text-success">
                                KES {{ $price ? number_format($price) : '0' }}
                            </div>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                {{ $duration_hours ?: '0' }} hours
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-database text-info me-2"></i>
                                @if($data_limit_mb)
                                    {{ $data_limit_mb >= 1024 ? round($data_limit_mb / 1024, 1) . ' GB' : $data_limit_mb . ' MB' }}
                                @else
                                    Unlimited Data
                                @endif
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                @if($speed_limit_kbps)
                                    {{ $speed_limit_kbps >= 1024 ? round($speed_limit_kbps / 1024, 1) . ' Mbps' : $speed_limit_kbps . ' Kbps' }}
                                @else
                                    No Speed Limit
                                @endif
                            </li>
                        </ul>
                        @if($description)
                            <p class="text-muted small mt-2">{{ $description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="card-icon me-3">
                                <div class="bg-primary bg-gradient rounded-circle p-3">
                                    <i class="fas fa-cog text-white fa-lg"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">Plan Configuration</h5>
                                <p class="text-muted mb-0">Set up your voucher plan details</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form wire:submit="save">
                            <div class="row">
                                <!-- Basic Info -->
                                <div class="col-12 mb-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="fas fa-tag text-primary me-1"></i>Plan Name *
                                    </label>
                                    <input type="text" wire:model.live="name" 
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           id="name" placeholder="e.g., Premium 24H Plan">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label fw-semibold">
                                        <i class="fas fa-dollar-sign text-primary me-1"></i>Price (KES) *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">KES</span>
                                        <input type="number" wire:model.live="price" step="0.01" min="0"
                                               class="form-control form-control-lg @error('price') is-invalid @enderror" 
                                               id="price" placeholder="100.00">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="fas fa-align-left text-primary me-1"></i>Description
                                    </label>
                                    <textarea wire:model.live="description" rows="3"
                                              class="form-control @error('description') is-invalid @enderror" 
                                              id="description" placeholder="Brief description of the plan benefits"></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Technical Specs -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-sliders-h me-2"></i>Technical Specifications
                                    </h6>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="duration_hours" class="form-label fw-semibold">
                                        <i class="fas fa-clock text-primary me-1"></i>Duration (Hours) *
                                    </label>
                                    <input type="number" wire:model.live="duration_hours" min="1"
                                           class="form-control form-control-lg @error('duration_hours') is-invalid @enderror" 
                                           id="duration_hours" placeholder="24">
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="data_limit_mb" class="form-label fw-semibold">
                                        <i class="fas fa-database text-primary me-1"></i>Data Limit (MB)
                                    </label>
                                    <input type="number" wire:model.live="data_limit_mb" min="1"
                                           class="form-control form-control-lg @error('data_limit_mb') is-invalid @enderror" 
                                           id="data_limit_mb" placeholder="1024 (Leave empty for unlimited)">
                                    <div class="form-text">Leave empty for unlimited data</div>
                                    @error('data_limit_mb')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="speed_limit_kbps" class="form-label fw-semibold">
                                        <i class="fas fa-tachometer-alt text-primary me-1"></i>Speed Limit (Kbps)
                                    </label>
                                    <input type="number" wire:model.live="speed_limit_kbps" min="1"
                                           class="form-control form-control-lg @error('speed_limit_kbps') is-invalid @enderror" 
                                           id="speed_limit_kbps" placeholder="2048 (Leave empty for no limit)">
                                    <div class="form-text">Leave empty for no speed limit</div>
                                    @error('speed_limit_kbps')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Settings -->
                                <div class="col-12 mb-4 mt-4">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-cogs me-2"></i>Plan Settings
                                    </h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input wire:model.live="is_active" class="form-check-input" type="checkbox" id="is_active">
                                        <label class="form-check-label fw-semibold" for="is_active">
                                            <i class="fas fa-toggle-on text-success me-1"></i>
                                            Plan is Active
                                        </label>
                                        <div class="form-text">Active plans are available for purchase</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input wire:model.live="is_popular" class="form-check-input" type="checkbox" id="is_popular">
                                        <label class="form-check-label fw-semibold" for="is_popular">
                                            <i class="fas fa-star text-warning me-1"></i>
                                            Mark as Popular
                                        </label>
                                        <div class="form-text">Popular plans are highlighted to customers</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4 pt-4 border-top">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('voucher-plans.index') }}" class="btn btn-outline-secondary btn-lg">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-save me-2"></i>Create Plan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
.bg-gradient-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}
</style>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Voucher Plan') }}
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
                            <a href="{{ route('voucher-plans.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
