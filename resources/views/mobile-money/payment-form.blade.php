<x-layouts.app>
    <x-slot name="title">Buy WiFi Voucher</x-slot>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="text-center">
                <h1 class="h2 mb-2 text-gray-800">Buy WiFi Voucher</h1>
                <p class="text-muted">Choose a plan and pay with Mobile Money to get instant internet access</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Plan Selection -->
        @if(!isset($selectedPlan))
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-wifi"></i> Available Plans
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($plans as $plan)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 plan-card" data-plan-id="{{ $plan->id }}">
                                <div class="card-header bg-gradient-primary text-white text-center">
                                    <h5 class="card-title mb-0">{{ $plan->name }}</h5>
                                    @if($plan->is_popular)
                                        <span class="badge bg-warning text-dark">Most Popular</span>
                                    @endif
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-4 text-primary mb-3">
                                        ${{ number_format($plan->price, 2) }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Duration:</span>
                                            <strong>{{ $plan->formatted_duration }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Data Limit:</span>
                                            <strong>{{ $plan->formatted_data_limit }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Speed:</span>
                                            <strong>{{ $plan->speed_limit ? $plan->speed_limit . ' Mbps' : 'Unlimited' }}</strong>
                                        </div>
                                    </div>

                                    @if($plan->description)
                                    <p class="text-muted small">{{ $plan->description }}</p>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <a href="/buy-voucher/{{ $plan->id }}" class="btn btn-primary btn-block">
                                        Select This Plan
                                    </a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5 class="text-muted">No plans available</h5>
                                <p class="text-muted">Please contact support for assistance.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Payment Form -->
        <div class="col-lg-8">
            <div class="row">
                <!-- Selected Plan Summary -->
                <div class="col-md-12 mb-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="m-0"><i class="fas fa-check-circle"></i> Selected Plan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="text-primary">{{ $selectedPlan->name }}</h4>
                                    <p class="text-muted mb-2">{{ $selectedPlan->description }}</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="display-5 text-success mb-2">
                                        ${{ number_format($selectedPlan->price, 2) }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $selectedPlan->formatted_duration }} • {{ $selectedPlan->formatted_data_limit }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-credit-card"></i> Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="paymentForm" method="POST" action="{{ route('mobile-money.initiate') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $selectedPlan->id }}">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone_number" class="form-label">
                                                Phone Number <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                                   id="phone_number" name="phone_number" 
                                                   placeholder="+1234567890" 
                                                   value="{{ old('phone_number') }}" required>
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Include country code (e.g., +256701234567)</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="provider" class="form-label">
                                                Mobile Money Provider <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('provider') is-invalid @enderror" 
                                                    id="provider" name="provider" required>
                                                <option value="">Select Provider</option>
                                                <option value="mtn_mobile_money" {{ old('provider') === 'mtn_mobile_money' ? 'selected' : '' }}>
                                                    MTN Mobile Money
                                                </option>
                                                <option value="airtel_money" {{ old('provider') === 'airtel_money' ? 'selected' : '' }}>
                                                    Airtel Money
                                                </option>
                                                <option value="vodacom_mpesa" {{ old('provider') === 'vodacom_mpesa' ? 'selected' : '' }}>
                                                    Vodacom M-Pesa
                                                </option>
                                                <option value="tigo_pesa" {{ old('provider') === 'tigo_pesa' ? 'selected' : '' }}>
                                                    Tigo Pesa
                                                </option>
                                                <option value="safaricom_mpesa" {{ old('provider') === 'safaricom_mpesa' ? 'selected' : '' }}>
                                                    Safaricom M-Pesa
                                                </option>
                                                <option value="orange_money" {{ old('provider') === 'orange_money' ? 'selected' : '' }}>
                                                    Orange Money
                                                </option>
                                            </select>
                                            @error('provider')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">
                                                Full Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                   id="customer_name" name="customer_name" 
                                                   placeholder="Enter your full name"
                                                   value="{{ old('customer_name') }}" required>
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email (Optional)</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" 
                                                   placeholder="your@email.com"
                                                   value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">We'll send your voucher here if provided</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Summary -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Payment Summary</h6>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Plan Price:</strong> ${{ number_format($selectedPlan->price, 2) }}
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Service Fee (3%):</strong> ${{ number_format($selectedPlan->price * 0.03, 2) }}
                                        </div>
                                        <div class="col-12 mt-2">
                                            <strong class="text-primary">Total Amount: ${{ number_format($selectedPlan->price * 1.03, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                        <label class="form-check-label" for="agree_terms">
                                            I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('voucher.buy') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Change Plan
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg" id="payBtn">
                                        <i class="fas fa-credit-card"></i> Pay ${{ number_format($selectedPlan->price * 1.03, 2) }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle"></i> How it works
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h6>Select Plan</h6>
                                <p class="text-muted small">Choose the internet plan that suits your needs</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h6>Enter Details</h6>
                                <p class="text-muted small">Provide your phone number and mobile money provider</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h6>Complete Payment</h6>
                                <p class="text-muted small">Approve the payment on your mobile money account</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h6>Get Voucher</h6>
                                <p class="text-muted small">Receive your WiFi credentials via SMS instantly</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-headset"></i> Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Contact our support team if you need assistance</p>
                    <div class="d-grid gap-2">
                        <a href="tel:{{ config('app.support_phone', '+1234567890') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone"></i> Call Support
                        </a>
                        <a href="https://wa.me/{{ str_replace('+', '', config('app.support_phone', '+1234567890')) }}" 
                           class="btn btn-outline-success btn-sm" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Payment Status Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Processing</h5>
            </div>
            <div class="modal-body text-center">
                <div id="paymentStatus">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6>Processing your payment...</h6>
                    <p class="text-muted">Please check your phone and approve the payment request.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<x-slot name="styles">
<style>
.plan-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.timeline-steps .step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.timeline-steps .step:last-child {
    margin-bottom: 0;
}

.step-number {
    width: 30px;
    height: 30px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin-right: 15px;
    flex-shrink: 0;
}

.step-content h6 {
    margin-bottom: 5px;
    font-size: 14px;
}

.step-content p {
    margin-bottom: 0;
    font-size: 12px;
    line-height: 1.3;
}
</style>
</x-slot>

<x-slot name="scripts">
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const payBtn = document.getElementById('payBtn');
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            
            // Show loading modal
            modal.show();
            
            // Submit form via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update modal content
                    document.getElementById('paymentStatus').innerHTML = `
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h6>Payment Initiated Successfully!</h6>
                        <p class="text-muted">Please check your phone and approve the payment request.</p>
                        <p class="text-muted">Transaction ID: <strong>${data.transaction_id}</strong></p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" onclick="checkPaymentStatus('${data.transaction_id}')">
                                Check Status
                            </button>
                        </div>
                    `;
                } else {
                    // Show error
                    document.getElementById('paymentStatus').innerHTML = `
                        <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                        <h6>Payment Failed</h6>
                        <p class="text-muted">${data.message}</p>
                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Try Again
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('paymentStatus').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                    <h6>Connection Error</h6>
                    <p class="text-muted">Please check your internet connection and try again.</p>
                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                `;
            });
        });
    }
});

function checkPaymentStatus(transactionId) {
    fetch('{{ route("mobile-money.check-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            transaction_id: transactionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.status === 'completed') {
            document.getElementById('paymentStatus').innerHTML = `
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h6>Payment Successful!</h6>
                <p class="text-muted">Your voucher has been sent to your phone via SMS.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-success" onclick="window.location.reload()">
                        Continue
                    </button>
                </div>
            `;
        } else if (data.success && data.status === 'failed') {
            document.getElementById('paymentStatus').innerHTML = `
                <i class="fas fa-times-circle text-danger fa-3x mb-3"></i>
                <h6>Payment Failed</h6>
                <p class="text-muted">${data.message || 'The payment was not completed successfully.'}</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Try Again
                    </button>
                </div>
            `;
        } else {
            // Still pending, check again in 5 seconds
            setTimeout(() => {
                checkPaymentStatus(transactionId);
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Error checking payment status:', error);
    });
}
</script>
</x-slot>

</x-layouts.app>
