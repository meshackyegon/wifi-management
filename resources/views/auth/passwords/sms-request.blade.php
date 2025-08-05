@extends('layouts.auth')

@section('title', 'Reset Password via SMS')

@section('content')
<div class="card">
    <div class="card-header text-center">
        <h4 class="mb-0">Reset Password via SMS</h4>
        <p class="text-muted mt-2">Enter your phone number to receive a temporary password via SMS</p>
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

        <form method="POST" action="{{ route('password.sms.send') }}">
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
                    value="{{ old('phone') }}" 
                    placeholder="+254712345678 or 0712345678"
                    required
                >
                <div class="form-text">
                    Enter your phone number in any format (e.g., +254712345678, 0712345678, or 712345678)
                </div>
                @error('phone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sms"></i> Send Reset SMS
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('password.request') }}" class="text-decoration-none">
                        <i class="fas fa-envelope"></i> Reset via Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h6 class="card-title">
            <i class="fas fa-info-circle text-info"></i> How SMS Password Reset Works
        </h6>
        <ol class="mb-0">
            <li>Enter your registered phone number</li>
            <li>You'll receive an SMS with a temporary password and verification code</li>
            <li>Use both the temporary password and verification code to set a new password</li>
            <li>The temporary password expires in 15 minutes for security</li>
        </ol>
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
</style>
@endpush
