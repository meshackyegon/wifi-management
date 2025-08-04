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

    <!-- Action Buttons & Quick Stats -->
    <div class="row g-3 mb-4">
        @if(auth()->user()->isAdmin() || auth()->user()->isAgent())
            <div class="col-md-8">
                <!-- Charts and Activity -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        @if($stats['recent_transactions'] ?? false)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Customer</th>
                                            <th>Plan</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(collect($stats['recent_transactions'])->take(5) as $transaction)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('M d, Y') }}</td>
                                                <td>{{ $transaction['customer_name'] ?? 'N/A' }}</td>
                                                <td>{{ $transaction['plan_name'] ?? 'N/A' }}</td>
                                                <td><span class="badge bg-success">KES {{ number_format($transaction['amount'] ?? 0) }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction['status'] === 'completed' ? 'success' : ($transaction['status'] === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($transaction['status'] ?? 'pending') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No recent transactions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Manage Plans
                                </a>
                                <a href="{{ route('vouchers.index') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-ticket me-1"></i> View Vouchers
                                </a>
                                <a href="{{ route('routers.index') }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-router me-1"></i> Manage Routers
                                </a>
                                <a href="{{ route('users.index') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-people me-1"></i> Manage Users
                                </a>
                            @elseif(auth()->user()->isAgent())
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i> View Plans
                                </a>
                                <a href="{{ route('vouchers.index') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-ticket me-1"></i> View Vouchers
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">System Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">SMS Service</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Payment Gateway</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Router API</span>
                            <span class="badge bg-{{ ($stats['active_routers'] ?? 0) > 0 ? 'success' : 'warning' }}">
                                {{ ($stats['active_routers'] ?? 0) > 0 ? 'Connected' : 'Checking' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Database</span>
                            <span class="badge bg-success">Healthy</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Customer View -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-wifi fs-1 mb-2"></i>
                                <h5>Buy Internet Vouchers</h5>
                                <p class="mb-3">Get instant access to high-speed internet</p>
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-light">
                                    <i class="bi bi-cart me-1"></i> Browse Plans
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-success text-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-phone fs-1 mb-2"></i>
                                <h5>Mobile Money Payment</h5>
                                <p class="mb-3">Pay easily with M-Pesa, Airtel Money & more</p>
                                <a href="{{ route('voucher-plans.index') }}" class="btn btn-light">
                                    <i class="bi bi-credit-card me-1"></i> Pay Now
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-info text-white shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-clock-history fs-1 mb-2"></i>
                                <h5>Purchase History</h5>
                                <p class="mb-3">View your voucher purchase history</p>
                                <a href="{{ route('history') }}" class="btn btn-light">
                                    <i class="bi bi-list me-1"></i> View History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(auth()->user()->isAdmin())
        <!-- Recent SMS Notifications Section -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent SMS Notifications</h5>
                        <small class="text-muted">Admin notifications & voucher deliveries</small>
                    </div>
                    <div class="card-body">
                        @if($stats['recent_sms_logs'] ?? false)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Phone</th>
                                            <th>Type</th>
                                            <th>Voucher Code</th>
                                            <th>Provider</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(collect($stats['recent_sms_logs'])->take(10) as $sms)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($sms['created_at'])->format('M d, H:i') }}</td>
                                                <td>
                                                    <span class="fw-medium">{{ $sms['phone'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $sms['message_type'] === 'Voucher SMS' ? 'info' : 'primary' }} rounded-pill">
                                                        {{ $sms['message_type'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <code class="text-primary">{{ $sms['voucher_code'] }}</code>
                                                </td>
                                                <td>
                                                    <small class="text-uppercase text-muted">{{ $sms['provider'] }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $sms['status'] === 'sent' ? 'success' : ($sms['status'] === 'pending' ? 'warning' : 'danger') }} rounded-pill">
                                                        {{ ucfirst($sms['status']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-chat-dots fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No SMS notifications yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->isAdmin())
        <!-- Analytics Charts Row -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Revenue Trend (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Voucher Sales (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="voucherChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Chart.js Scripts -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            @if(auth()->user()->isAdmin())
                // Revenue Chart
                const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
                        datasets: [{
                            label: 'Revenue (KES)',
                            data: {!! json_encode($stats['revenue_chart'] ?? [100, 150, 200, 175, 300, 250, 400]) !!},
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.4,
                            fill: true
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
                                        return 'KES ' + value;
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Voucher Chart
                const voucherCtx = document.getElementById('voucherChart').getContext('2d');
                new Chart(voucherCtx, {
                    type: 'bar',
                    data: {
                        labels: ['6 days ago', '5 days ago', '4 days ago', '3 days ago', '2 days ago', 'Yesterday', 'Today'],
                        datasets: [{
                            label: 'Vouchers Sold',
                            data: {!! json_encode($stats['voucher_chart'] ?? [5, 8, 12, 9, 15, 11, 18]) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            @endif
        </script>
    @endpush

</x-layouts.app>
