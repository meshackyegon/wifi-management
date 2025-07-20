@section('title', __('Dashboard'))
<x-layouts.app :title="__('Dashboard')">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        @if(auth()->user()->isAdmin())
            <!-- Admin Stats -->
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Revenue</h6>
                                <h3 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h3>
                                <small class="opacity-75">Today: ${{ number_format($stats['revenue_today'], 2) }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-currency-dollar fs-2 opacity-75"></i>
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
                                <h6 class="card-title">Total Vouchers</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_vouchers']) }}</h3>
                                <small class="opacity-75">Today: {{ number_format($stats['vouchers_generated_today']) }}</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-ticket fs-2 opacity-75"></i>
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
                                <h6 class="card-title">Active Users</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_users']) }}</h3>
                                <small class="opacity-75">Routers: {{ $stats['active_routers'] }}</small>
                            </div>
                            <div class="align-self-center">
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
