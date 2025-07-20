@section('title', 'Generate Vouchers')
<x-layouts.app title="Generate Vouchers">

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Generate Vouchers</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('vouchers.index') }}">Vouchers</a></li>
                            <li class="breadcrumb-item active">Generate</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Vouchers
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Generate Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Voucher Generation</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vouchers.generate') }}" method="POST" id="generateForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="voucher_plan_id" class="form-label">
                                        Voucher Plan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('voucher_plan_id') is-invalid @enderror" 
                                            id="voucher_plan_id" name="voucher_plan_id" required>
                                        <option value="">Select a plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" 
                                                    data-price="{{ $plan->price }}"
                                                    data-duration="{{ $plan->formatted_duration }}"
                                                    data-data-limit="{{ $plan->formatted_data_limit }}"
                                                    {{ old('voucher_plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('voucher_plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">
                                        Quantity <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" min="1" max="1000" 
                                           value="{{ old('quantity', 1) }}" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Maximum 1000 vouchers per batch</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="router_id" class="form-label">Router (Optional)</label>
                                    <select class="form-select @error('router_id') is-invalid @enderror" 
                                            id="router_id" name="router_id">
                                        <option value="">Auto-assign router</option>
                                        @foreach($routers as $router)
                                            <option value="{{ $router->id }}" 
                                                    {{ old('router_id') == $router->id ? 'selected' : '' }}>
                                                {{ $router->name }} ({{ $router->location }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('router_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Plan Preview -->
                        <div id="planPreview" class="alert alert-info d-none">
                            <h6>Selected Plan Details:</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Duration:</strong> <span id="previewDuration"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Data Limit:</strong> <span id="previewDataLimit"></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Price:</strong> $<span id="previewPrice"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Summary -->
                        <div id="costSummary" class="alert alert-warning d-none">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total Cost:</strong> $<span id="totalCost">0.00</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Commission (3%):</strong> $<span id="commission">0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="generateBtn">
                                <i class="fas fa-cog fa-spin d-none" id="loadingIcon"></i>
                                <i class="fas fa-plus" id="generateIcon"></i>
                                Generate Vouchers
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Tips -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Vouchers:</span>
                            <strong id="totalVouchers">{{ \App\Models\Voucher::count() }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Available:</span>
                            <strong class="text-success" id="availableVouchers">
                                {{ \App\Models\Voucher::whereIn('status', ['generated', 'printed'])->count() }}
                            </strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Used Today:</span>
                            <strong class="text-info" id="usedToday">
                                {{ \App\Models\Voucher::where('status', 'used')->whereDate('used_at', today())->count() }}
                            </strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Revenue Today:</span>
                            <strong class="text-primary" id="revenueToday">
                                ${{ number_format(\App\Models\Voucher::where('status', 'used')->whereDate('used_at', today())->sum('price'), 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Generation Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <small>Generate vouchers in smaller batches for better performance</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-info"></i>
                            <small>Vouchers are automatically assigned unique codes</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-print text-success"></i>
                            <small>Print vouchers immediately after generation</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-shield-alt text-primary"></i>
                            <small>All vouchers include secure passwords</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-clock text-secondary"></i>
                            <small>Track expiration dates for better inventory management</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('voucher_plan_id');
    const quantityInput = document.getElementById('quantity');
    const planPreview = document.getElementById('planPreview');
    const costSummary = document.getElementById('costSummary');
    const generateForm = document.getElementById('generateForm');
    const generateBtn = document.getElementById('generateBtn');
    const loadingIcon = document.getElementById('loadingIcon');
    const generateIcon = document.getElementById('generateIcon');

    function updatePreview() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;

        if (selectedOption.value && quantity > 0) {
            // Show plan details
            document.getElementById('previewDuration').textContent = selectedOption.dataset.duration;
            document.getElementById('previewDataLimit').textContent = selectedOption.dataset.dataLimit;
            document.getElementById('previewPrice').textContent = parseFloat(selectedOption.dataset.price).toFixed(2);
            planPreview.classList.remove('d-none');

            // Calculate costs
            const unitPrice = parseFloat(selectedOption.dataset.price);
            const totalCost = unitPrice * quantity;
            const commission = totalCost * 0.03;

            document.getElementById('totalCost').textContent = totalCost.toFixed(2);
            document.getElementById('commission').textContent = commission.toFixed(2);
            costSummary.classList.remove('d-none');
        } else {
            planPreview.classList.add('d-none');
            costSummary.classList.add('d-none');
        }
    }

    planSelect.addEventListener('change', updatePreview);
    quantityInput.addEventListener('input', updatePreview);

    // Form submission
    generateForm.addEventListener('submit', function(e) {
        generateBtn.disabled = true;
        loadingIcon.classList.remove('d-none');
        generateIcon.classList.add('d-none');
        generateBtn.innerHTML = '<i class="fas fa-cog fa-spin"></i> Generating...';
    });

    // Initial preview update
    updatePreview();
});
</script>
@endpush
</x-layouts.app>
