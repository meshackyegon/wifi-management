<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buy WiFi Voucher - HotSpot Billing</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .voucher-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .voucher-card:hover {
            transform: translateY(-5px);
        }
        
        .plan-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .plan-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .plan-duration {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 20px;
        }
        
        .btn-buy {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .header-logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            margin-bottom: 3rem;
            font-size: 1.2rem;
        }
        
        .login-link {
            position: absolute;
            top: 20px;
            right: 20px;
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }
        
        .login-link:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .payment-methods {
            text-align: center;
            margin-top: 2rem;
        }
        
        .payment-logo {
            height: 40px;
            margin: 0 15px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .payment-logo:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- Login Link -->
    <a href="{{ route('login') }}" class="login-link">
        <i class="bi bi-person-circle me-2"></i>Admin Login
    </a>

    <div class="main-container">
        <div class="container">
            <!-- Header -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="header-logo">
                        <i class="bi bi-wifi me-3"></i>HotSpot Billing
                    </div>
                    <div class="header-subtitle">
                        Get instant WiFi access with our prepaid vouchers. Choose your plan and pay with M-Pesa or Mobile Money.
                    </div>
                </div>
            </div>

            <!-- Voucher Plans -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="voucher-card p-4">
                        <h2 class="text-center mb-4 fw-bold" style="color: #667eea;">
                            <i class="bi bi-ticket-perforated me-2"></i>Choose Your WiFi Plan
                        </h2>
                        
                        <div class="row g-4">
                            @php
                                $plans = \App\Models\VoucherPlan::where('is_active', true)->orderBy('price')->get();
                            @endphp
                            
                            @forelse($plans as $plan)
                            <div class="col-lg-4 col-md-6">
                                <div class="plan-card p-4 text-center">
                                    <div class="feature-icon">
                                        <i class="bi bi-wifi"></i>
                                    </div>
                                    
                                    <h4 class="fw-bold mb-3">{{ $plan->name }}</h4>
                                    <div class="plan-price">KES {{ number_format($plan->price) }}</div>
                                    <div class="plan-duration mb-3">{{ $plan->duration_hours }} Hours</div>
                                    
                                    <div class="mb-4">
                                        @if($plan->data_limit_mb)
                                            <div class="mb-2">
                                                <i class="bi bi-download text-primary me-2"></i>
                                                {{ $plan->data_limit_mb }} MB Data
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <i class="bi bi-infinity text-success me-2"></i>
                                                Unlimited Data
                                            </div>
                                        @endif
                                        
                                        @if($plan->speed_limit_kbps)
                                            <div class="mb-2">
                                                <i class="bi bi-speedometer2 text-info me-2"></i>
                                                {{ number_format($plan->speed_limit_kbps / 1024, 1) }} Mbps Speed
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <i class="bi bi-lightning text-warning me-2"></i>
                                                Full Speed
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <p class="text-muted small mb-4">{{ $plan->description }}</p>
                                    
                                    <a href="{{ route('voucher.buy.plan', $plan) }}" class="btn btn-buy w-100">
                                        <i class="bi bi-cart-plus me-2"></i>Buy Now
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No voucher plans available at the moment. Please check back later.
                                </div>
                            </div>
                            @endforelse
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <h5 class="mb-3" style="color: #667eea;">Accepted Payment Methods</h5>
                            <div class="d-flex justify-content-center align-items-center flex-wrap">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiB2aWV3Qm94PSIwIDAgMTAwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiByeD0iNSIgZmlsbD0iIzAwQUE0RiIvPgo8dGV4dCB4PSI1MCIgeT0iMjQiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5NLVBFU0E8L3RleHQ+Cjwvc3ZnPgo=" alt="M-Pesa" class="payment-logo">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiB2aWV3Qm94PSIwIDAgMTAwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiByeD0iNSIgZmlsbD0iI0VEMjAyNCIvPgo8dGV4dCB4PSI1MCIgeT0iMjQiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5BSVJURUwgJCQkPC90ZXh0Pgo8L3N2Zz4K" alt="Airtel Money" class="payment-logo">
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiB2aWV3Qm94PSIwIDAgMTAwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiByeD0iNSIgZmlsbD0iI0ZGQzAwMCIvPgo8dGV4dCB4PSI1MCIgeT0iMjQiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZm9udC13ZWlnaHQ9ImJvbGQiIGZpbGw9ImJsYWNrIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5NVE4gJCQkPC90ZXh0Pgo8L3N2Zz4K" alt="MTN Mobile Money" class="payment-logo">
                            </div>
                            <p class="mt-3 text-muted small">
                                <i class="bi bi-shield-check text-success me-1"></i>
                                Secure payments • Instant voucher delivery via SMS
                            </p>
                        </div>
                        
                        <!-- How it Works -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h5 class="text-center mb-4" style="color: #667eea;">How It Works</h5>
                                <div class="row g-3">
                                    <div class="col-md-3 text-center">
                                        <div class="feature-icon" style="width: 60px; height: 60px;">
                                            <span class="fw-bold">1</span>
                                        </div>
                                        <h6>Choose Plan</h6>
                                        <p class="small text-muted">Select your preferred data plan</p>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="feature-icon" style="width: 60px; height: 60px;">
                                            <span class="fw-bold">2</span>
                                        </div>
                                        <h6>Pay Securely</h6>
                                        <p class="small text-muted">Pay via M-Pesa or Mobile Money</p>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="feature-icon" style="width: 60px; height: 60px;">
                                            <span class="fw-bold">3</span>
                                        </div>
                                        <h6>Get Voucher</h6>
                                        <p class="small text-muted">Receive voucher code via SMS</p>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <div class="feature-icon" style="width: 60px; height: 60px;">
                                            <span class="fw-bold">4</span>
                                        </div>
                                        <h6>Connect</h6>
                                        <p class="small text-muted">Enter code and start browsing</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="text-white-50">
                    <i class="bi bi-telephone me-2"></i>Support: +254700000000 
                    <span class="mx-3">|</span>
                    <i class="bi bi-envelope me-2"></i>help@hotspotbilling.com
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
