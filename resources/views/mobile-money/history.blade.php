@section('title', 'Payment History')
<x-layouts.app title="Payment History">

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Payment History</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('mobile-money.export') }}" class="btn btn-outline-primary">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mobile-money.history') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="provider" class="form-label">Provider</label>
                        <select class="form-select" id="provider" name="provider">
                            <option value="">All Providers</option>
                            <option value="mtn" {{ request('provider') === 'mtn' ? 'selected' : '' }}>MTN</option>
                            <option value="airtel" {{ request('provider') === 'airtel' ? 'selected' : '' }}>Airtel</option>
                            <option value="vodacom" {{ request('provider') === 'vodacom' ? 'selected' : '' }}>Vodacom</option>
                            <option value="tigo" {{ request('provider') === 'tigo' ? 'selected' : '' }}>Tigo</option>
                            <option value="mpesa" {{ request('provider') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="amount_min" class="form-label">Min Amount</label>
                        <input type="number" class="form-control" id="amount_min" name="amount_min" 
                               step="0.01" value="{{ request('amount_min') }}" placeholder="0.00">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('mobile-money.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $payments->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Successful
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $payments->where('status', 'completed')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $payments->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('mobile-money.export') . '?' . request()->getQueryString() }}">
                        <i class="fas fa-download"></i> Export Filtered
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('mobile-money.stats') }}">
                        <i class="fas fa-chart-bar"></i> View Statistics
                    </a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Provider</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Voucher</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <code class="text-primary">{{ $payment->transaction_id }}</code>
                                    </td>
                                    <td>
                                        {{ $payment->customer_name }}
                                        @if($payment->email)
                                            <br><small class="text-muted">{{ $payment->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $payment->phone_number }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ strtoupper($payment->provider) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        @switch($payment->status)
                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">Failed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($payment->voucher)
                                            <a href="{{ route('vouchers.show', $payment->voucher) }}" 
                                               class="text-primary">
                                                {{ $payment->voucher->code }}
                                            </a>
                                            <br><small class="text-muted">{{ $payment->voucher->voucherPlan->name }}</small>
                                        @else
                                            <span class="text-muted">No voucher</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $payment->created_at->format('M d, Y H:i') }}
                                        <br><small class="text-muted">{{ $payment->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($payment->status === 'failed' && auth()->user()->isAdmin())
                                                <form action="{{ route('mobile-money.retry', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                            title="Retry Payment">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($payment->voucher)
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="resendVoucher('{{ $payment->voucher->id }}')" 
                                                        title="Resend Voucher SMS">
                                                    <i class="fas fa-sms"></i>
                                                </button>
                                            @endif
                                            
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="viewPaymentDetails('{{ $payment->id }}')" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                    </div>
                    <div>
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No payment history found</h5>
                    <p class="text-muted">Payments will appear here once customers start purchasing vouchers.</p>
                    <a href="{{ route('voucher.buy') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Make a Test Purchase
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Payment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewPaymentDetails(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
    const content = document.getElementById('paymentDetailsContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading payment details...</p>
        </div>
    `;
    
    modal.show();
    
    // Here you would fetch payment details via AJAX
    // For now, we'll show a placeholder
    setTimeout(() => {
        content.innerHTML = `
            <div class="alert alert-info">
                <h6>Payment Details</h6>
                <p>Payment ID: ${paymentId}</p>
                <p>This would show detailed information about the payment including:</p>
                <ul>
                    <li>Full transaction history</li>
                    <li>Provider response details</li>
                    <li>Error messages (if any)</li>
                    <li>Retry attempts</li>
                </ul>
            </div>
        `;
    }, 1000);
}

function resendVoucher(voucherId) {
    if (confirm('Are you sure you want to resend the voucher SMS?')) {
        // Here you would make an AJAX call to resend the SMS
        fetch(`/vouchers/${voucherId}/resend-sms`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('SMS sent successfully!');
            } else {
                alert('Failed to send SMS: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending SMS.');
        });
    }
}

// Auto-refresh pending payments every 30 seconds
setInterval(function() {
    const url = new URL(window.location);
    const params = new URLSearchParams(url.search);
    
    // Only refresh if viewing pending payments
    if (params.get('status') === 'pending' || !params.get('status')) {
        // Add a refresh parameter to avoid browser cache
        params.set('refresh', Date.now());
        
        fetch(url.pathname + '?' + params.toString())
            .then(response => response.text())
            .then(html => {
                // Parse the response and update the table
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('.table-responsive');
                const currentTable = document.querySelector('.table-responsive');
                
                if (newTable && currentTable) {
                    currentTable.innerHTML = newTable.innerHTML;
                }
            })
            .catch(error => {
                console.error('Auto-refresh error:', error);
            });
    }
}, 30000); // 30 seconds
</script>
@endpush
</x-layouts.app>
