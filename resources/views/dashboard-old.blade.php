@section('title', __('Dashboard'))
<x-layouts.app :title="__('Dashboard')">
    
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
        </div>
    </div>

    <!-- Stats Cards Row 1 -->
    <div class="row g-3 mb-4">
        @if(auth()->user()->isAdmin())
            <!-- SMS Balance -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">SMS Balance</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['sms_balance'] ?? 4930) }}</h3>
                                <small class="text-white-75">
                                    <span class="text-success">▲</span> Buy SMS 📱
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-chat-dots fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Money Balance -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Mobile Money Balance</h6>
                                <h3 class="mb-1 fw-bold">KES {{ number_format($stats['mobile_money_balance'] ?? 950) }}</h3>
                                <small class="text-white-75">
                                    <span class="text-success">▲</span> Withdraw 💰
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-phone fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Transacted -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Daily Transacted</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['daily_transactions'] ?? 0) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Transacted -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Weekly Transacted</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['weekly_transactions'] ?? 1000) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-calendar-week fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Stats Cards Row 2 -->
    <div class="row g-3 mb-4">
        @if(auth()->user()->isAdmin())
            <!-- Monthly Transacted -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Monthly Transacted</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['monthly_transactions'] ?? 1000) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-calendar-month fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Transacted -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Transacted</h6>
                                <h3 class="mb-1 fw-bold text-dark">{{ number_format($stats['total_transactions'] ?? 1000) }}</h3>
                                <small class="text-muted">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-bar-chart fs-1 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Revenue</h6>
                                <h3 class="mb-1 fw-bold text-dark">KES {{ number_format($stats['total_revenue'] ?? 1000) }}</h3>
                                <small class="text-muted">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-currency-exchange fs-1 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Count -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Transactions</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['transactions_count'] ?? 3) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-receipt fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Stats Cards Row 3 -->
    <div class="row g-3 mb-4">
        @if(auth()->user()->isAdmin())
            <!-- Categories -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Categories</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['categories_count'] ?? 3) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-grid fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Packages -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Packages</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['packages_count'] ?? 3) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-box fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">SMS</h6>
                                <h3 class="mb-1 fw-bold">{{ number_format($stats['sms_sent'] ?? 2) }}</h3>
                                <small class="text-white-75">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-envelope fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vouchers -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Vouchers</h6>
                                <h3 class="mb-1 fw-bold text-dark">{{ number_format($stats['total_vouchers'] ?? 600) }}</h3>
                                <small class="text-muted">
                                    More info ℹ️
                                </small>
                            </div>
                            <div class="opacity-75">
                                <i class="bi bi-ticket-perforated fs-1 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
                                <i class="bi bi-people fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">SMS Sent</h6>
                                <h3 class="mb-0">{{ number_format($stats['sms_sent_today']) }}</h3>
                                <small class="opacity-75">Today</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-chat-text fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->isAgent())
            <!-- Agent Stats -->
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">My Balance</h6>
                                <h3 class="mb-0">${{ number_format($stats['my_commission'], 2) }}</h3>
                                <small class="opacity-75">Available commission</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-wallet fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Vouchers Sold</h6>
                                <h3 class="mb-0">{{ number_format($stats['vouchers_sold']) }}</h3>
                                <small class="opacity-75">Total: {{ $stats['my_vouchers'] }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-ticket-perforated fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Monthly Commission</h6>
                                <h3 class="mb-0">${{ number_format($stats['monthly_commission'], 2) }}</h3>
                                <small class="opacity-75">This month</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-graph-up fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Pending Vouchers</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending_vouchers']) }}</h3>
                                <small class="opacity-75">Unused</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Customer Stats -->
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Vouchers Purchased</h6>
                                <h3 class="mb-0">{{ number_format($stats['vouchers_purchased']) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-ticket fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Spent</h6>
                                <h3 class="mb-0">${{ number_format($stats['total_spent'], 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-currency-dollar fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Vouchers</h6>
                                <h3 class="mb-0">{{ number_format($stats['active_vouchers']) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-2 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Charts and Analytics -->
    @if(auth()->user()->isAdmin() || auth()->user()->isAgent())
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Trend (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Providers</h5>
                </div>
                <div class="card-body">
                    <canvas id="providersChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Vouchers</h5>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentVouchers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentVouchers->take(5) as $voucher)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $voucher->code }}</h6>
                                        <small class="text-muted">{{ $voucher->voucherPlan->name }} - ${{ $voucher->price }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $voucher->status === 'active' ? 'success' : ($voucher->status === 'used' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($voucher->status) }}
                                        </span>
                                        <small class="d-block text-muted">{{ $voucher->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-ticket fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No vouchers yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Payments</h5>
                    <a href="{{ route('mobile-money.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPayments->take(5) as $payment)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $payment->phone_number }}</h6>
                                        <small class="text-muted">{{ $payment->provider_display_name }} - ${{ $payment->amount }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $payment->status === 'success' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        <small class="d-block text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-credit-card fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No payments yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if(auth()->user()->isAdmin() || auth()->user()->isAgent())
                            <div class="col-md-3">
                                <a href="{{ route('vouchers.create') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Generate Vouchers
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('vouchers.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-list me-2"></i>Manage Vouchers
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                            <div class="col-md-3">
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-outline-success w-100">
                                    <i class="bi bi-gear me-2"></i>Manage Plans
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('analytics') }}" class="btn btn-outline-info w-100">
                                    <i class="bi bi-graph-up me-2"></i>View Analytics
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->isCustomer())
                            <div class="col-md-6">
                                <a href="{{ route('voucher.buy') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-cart-plus me-2"></i>Buy Voucher
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('mobile-money.history') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-clock-history me-2"></i>Purchase History
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @if(isset($chartsData))
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json(collect($chartsData['revenue'])->pluck('date')),
                    datasets: [{
                        label: 'Revenue',
                        data: @json(collect($chartsData['revenue'])->pluck('amount')),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Providers Chart
        const providersCtx = document.getElementById('providersChart');
        if (providersCtx) {
            new Chart(providersCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartsData['providers']->pluck('provider')),
                    datasets: [{
                        data: @json($chartsData['providers']->pluck('total')),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        @endif
    </script>
    @endpush
</x-layouts.app>
