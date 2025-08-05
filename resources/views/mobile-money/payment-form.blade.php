
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
.country-select {
    margin-bottom: 1.5rem;
}
.country-select select {
    background: #f5f7fa;
    border: none;
    border-radius: 10px;
    padding: 0.8rem 1rem;
    width: 100%;
    font-size: 1rem;
    color: #333;
}
.provider-options {
    margin: 1rem 0;
    display: none;
}
.provider-btn {
    background: #f5f7fa;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 0.8rem 1rem;
    margin-bottom: 0.5rem;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all 0.2s;
}
.provider-btn.selected {
    border-color: #2c5364;
    background: rgba(44, 83, 100, 0.1);
}
.provider-btn:hover {
    border-color: #2c5364;
}
</style>
</head>
<body>

<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="landing-card">
        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, #0f2027 0%, #2c5364 100%); border-radius: 15px; margin: 0 auto 1.5rem auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
            📶
        </div>
        <div class="greeting">
            Hello {{ $customerName ? e($customerName) : 'Guest' }}
        </div>
        <div class="landing-title">
            Welcome, please select a package to continue
        </div>

        <!-- Country Selection -->
        <div class="country-select">
            <select id="countrySelect" required>
                <option value="">Select Country</option>
                <option value="KE">🇰🇪 Kenya</option>
                <option value="UG">🇺🇬 Uganda</option>
                <option value="TZ">🇹🇿 Tanzania</option>
                <option value="RW">🇷🇼 Rwanda</option>
                <option value="BI">🇧🇮 Burundi</option>
                <option value="SS">🇸🇸 South Sudan</option>
                <option value="ET">🇪🇹 Ethiopia</option>
            </select>
        </div>

        <form method="POST" action="{{ route('mobile-money.initiate') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="plan_id" id="plan_id" value="">
            <input type="hidden" name="provider" id="selectedProvider" value="">
            <input type="hidden" name="customer_name" id="customerName" value="Guest User">
            <input type="hidden" name="country" id="selectedCountry" value="">
            <input type="hidden" name="email" value="">
            
            <div id="package-list">
                @foreach($plans as $plan)
                <button type="button" class="package-btn" data-id="{{ $plan->id }}">
                    <span>
                        <b>{{ number_format($plan->price) }} {{ $plan->currency ?? 'UGX' }}</b><br>
                        <small>{{ $plan->duration_hours }} Hours Unlimited Access</small>
                    </span>
                    <i class="bi bi-chevron-right"></i>
                </button>
                @endforeach
            </div>

            <!-- Payment Provider Selection -->
            <div class="provider-options" id="providerOptions">
                <h6 style="margin-bottom: 1rem; color: #333;">Select Payment Method:</h6>
                
                <!-- Cash Payment Option (Always Visible) -->
                <button type="button" class="provider-btn" data-provider="cash">
                    <span>
                        <strong>💰 Cash Payment</strong><br>
                        <small>Pay with cash at our location</small>
                    </span>
                    <span style="color: #28a745; font-size: 1.2rem;">💵</span>
                </button>
                
                <!-- Kenya Providers -->
                <div class="kenya-providers" style="display: none;">
                    <button type="button" class="provider-btn" data-provider="safaricom_mpesa">
                        <span>
                            <strong>M-Pesa (Safaricom)</strong><br>
                            <small>Pay with M-Pesa</small>
                        </span>
                        <span style="color: #00AA4F; font-size: 1.2rem;">📱</span>
                    </button>
                </div>

                <!-- Other Countries Providers -->
                <div class="other-providers" style="display: none;">
                    <button type="button" class="provider-btn" data-provider="mtn_mobile_money">
                        <span>
                            <strong>MTN Mobile Money</strong><br>
                            <small>Pay with MTN MoMo</small>
                        </span>
                        <span style="color: #FFC000; font-size: 1.2rem;">📱</span>
                    </button>
                    <button type="button" class="provider-btn" data-provider="airtel_money">
                        <span>
                            <strong>Airtel Money</strong><br>
                            <small>Pay with Airtel Money</small>
                        </span>
                        <span style="color: #ED2024; font-size: 1.2rem;">📱</span>
                    </button>
                </div>
            </div>

            <div class="phone-input-group" id="phoneInputGroup" style="display: none;">
                <img src="" id="countryFlag" class="country-flag" alt="">
                <span class="country-code" id="countryCode">+256</span>
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
// Country data with flags and phone codes
const countryData = {
    'KE': { flag: 'https://hatscripts.github.io/circle-flags/flags/ke.svg', code: '+254', placeholder: '0712345678', currency: 'KES' },
    'UG': { flag: 'https://hatscripts.github.io/circle-flags/flags/ug.svg', code: '+256', placeholder: '0712345678', currency: 'UGX' },
    'TZ': { flag: 'https://hatscripts.github.io/circle-flags/flags/tz.svg', code: '+255', placeholder: '0712345678', currency: 'TZS' },
    'RW': { flag: 'https://hatscripts.github.io/circle-flags/flags/rw.svg', code: '+250', placeholder: '0712345678', currency: 'RWF' },
    'BI': { flag: 'https://hatscripts.github.io/circle-flags/flags/bi.svg', code: '+257', placeholder: '0712345678', currency: 'BIF' },
    'SS': { flag: 'https://hatscripts.github.io/circle-flags/flags/ss.svg', code: '+211', placeholder: '0712345678', currency: 'SSP' },
    'ET': { flag: 'https://hatscripts.github.io/circle-flags/flags/et.svg', code: '+251', placeholder: '0912345678', currency: 'ETB' }
};

let selectedPlan = null;
let selectedProvider = null;
let selectedCountry = null;

