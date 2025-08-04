
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buy WiFi Voucher - WiFi Now</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

@php
    $plans = $plans ?? \App\Models\VoucherPlan::where('is_active', true)->orderBy('price')->get();
    $customerName = isset($customer_name) ? $customer_name : null;
@endphp

<style>
body {
    background: linear-gradient(135deg, #0f2027 0%, #2c5364 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.landing-card {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    max-width: 400px;
    margin: 40px auto;
    padding: 2.5rem 2rem 2rem 2rem;
    position: relative;
}
.logo {
    width: 90px;
    margin: 0 auto 1.5rem auto;
    display: block;
}
.greeting {
    font-size: 1.5rem;
    font-weight: 600;
    color: #0f2027;
    text-align: center;
    margin-bottom: 0.5rem;
}
.landing-title {
    text-align: center;
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 1.5rem;
}
.package-btn {
    background: #0f2027;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 0.7rem;
    padding: 1rem 1.2rem;
    width: 100%;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s;
}
.package-btn.selected, .package-btn:hover {
    background: #2c5364;
    color: #fff;
}
.phone-input-group {
    margin: 1.5rem 0 1rem 0;
    display: flex;
    align-items: center;
    background: #f5f7fa;
    border-radius: 10px;
    padding: 0.5rem 1rem;
}
.country-flag {
    width: 28px;
    margin-right: 0.5rem;
}
.country-code {
    font-weight: 500;
    color: #333;
    margin-right: 0.5rem;
}
.pay-btn {
    background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1.2rem;
    font-weight: 600;
    width: 100%;
    padding: 1rem;
    margin-top: 1.2rem;
    box-shadow: 0 4px 16px rgba(56,239,125,0.12);
    transition: background 0.2s;
}
.pay-btn:hover {
    background: linear-gradient(90deg, #38ef7d 0%, #11998e 100%);
}
.pay-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.terms {
    font-size: 0.9rem;
    color: #888;
    text-align: center;
    margin-top: 1.2rem;
}
</style>
</head>
<body>

<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="landing-card">
        <img src="/assets/img/wifi-logo.png" alt="wifi now!" class="logo">
        <div class="greeting">
            Hello {{ $customerName ? e($customerName) : 'Guest' }}
        </div>
        <div class="landing-title">
            Welcome, please select a package to continue
        </div>

        <form method="POST" action="{{ route('mobile-money.initiate') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="plan_id" id="plan_id" value="">
            <input type="hidden" name="provider" value="safaricom_mpesa">
            <input type="hidden" name="customer_name" value="Guest User">
            
            <div id="package-list">
                @foreach($plans as $plan)
                <button type="button" class="package-btn" data-id="{{ $plan->id }}">
                    <span>
                        <b>{{ number_format($plan->price) }} UGX</b><br>
                        <small>{{ $plan->duration_hours }} Hours Unlimited Access</small>
                    </span>
                    <i class="bi bi-chevron-right"></i>
                </button>
                @endforeach
            </div>

            <div class="phone-input-group">
                <img src="https://hatscripts.github.io/circle-flags/flags/ug.svg" class="country-flag" alt="UG">
                <span class="country-code">+256</span>
                <input type="tel" name="phone_number" id="phone_number" class="form-control border-0 bg-transparent" placeholder="07XXXXXXXX" required style="flex:1;">
            </div>

            <button type="submit" class="pay-btn" id="payNowBtn" disabled>Pay Now</button>
        </form>
        <div class="terms">
            By signing in, you accept our <a href="#" target="_blank">Terms &amp; Conditions</a>
        </div>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Package selection logic
document.querySelectorAll('.package-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.package-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('plan_id').value = this.getAttribute('data-id');
        document.getElementById('payNowBtn').disabled = false;
    });
});

// Payment form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const payBtn = document.getElementById('payNowBtn');
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

</body>
</html>
