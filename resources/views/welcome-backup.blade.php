<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed" data-base-url="{{url('/')}}" data-framework="laravel">
  @section('title', __('WiFi Hotspot Billing System'))
  <head>
    @include('partials.head')
    <style>
      .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
      }
      .feature-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .feature-card:hover {
        transform: translateY(-5px);
      }
      .stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }
    </style>
  </head>
  <body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
          <i class="bx bx-wifi me-2"></i>HotSpot Billing
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="#features">Features</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#pricing">Plans</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact">Contact</a>
            </li>
          </ul>
          
          <ul class="navbar-nav">
            @auth
              <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                  <i class="bx bx-tachometer me-1"></i>Dashboard
                </a>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link" href="{{ route('voucher.buy') }}">
                  <i class="bx bx-cart-add me-1"></i>Buy Voucher
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">Login</a>
              </li>
              <li class="nav-item">
                <a class="nav-link btn btn-primary text-white ms-2 px-3" href="{{ route('register') }}">
                  Register
                </a>
              </li>
            @endauth
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 text-white">
            <h1 class="display-4 fw-bold mb-4">
              Complete WiFi Hotspot Billing Solution
            </h1>
            <p class="lead mb-4">
              Manage your WiFi business with integrated mobile money payments, SMS voucher delivery, 
              and comprehensive router management across Africa.
            </p>
            
            <div class="d-flex flex-wrap gap-3 mb-5">
              <a href="{{ route('voucher.buy') }}" class="btn btn-light btn-lg">
                <i class="bx bx-cart-add me-2"></i>Buy Voucher Now
              </a>
              @guest
              <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                <i class="bx bx-user-plus me-2"></i>Become an Agent
              </a>
              @endguest
            </div>

            <!-- Stats -->
            <div class="row g-3">
              <div class="col-md-4">
                <div class="stat-card rounded p-3 text-center">
                  <h3 class="h4 mb-0">5+</h3>
                  <small>Mobile Money Providers</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-card rounded p-3 text-center">
                  <h3 class="h4 mb-0">24/7</h3>
                  <small>Support</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-card rounded p-3 text-center">
                  <h3 class="h4 mb-0">3%</h3>
                  <small>Commission Rate</small>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <div class="text-center">
              <img src="{{asset('assets/img/illustrations/wifi-network.png')}}" 
                   alt="WiFi Network" 
                   class="img-fluid"
                   style="max-height: 400px;"
                   onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDQwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0iIzY2N2VlYSI+PGNpcmNsZSBjeD0iMjAwIiBjeT0iMTUwIiByPSIxMDAiIG9wYWNpdHk9IjAuMSIvPjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE1MCIgcj0iNzAiIG9wYWNpdHk9IjAuMiIvPjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE1MCIgcj0iNDAiIG9wYWNpdHk9IjAuMyIvPjxjaXJjbGUgY3g9IjIwMCIgY3k9IjE1MCIgcj0iMTAiLz48L2c+PC9zdmc+'">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
      <div class="container">
        <div class="row mb-5">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="display-5 fw-bold mb-3">Everything You Need</h2>
            <p class="lead text-muted">
              Complete hotspot billing solution with African mobile money integration
            </p>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-mobile-alt text-white fs-4"></i>
                </div>
                <h5 class="card-title">Mobile Money Integration</h5>
                <p class="card-text text-muted">
                  Support for MTN, Airtel, M-Pesa, Tigo Pesa and all major African mobile money providers
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-message-dots text-white fs-4"></i>
                </div>
                <h5 class="card-title">SMS Voucher Delivery</h5>
                <p class="card-text text-muted">
                  Automatic SMS delivery of voucher codes via Africa's Talking and Twilio
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-router text-white fs-4"></i>
                </div>
                <h5 class="card-title">Router Management</h5>
                <p class="card-text text-muted">
                  Remote MikroTik and CoovaChilli configuration with hotspot sharing prevention
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-receipt text-white fs-4"></i>
                </div>
                <h5 class="card-title">Voucher Generation</h5>
                <p class="card-text text-muted">
                  Bulk voucher generation, printing, and automated distribution
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-danger bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-line-chart text-white fs-4"></i>
                </div>
                <h5 class="card-title">Analytics & Reports</h5>
                <p class="card-text text-muted">
                  Comprehensive reporting, revenue tracking, and business analytics
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="feature-card card h-100">
              <div class="card-body text-center p-4">
                <div class="bg-secondary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                  <i class="bx bx-shield-check text-white fs-4"></i>
                </div>
                <h5 class="card-title">User Verification</h5>
                <p class="card-text text-muted">
                  National ID and passport verification for secure user registration
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing/Plans Section -->
    <section id="pricing" class="py-5 bg-light">
      <div class="container">
        <div class="row mb-5">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="display-5 fw-bold mb-3">Available Plans</h2>
            <p class="lead text-muted">
              Choose the perfect plan for your internet needs
            </p>
          </div>
        </div>

        <div class="row g-4 justify-content-center" id="voucher-plans">
          <!-- Plans will be loaded here via AJAX -->
          <div class="col-md-4">
            <div class="card feature-card">
              <div class="card-body text-center p-4">
                <h5 class="card-title">Hourly Plans</h5>
                <p class="text-muted">Perfect for short browsing sessions</p>
                <div class="fs-4 fw-bold text-primary mb-3">Starting from KES 100</div>
                <ul class="list-unstyled">
                  <li><i class="bx bx-check text-success me-2"></i>1-24 hour durations</li>
                  <li><i class="bx bx-check text-success me-2"></i>High-speed internet</li>
                  <li><i class="bx bx-check text-success me-2"></i>Instant activation</li>
                </ul>
                <a href="{{ route('voucher.buy') }}" class="btn btn-primary w-100 mt-3">
                  View Plans
                </a>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card feature-card border-primary">
              <div class="card-header bg-primary text-white text-center">
                <h6 class="mb-0">Most Popular</h6>
              </div>
              <div class="card-body text-center p-4">
                <h5 class="card-title">Daily Plans</h5>
                <p class="text-muted">Great for regular users</p>
                <div class="fs-4 fw-bold text-primary mb-3">Starting from KES 300</div>
                <ul class="list-unstyled">
                  <li><i class="bx bx-check text-success me-2"></i>1-7 day durations</li>
                  <li><i class="bx bx-check text-success me-2"></i>Unlimited data options</li>
                  <li><i class="bx bx-check text-success me-2"></i>Priority support</li>
                </ul>
                <a href="{{ route('voucher.buy') }}" class="btn btn-primary w-100 mt-3">
                  View Plans
                </a>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card feature-card">
              <div class="card-body text-center p-4">
                <h5 class="card-title">Weekly Plans</h5>
                <p class="text-muted">Best value for extended use</p>
                <div class="fs-4 fw-bold text-primary mb-3">Starting from KES 1000</div>
                <ul class="list-unstyled">
                  <li><i class="bx bx-check text-success me-2"></i>7-30 day durations</li>
                  <li><i class="bx bx-check text-success me-2"></i>Maximum data allowance</li>
                  <li><i class="bx bx-check text-success me-2"></i>24/7 support</li>
                </ul>
                <a href="{{ route('voucher.buy') }}" class="btn btn-primary w-100 mt-3">
                  View Plans
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="display-5 fw-bold mb-3">Get Started Today</h2>
            <p class="lead text-muted mb-4">
              Join thousands of satisfied customers and start your WiFi business
            </p>
            
            <div class="d-flex flex-wrap justify-content-center gap-3">
              <a href="{{ route('voucher.buy') }}" class="btn btn-primary btn-lg">
                <i class="bx bx-cart-add me-2"></i>Buy Your First Voucher
              </a>
              @guest
              <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                <i class="bx bx-user-plus me-2"></i>Become an Agent
              </a>
              @endguest
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h6>HotSpot Billing System</h6>
            <p class="text-muted small mb-0">
              Complete WiFi hotspot billing solution for Africa
            </p>
          </div>
          <div class="col-md-6 text-md-end">
            <small class="text-muted">
              © {{ date('Y') }} HotSpot Billing. All rights reserved.
            </small>
          </div>
        </div>
      </div>
    </footer>

    <!-- Include Scripts -->
    @include('partials.scripts')
    
    <script>
      // Smooth scrolling for navigation links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });

      // Load available voucher plans
      document.addEventListener('DOMContentLoaded', function() {
        // This can be enhanced to load actual plans via AJAX
        console.log('Welcome page loaded');
      });
    </script>
  </body>
</html>