// Country selection logic
document.getElementById('countrySelect').addEventListener('change', function() {
    selectedCountry = this.value;
    document.getElementById('selectedCountry').value = selectedCountry;
    
    if (selectedCountry) {
        // Update currency for packages
        updatePackageCurrency(selectedCountry);
        
        // Show provider options
        const providerOptions = document.getElementById('providerOptions');
        const kenyaProviders = document.querySelector('.kenya-providers');
        const otherProviders = document.querySelector('.other-providers');
        
        providerOptions.style.display = 'block';
        
        if (selectedCountry === 'KE') {
            kenyaProviders.style.display = 'block';
            otherProviders.style.display = 'none';
        } else {
            kenyaProviders.style.display = 'none';
            otherProviders.style.display = 'block';
        }
        
        // Update phone input
        const countryInfo = countryData[selectedCountry];
        if (countryInfo) {
            document.getElementById('countryFlag').src = countryInfo.flag;
            document.getElementById('countryCode').textContent = countryInfo.code;
            document.getElementById('phone_number').placeholder = countryInfo.placeholder;
            document.getElementById('phoneInputGroup').style.display = 'flex';
        }
        
        // Reset selections
        selectedProvider = null;
        document.getElementById('selectedProvider').value = '';
        document.querySelectorAll('.provider-btn').forEach(btn => btn.classList.remove('selected'));
        updatePayButton();
    } else {
        document.getElementById('providerOptions').style.display = 'none';
        document.getElementById('phoneInputGroup').style.display = 'none';
        updatePayButton();
    }
});

// Package selection logic
document.querySelectorAll('.package-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.package-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        selectedPlan = this.getAttribute('data-id');
        document.getElementById('plan_id').value = selectedPlan;
        updatePayButton();
    });
});

// Provider selection logic
document.addEventListener('click', function(e) {
    if (e.target.closest('.provider-btn')) {
        const btn = e.target.closest('.provider-btn');
        document.querySelectorAll('.provider-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        selectedProvider = btn.getAttribute('data-provider');
        document.getElementById('selectedProvider').value = selectedProvider;
        
        // Handle cash payments - hide phone input
        const phoneInputGroup = document.getElementById('phoneInputGroup');
        if (selectedProvider === 'cash') {
            phoneInputGroup.style.display = 'none';
            // Set a default phone number for cash payments
            document.getElementById('phone_number').value = '+254700000000';
        } else {
            phoneInputGroup.style.display = 'flex';
            document.getElementById('phone_number').value = '';
        }
        
        updatePayButton();
    }
});

// Update pay button state
function updatePayButton() {
    const payBtn = document.getElementById('payNowBtn');
    const isReady = selectedCountry && selectedPlan && selectedProvider;
    payBtn.disabled = !isReady;
}

// Update package currency based on country
function updatePackageCurrency(country) {
    const currency = countryData[country]?.currency || 'USD';
    document.querySelectorAll('.package-btn b').forEach(priceElement => {
        const text = priceElement.textContent;
        // Replace the currency part (last 3 characters)
        const price = text.replace(/[A-Z]{3}$/, currency);
        priceElement.textContent = price;
    });
}

// Payment form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate all required fields
    if (!selectedPlan) {
        alert('Please select a package');
        return;
    }
    if (!selectedProvider) {
        alert('Please select a payment method');
        return;
    }
    
    // For mobile money payments, validate country and phone
    if (selectedProvider !== 'cash') {
        if (!selectedCountry) {
            alert('Please select a country');
            return;
        }
        
        const phoneNumber = document.getElementById('phone_number').value;
        if (!phoneNumber) {
            alert('Please enter your phone number');
            return;
        }
        
        // Clean and format phone number
        let cleanPhone = phoneNumber.replace(/\s+/g, '').replace(/[^\d+]/g, '');
        if (!cleanPhone.startsWith('+')) {
            const countryInfo = countryData[selectedCountry];
            if (countryInfo && cleanPhone.startsWith('0')) {
                cleanPhone = countryInfo.code + cleanPhone.substring(1);
            } else if (countryInfo) {
                cleanPhone = countryInfo.code + cleanPhone;
            }
        }
        
        // Update the form field with cleaned phone number
        document.getElementById('phone_number').value = cleanPhone;
    }
    
    const payBtn = document.getElementById('payNowBtn');
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    
    // Show loading modal
    modal.show();
    
    // Submit form via AJAX
    const formData = new FormData(this);
    
    // Debug: Log form data
    console.log('Submitting form data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid server response');
            }
        });
    })
    .then(data => {
        console.log('Parsed response:', data);
        if (data.success) {
            // Handle cash payment differently
            if (data.payment_type === 'cash') {
                document.getElementById('paymentStatus').innerHTML = `
                    <i class="fas fa-money-bill-wave text-success fa-3x mb-3"></i>
                    <h6>Cash Payment Request Created!</h6>
                    <p class="text-muted">${data.message}</p>
                    <div class="alert alert-info mt-3">
                        <strong>Transaction ID:</strong> ${data.transaction_id}<br>
                        <strong>Amount to Pay:</strong> KES ${data.amount}<br>
                        <strong>Instructions:</strong> ${data.instructions}
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" onclick="window.location.reload()">
                            Continue
                        </button>
                    </div>
                `;
            } else {
                // Handle mobile money payment
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
            }
        } else {
            // Show error
            let errorMessage = data.message || 'Payment failed';
            if (data.errors) {
                const errors = Object.values(data.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            document.getElementById('paymentStatus').innerHTML = `
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h6>Payment Failed</h6>
                <p class="text-muted">${errorMessage}</p>
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
