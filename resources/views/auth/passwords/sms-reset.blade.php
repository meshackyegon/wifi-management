@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')
<div class="card">
    <div class="card-header text-center">
        <h4 class="mb-0">Set New Password</h4>
        <p class="text-muted mt-2">Enter the details from SMS to reset your password</p>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.sms.reset') }}">
            @csrf

            <div class="mb-3">
                <label for="phone" class="form-label">
                    <i class="fas fa-phone"></i> Phone Number
                </label>
                <input 
                    type="tel" 
                    class="form-control @error('phone') is-invalid @enderror" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $phone ?? '') }}" 
                    placeholder="+254712345678"
                    required
                >
                @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">
                            <i class="fas fa-key"></i> Verification Code
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('verification_code') is-invalid @enderror" 
                            id="verification_code" 
                            name="verification_code" 
                            value="{{ old('verification_code') }}" 
                            placeholder="6-digit code from SMS"
                            maxlength="6"
                            required
                        >
                        <div class="form-text">
                            Enter the 6-digit verification code from SMS
                        </div>
                        @error('verification_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="temporary_password" class="form-label">
                            <i class="fas fa-clock"></i> Temporary Password
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('temporary_password') is-invalid @enderror" 
                            id="temporary_password" 
                            name="temporary_password" 
                            value="{{ old('temporary_password') }}" 
                            placeholder="Temporary password from SMS"
                            required
                        >
                        <div class="form-text">
                            Enter the temporary password from SMS
                        </div>
                        @error('temporary_password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your new password"
                    minlength="8"
                    required
                >
                <div class="form-text">
                    Password must be at least 8 characters long
                </div>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock"></i> Confirm New Password
                </label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Confirm your new password"
                    minlength="8"
                    required
                >
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('password.sms.request') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to SMS Request
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-sign-in-alt"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h6 class="card-title">
            <i class="fas fa-exclamation-triangle text-warning"></i> Important Notes
        </h6>
        <ul class="mb-0">
            <li><strong>Temporary Password:</strong> Valid for 15 minutes only</li>
            <li><strong>Verification Code:</strong> Must be entered exactly as received</li>
            <li><strong>New Password:</strong> Should be strong and unique</li>
            <li><strong>Login Immediately:</strong> Use your new password to login after reset</li>
        </ul>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.text-muted {
    opacity: 0.8;
}

#verification_code {
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
    letter-spacing: 2px;
}

#temporary_password {
    font-family: monospace;
    font-size: 1.1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format verification code input
    const verificationCodeInput = document.getElementById('verification_code');
    verificationCodeInput.addEventListener('input', function(e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 6 characters
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });

    // Auto-format temporary password input
    const tempPasswordInput = document.getElementById('temporary_password');
    tempPasswordInput.addEventListener('input', function(e) {
        // Convert to uppercase
        this.value = this.value.toUpperCase();
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    function validatePassword() {
        if (passwordInput.value && confirmPasswordInput.value) {
            if (passwordInput.value === confirmPasswordInput.value) {
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            } else {
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
            }
        }
    }
    
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
});
</script>
@endpush
