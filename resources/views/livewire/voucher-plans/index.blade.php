<?php

use Livewire\Volt\Component;
use App\Models\VoucherPlan;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'plans' => VoucherPlan::orderBy('price')->paginate(12),
        ];
    }

    public function delete($id)
    {
        $plan = VoucherPlan::findOrFail($id);
        $plan->delete();
        
        session()->flash('success', 'Voucher plan deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $plan = VoucherPlan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);
        
        session()->flash('success', 'Plan status updated successfully.');
    }
}; ?>

<div>
    <div class="container-fluid px-4 py-4">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white shadow-lg border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h2 mb-2 text-white">🎫 Voucher Plans Management</h1>
                                <p class="mb-0 text-white-75">Create and manage your WiFi voucher plans with flexible pricing</p>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <a href="{{ route('voucher-plans.create') }}" class="btn btn-light btn-lg shadow">
                                    <i class="fas fa-plus-circle me-2"></i>Create New Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon me-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading mb-1">Success!</h6>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Plans</h6>
                                <h3 class="mb-0 fw-bold">{{ $plans->total() }}</h3>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-layer-group fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-gradient-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Active Plans</h6>
                                <h3 class="mb-0 fw-bold">{{ $plans->where('is_active', true)->count() }}</h3>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Price Range</h6>
                                <h3 class="mb-0 fw-bold">KES {{ number_format($plans->min('price')) }} - {{ number_format($plans->max('price')) }}</h3>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-gradient-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Avg Duration</h6>
                                <h3 class="mb-0 fw-bold">{{ round($plans->avg('duration_hours')) }}h</h3>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plans Grid -->
        @if($plans->count() > 0)
            <div class="row">
                @foreach ($plans as $plan)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow border-0 h-100 {{ $plan->is_active ? 'border-success' : 'border-secondary' }}">
                            <div class="card-header bg-white border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-primary">{{ $plan->name }}</h5>
                                        <div class="d-flex align-items-center">
                                            @if($plan->is_active)
                                                <span class="badge bg-success me-2">
                                                    <i class="fas fa-check me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-secondary me-2">
                                                    <i class="fas fa-pause me-1"></i>Inactive
                                                </span>
                                            @endif
                                            @if($plan->is_popular)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star me-1"></i>Popular
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('voucher-plans.edit', $plan) }}">
                                                <i class="fas fa-edit me-2"></i>Edit Plan
                                            </a></li>
                                            <li><button class="dropdown-item" wire:click="toggleStatus({{ $plan->id }})">
                                                <i class="fas fa-toggle-{{ $plan->is_active ? 'off' : 'on' }} me-2"></i>
                                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                            </button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button class="dropdown-item text-danger" wire:click="delete({{ $plan->id }})" wire:confirm="Are you sure you want to delete this plan?">
                                                <i class="fas fa-trash me-2"></i>Delete Plan
                                            </button></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Price -->
                                <div class="text-center mb-3">
                                    <div class="display-6 fw-bold text-primary">KES {{ number_format($plan->price) }}</div>
                                    <small class="text-muted">One-time payment</small>
                                </div>

                                <!-- Features -->
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        <strong>Duration:</strong> {{ $plan->duration_hours }} hours
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-database text-info me-2"></i>
                                        <strong>Data Limit:</strong> 
                                        @if($plan->data_limit_mb)
                                            {{ $plan->data_limit_mb >= 1024 ? round($plan->data_limit_mb / 1024, 1) . ' GB' : $plan->data_limit_mb . ' MB' }}
                                        @else
                                            <span class="text-success">Unlimited</span>
                                        @endif
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-tachometer-alt text-warning me-2"></i>
                                        <strong>Speed:</strong> 
                                        @if($plan->speed_limit_kbps)
                                            {{ $plan->speed_limit_kbps >= 1024 ? round($plan->speed_limit_kbps / 1024, 1) . ' Mbps' : $plan->speed_limit_kbps . ' Kbps' }}
                                        @else
                                            <span class="text-success">No Limit</span>
                                        @endif
                                    </li>
                                    @if($plan->description)
                                        <li class="mb-2">
                                            <i class="fas fa-info-circle text-secondary me-2"></i>
                                            <small class="text-muted">{{ Str::limit($plan->description, 60) }}</small>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('voucher.buy.plan', $plan) }}" 
                                       class="btn btn-{{ $plan->is_active ? 'primary' : 'secondary' }} btn-lg {{ !$plan->is_active ? 'disabled' : '' }}">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        {{ $plan->is_active ? 'Select This Plan' : 'Plan Inactive' }}
                                    </a>
                                    <div class="row g-2">
                                        <div class="col">
                                            <a href="{{ route('voucher-plans.edit', $plan) }}" class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                        </div>
                                        <div class="col">
                                            <button wire:click="toggleStatus({{ $plan->id }})" class="btn btn-outline-{{ $plan->is_active ? 'warning' : 'success' }} btn-sm w-100">
                                                <i class="fas fa-toggle-{{ $plan->is_active ? 'off' : 'on' }} me-1"></i>
                                                {{ $plan->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $plans->firstItem() }} to {{ $plans->lastItem() }} of {{ $plans->total() }} plans
                        </div>
                        {{ $plans->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-layer-group fa-4x text-gray-300"></i>
                            </div>
                            <h4 class="text-gray-500 mb-3">No Voucher Plans Found</h4>
                            <p class="text-muted mb-4">Get started by creating your first voucher plan to offer WiFi services to your customers.</p>
                            <a href="{{ route('voucher-plans.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>Create Your First Plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
.bg-gradient-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.border-success {
    border-left: 4px solid #28a745 !important;
}

.border-secondary {
    border-left: 4px solid #6c757d !important;
}
</style>
