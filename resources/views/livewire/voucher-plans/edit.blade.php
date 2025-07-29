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
    public $is_popular = false;

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
        $this->is_popular = $plan->is_popular ?? false;
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
            'is_popular' => 'boolean',
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
            'is_popular' => $this->is_popular,
        ]);

        session()->flash('success', 'Voucher plan updated successfully.');
        
        return redirect()->route('voucher-plans.index');
    }

    public function delete()
    {
        $this->plan->delete();
        session()->flash('success', 'Voucher plan deleted successfully.');
        return redirect()->route('voucher-plans.index');
    }
}; ?>

<div>
    <div class="container-fluid px-4 py-4">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-warning text-white shadow-lg border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h2 mb-2 text-white">
                                    <i class="fas fa-edit me-2"></i>Edit Voucher Plan
                                </h1>
                                <p class="mb-0 text-white-75">Updating: <strong>{{ $plan->name }}</strong></p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-light btn-lg me-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Plans
                                </a>
                                <button wire:click="delete" onclick="return confirm('Are you sure you want to delete this plan?')" 
                                        class="btn btn-danger btn-lg">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Current Plan Info -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-2"></i>Current Plan Info
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }} mb-2">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </div>
                            @if($plan->is_popular)
                                <div class="badge bg-warning text-dark mb-2 ms-1">
                                    <i class="fas fa-star me-1"></i>Popular
                                </div>
                            @endif
                        </div>
                        <div class="text-center mb-3">
                            <h5 class="text-primary">{{ $plan->name }}</h5>
                            <div class="display-6 fw-bold text-success">
                                KES {{ number_format($plan->price) }}
                            </div>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                {{ $plan->duration_hours }} hours
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-database text-info me-2"></i>
                                @if($plan->data_limit_mb)
                                    {{ $plan->data_limit_mb >= 1024 ? round($plan->data_limit_mb / 1024, 1) . ' GB' : $plan->data_limit_mb . ' MB' }}
                                @else
                                    Unlimited Data
                                @endif
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                @if($plan->speed_limit_kbps)
                                    {{ $plan->speed_limit_kbps >= 1024 ? round($plan->speed_limit_kbps / 1024, 1) . ' Mbps' : $plan->speed_limit_kbps . ' Kbps' }}
                                @else
                                    No Speed Limit
                                @endif
                            </li>
                        </ul>
                        @if($plan->description)
                            <p class="text-muted small mt-2">{{ $plan->description }}</p>
                        @endif
                    </div>
                </div>

                <!-- Plan Statistics -->
                <div class="card shadow border-0 mt-4">
                    <div class="card-header bg-gradient-info text-white border-0">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-chart-bar me-2"></i>Plan Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $plan->vouchers()->count() }}</h4>
                                    <small class="text-muted">Total Vouchers</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $plan->mobileMoneyPayments()->where('status', 'completed')->count() }}</h4>
                                <small class="text-muted">Purchases</small>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="text-center">
                                <h5 class="text-warning mb-1">
                                    KES {{ number_format($plan->mobileMoneyPayments()->where('status', 'completed')->sum('amount')) }}
                                </h5>
                                <small class="text-muted">Total Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="card-icon me-3">
                                <div class="bg-warning bg-gradient rounded-circle p-3">
                                    <i class="fas fa-edit text-white fa-lg"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">Update Plan Configuration</h5>
                                <p class="text-muted mb-0">Modify the voucher plan details</p>
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
                                        <button type="submit" class="btn btn-warning btn-lg px-5">
                                            <i class="fas fa-save me-2"></i>Update Plan
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
.bg-gradient-warning {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
</style>
