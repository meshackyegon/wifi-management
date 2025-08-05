@extends('layouts.app')

@section('title', 'Cash Payments Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">💰 Cash Payments</h1>
                    <p class="text-muted">Manage cash payments and complete transactions</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Pending Cash Payments</div>
                                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Completed Payments</div>
                                    <div class="h4 mb-0">{{ $stats['completed'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-info" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="text-muted small">Total Cash Collected</div>
                                    <div class="h4 mb-0">KES {{ number_format($stats['total_amount'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Filter by Status</label>
                            <select class="form-select" id="statusFilter" onchange="filterPayments()">
                                <option value="">All Statuses</option>
                                <option value="pending_cash" {{ request('status') == 'pending_cash' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cash Payment Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Plan</th>
                                    <th>Phone Number</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Received By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <code>{{ $payment->transaction_id }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $payment->voucherPlan->name }}</strong><br>
                                        <small class="text-muted">{{ $payment->voucherPlan->data_limit }}</small>
                                    </td>
                                    <td>{{ $payment->phone_number }}</td>
                                    <td>
                                        <strong>KES {{ number_format($payment->amount, 2) }}</strong>
                                        @if($payment->change_given > 0)
                                            <br><small class="text-muted">Change: KES {{ number_format($payment->change_given, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'pending_cash')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @elseif($payment->status === 'success')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Completed
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $payment->created_at->format('M j, Y') }}<br>
                                        <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($payment->cashReceiver)
                                            <strong>{{ $payment->cashReceiver->name }}</strong><br>
                                            <small class="text-muted">{{ $payment->cash_received_at?->format('M j, Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status === 'pending_cash')
                                            <button type="button" 
                                                    class="btn btn-success btn-sm" 
                                                    onclick="openCompleteCashModal({{ $payment->id }}, '{{ $payment->transaction_id }}', {{ $payment->amount }})">
                                                <i class="fas fa-money-bill"></i> Complete Payment
                                            </button>
                                        @elseif($payment->voucher)
                                            <button type="button" 
                                                    class="btn btn-info btn-sm" 
                                                    onclick="showVoucherDetails('{{ $payment->voucher->code }}', '{{ $payment->voucher->username }}', '{{ $payment->voucher->password }}')">
                                                <i class="fas fa-ticket-alt"></i> View Voucher
                                            </button>
                                        @endif
                                        
                                        @if($payment->payment_notes)
                                            <button type="button" 
                                                    class="btn btn-outline-secondary btn-sm" 
                                                    onclick="showNotes('{{ addslashes($payment->payment_notes) }}')">
                                                <i class="fas fa-sticky-note"></i> Notes
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-money-bill text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No cash payments found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($payments->hasPages())
                <div class="card-footer">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Complete Cash Payment Modal -->
<div class="modal fade" id="completeCashModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Cash Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="completeCashForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Transaction ID: <strong id="modalTransactionId"></strong><br>
                        Required Amount: <strong>KES <span id="modalRequiredAmount"></span></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amountReceived" class="form-label">Amount Received *</label>
                        <div class="input-group">
                            <span class="input-group-text">KES</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="amountReceived" 
                                   name="amount_received" 
                                   step="0.01" 
                                   min="0" 
                                   required>
                        </div>
                        <div class="form-text">Enter the exact amount of cash received from customer</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="paymentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" 
                                  id="paymentNotes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Add any additional notes about this payment..."></textarea>
                    </div>
                    
                    <div id="changeCalculation" class="alert alert-success" style="display: none;">
                        <i class="fas fa-calculator"></i>
                        Change to give: <strong>KES <span id="changeAmount">0.00</span></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="completeCashBtn">
                        <i class="fas fa-check"></i> Complete Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Voucher Details Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Voucher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Voucher Code:</strong><br>
                        <code id="voucherCode" class="fs-5"></code>
                    </div>
                    <div class="col-md-6">
                        <strong>Username:</strong><br>
                        <code id="voucherUsername" class="fs-5"></code>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Password:</strong><br>
                        <code id="voucherPassword" class="fs-5"></code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="notesContent"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-responsive {
    border-radius: 0.375rem;
}

code {
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentPaymentId = null;
let requiredAmount = 0;

function filterPayments() {
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location = url;
}

function clearFilters() {
    const url = new URL(window.location);
    url.searchParams.delete('status');
    window.location = url;
}

function openCompleteCashModal(paymentId, transactionId, amount) {
    currentPaymentId = paymentId;
    requiredAmount = amount;
    
    document.getElementById('modalTransactionId').textContent = transactionId;
    document.getElementById('modalRequiredAmount').textContent = amount.toFixed(2);
    document.getElementById('amountReceived').value = amount.toFixed(2);
    
    calculateChange();
    
    const modal = new bootstrap.Modal(document.getElementById('completeCashModal'));
    modal.show();
}

function calculateChange() {
    const received = parseFloat(document.getElementById('amountReceived').value) || 0;
    const change = Math.max(0, received - requiredAmount);
    
    document.getElementById('changeAmount').textContent = change.toFixed(2);
    
    const changeDiv = document.getElementById('changeCalculation');
    if (change > 0) {
        changeDiv.style.display = 'block';
    } else {
        changeDiv.style.display = 'none';
    }
}

function showVoucherDetails(code, username, password) {
    document.getElementById('voucherCode').textContent = code;
    document.getElementById('voucherUsername').textContent = username;
    document.getElementById('voucherPassword').textContent = password;
    
    const modal = new bootstrap.Modal(document.getElementById('voucherModal'));
    modal.show();
}

function showNotes(notes) {
    document.getElementById('notesContent').textContent = notes;
    
    const modal = new bootstrap.Modal(document.getElementById('notesModal'));
    modal.show();
}

// Calculate change when amount changes
document.getElementById('amountReceived').addEventListener('input', calculateChange);

// Handle form submission
document.getElementById('completeCashForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('completeCashBtn');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch(`/mobile-money/cash-payments/${currentPaymentId}/complete`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cash payment completed successfully!' + 
                  (data.voucher_code ? '\nVoucher Code: ' + data.voucher_code : '') +
                  (data.change_given > 0 ? '\nChange Given: KES ' + data.change_given.toFixed(2) : ''));
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the payment');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Complete Payment';
    });
});
</script>
@endpush
