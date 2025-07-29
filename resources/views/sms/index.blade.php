@section('title', 'Send SMS')
<x-layouts.app title="Send SMS">

<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="h2 mb-2 text-white">📱 SMS Management Center</h1>
                            <p class="mb-0 text-white-75">Send SMS messages to customers and manage your communication</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex gap-2 justify-content-lg-end">
                                <a href="{{ route('sms.logs') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-history"></i> SMS History
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-arrow-left"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon me-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1">Error!</h6>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Send SMS Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="fas fa-paper-plane text-white fa-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Compose SMS Message</h5>
                            <p class="text-muted mb-0">Send instant messages to your customers</p>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <form action="{{ route('sms.send') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="phone_number" class="form-label fw-semibold">
                                        <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" 
                                               id="phone_number" name="phone_number" 
                                               value="{{ old('phone_number') }}" 
                                               placeholder="+254722123456"
                                               required>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle text-info"></i>
                                        Include country code (e.g., +254 for Kenya)
                                    </div>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="provider" class="form-label fw-semibold">
                                        <i class="fas fa-satellite-dish text-primary me-2"></i>SMS Provider
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
                                        <select class="form-select form-select-lg @error('provider') is-invalid @enderror" 
                                                id="provider" name="provider" required>
                                            <option value="">Choose Provider</option>
                                            <option value="jambopay" {{ old('provider') === 'jambopay' ? 'selected' : '' }}>
                                                <i class="fas fa-star"></i> JamboPay
                                            </option>
                                            <option value="twilio" {{ old('provider') === 'twilio' ? 'selected' : '' }}>
                                                Twilio
                                            </option>
                                            <option value="africas_talking" {{ old('provider') === 'africas_talking' ? 'selected' : '' }}>
                                                Africa's Talking
                                            </option>
                                        </select>
                                    </div>
                                    @error('provider')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">
                                <i class="fas fa-comment-dots text-primary me-2"></i>Message Content
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" 
                                      placeholder="Type your message here... Keep it clear and concise!"
                                      required>{{ old('message') }}</textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-info"></i>
                                    <span id="charCount" class="fw-bold">0</span>/1000 characters
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark" id="smsCount">1 SMS</span>
                                </div>
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <div class="text-muted">
                                <i class="fas fa-zap text-warning"></i>
                                <small class="fw-semibold">SMS will be sent immediately</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i>Send SMS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Templates -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-magic fa-lg me-2"></i>
                        <h6 class="mb-0 text-white fw-bold">Quick Templates</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-lg" onclick="useTemplate('voucher')">
                            <i class="fas fa-ticket-alt me-2"></i>Voucher Details
                        </button>
                        <button type="button" class="btn btn-outline-success btn-lg" onclick="useTemplate('welcome')">
                            <i class="fas fa-hand-wave me-2"></i>Welcome Message
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-lg" onclick="useTemplate('expiry')">
                            <i class="fas fa-clock me-2"></i>Expiry Reminder
                        </button>
                        <button type="button" class="btn btn-outline-info btn-lg" onclick="useTemplate('promo')">
                            <i class="fas fa-gift me-2"></i>Promotional
                        </button>
                    </div>
                </div>
            </div>

            <!-- SMS Statistics -->
            <div class="card shadow border-0">
                <div class="card-header bg-gradient-success text-white border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line fa-lg me-2"></i>
                        <h6 class="mb-0 text-white fw-bold">Today's SMS Stats</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h3 class="text-success fw-bold">{{ \App\Models\SmsLog::whereDate('created_at', today())->where('status', 'sent')->count() }}</h3>
                                <small class="text-muted fw-semibold">Sent Successfully</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-danger mb-2">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                            <h3 class="text-danger fw-bold">{{ \App\Models\SmsLog::whereDate('created_at', today())->where('status', 'failed')->count() }}</h3>
                            <small class="text-muted fw-semibold">Failed</small>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar-day me-1"></i>
                                Statistics for {{ now()->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter with enhanced functionality
document.getElementById('message').addEventListener('input', function() {
    const count = this.value.length;
    const charCountElement = document.getElementById('charCount');
    const smsCountElement = document.getElementById('smsCount');
    
    charCountElement.textContent = count;
    
    // Calculate SMS count (160 chars per SMS)
    const smsCount = Math.ceil(count / 160) || 1;
    smsCountElement.textContent = `${smsCount} SMS`;
    
    // Color coding for character count
    if (count > 1000) {
        this.classList.add('is-invalid');
        charCountElement.classList.add('text-danger');
        smsCountElement.classList.remove('bg-light', 'bg-warning');
        smsCountElement.classList.add('bg-danger', 'text-white');
    } else if (count > 800) {
        this.classList.remove('is-invalid');
        charCountElement.classList.remove('text-danger');
        charCountElement.classList.add('text-warning');
        smsCountElement.classList.remove('bg-light', 'bg-danger');
        smsCountElement.classList.add('bg-warning', 'text-dark');
    } else {
        this.classList.remove('is-invalid');
        charCountElement.classList.remove('text-danger', 'text-warning');
        smsCountElement.classList.remove('bg-warning', 'bg-danger');
        smsCountElement.classList.add('bg-light', 'text-dark');
    }
});

// Enhanced SMS Templates with animations
const templates = {
    voucher: "🎫 Your WiFi voucher is ready!\n\nCode: [CODE]\nUsername: [USERNAME]\nPassword: [PASSWORD]\nValid for: [HOURS] hours\n\nEnjoy your internet! 📶",
    welcome: "🎉 Welcome to our WiFi service!\n\nYour voucher has been activated successfully.\nCode: [CODE]\n\nNeed help? Contact our support team.\n\nThank you for choosing us! 💙",
    expiry: "⏰ Reminder: Your WiFi voucher expires in 2 hours!\n\nCode: [CODE]\n\n🛒 Purchase a new voucher to continue enjoying uninterrupted internet access.\n\nDon't get disconnected!",
    promo: "🎉 SPECIAL OFFER ALERT! 🎉\n\nGet 20% OFF on your next WiFi voucher!\n\n💰 Use code: SAVE20\n⏳ Limited time offer\n\n🛒 Buy now and save big!"
};

function useTemplate(type) {
    const messageField = document.getElementById('message');
    const template = templates[type];
    
    // Add some visual feedback
    messageField.style.transition = 'all 0.3s ease';
    messageField.style.transform = 'scale(0.98)';
    
    setTimeout(() => {
        messageField.value = template;
        messageField.style.transform = 'scale(1)';
        
        // Trigger the input event to update character count
        messageField.dispatchEvent(new Event('input'));
        
        // Focus the message field
        messageField.focus();
        
        // Show success notification
        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} template loaded!`, 'success');
    }, 150);
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Form validation enhancement
document.querySelector('form').addEventListener('submit', function(e) {
    const phoneInput = document.getElementById('phone_number');
    const providerSelect = document.getElementById('provider');
    const messageInput = document.getElementById('message');
    
    // Phone number validation
    const phoneRegex = /^\+?[1-9]\d{1,14}$/;
    if (!phoneRegex.test(phoneInput.value)) {
        e.preventDefault();
        phoneInput.classList.add('is-invalid');
        showNotification('Please enter a valid phone number with country code', 'danger');
        return;
    }
    
    // Provider validation
    if (!providerSelect.value) {
        e.preventDefault();
        providerSelect.classList.add('is-invalid');
        showNotification('Please select an SMS provider', 'danger');
        return;
    }
    
    // Message validation
    if (messageInput.value.length === 0) {
        e.preventDefault();
        messageInput.classList.add('is-invalid');
        showNotification('Please enter a message', 'danger');
        return;
    }
    
    if (messageInput.value.length > 1000) {
        e.preventDefault();
        messageInput.classList.add('is-invalid');
        showNotification('Message is too long (max 1000 characters)', 'danger');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;
});

// Real-time validation
document.getElementById('phone_number').addEventListener('input', function() {
    const phoneRegex = /^\+?[1-9]\d{1,14}$/;
    if (phoneRegex.test(this.value)) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else {
        this.classList.remove('is-valid');
    }
});

document.getElementById('provider').addEventListener('change', function() {
    if (this.value) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else {
        this.classList.remove('is-valid');
    }
});
</script>

</x-layouts.app>
