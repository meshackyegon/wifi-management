@section('title', 'Voucher Details')
<x-layouts.app title="Voucher Details">

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Voucher Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('vouchers.index') }}">Vouchers</a></li>
                            <li class="breadcrumb-item active">{{ $voucher->code }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="printVoucher()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Voucher Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Voucher Information</h6>
                    @switch($voucher->status)
                        @case('generated')
                            <span class="badge bg-secondary fs-6">Generated</span>
                            @break
                        @case('printed')
                            <span class="badge bg-primary fs-6">Printed</span>
                            @break
                        @case('sold')
                            <span class="badge bg-warning fs-6">Sold</span>
                            @break
                        @case('used')
                            <span class="badge bg-success fs-6">Used</span>
                            @break
                        @case('expired')
                            <span class="badge bg-danger fs-6">Expired</span>
                            @break
                    @endswitch
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Voucher Code:</td>
                                    <td>
                                        <code class="fs-5 bg-light p-2 rounded">{{ $voucher->code }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                                onclick="copyToClipboard('{{ $voucher->code }}')" title="Copy Code">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Password:</td>
                                    <td>
                                        <code class="fs-5 bg-light p-2 rounded">{{ $voucher->password }}</code>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                                onclick="copyToClipboard('{{ $voucher->password }}')" title="Copy Password">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Plan:</td>
                                    <td>
                                        <span class="badge bg-info fs-6">{{ $voucher->voucherPlan->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Duration:</td>
                                    <td>{{ $voucher->voucherPlan->formatted_duration }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Data Limit:</td>
                                    <td>{{ $voucher->voucherPlan->formatted_data_limit }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Price:</td>
                                    <td class="fs-5 fw-bold text-success">${{ number_format($voucher->price, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Router:</td>
                                    <td>
                                        @if($voucher->router)
                                            {{ $voucher->router->name }}
                                            <br><small class="text-muted">{{ $voucher->router->location }}</small>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Generated By:</td>
                                    <td>
                                        {{ $voucher->user?->name ?? 'System' }}
                                        <br><small class="text-muted">{{ $voucher->created_at->format('M d, Y H:i:s') }}</small>
                                    </td>
                                </tr>
                                @if($voucher->used_at)
                                <tr>
                                    <td class="fw-bold">Used At:</td>
                                    <td>
                                        {{ $voucher->used_at->format('M d, Y H:i:s') }}
                                        <br><small class="text-muted">{{ $voucher->used_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endif
                                @if($voucher->expires_at)
                                <tr>
                                    <td class="fw-bold">Expires At:</td>
                                    <td>
                                        {{ $voucher->expires_at->format('M d, Y H:i:s') }}
                                        <br>
                                        @if($voucher->expires_at->isPast())
                                            <small class="text-danger">Expired {{ $voucher->expires_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">{{ $voucher->expires_at->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($voucher->phone_number)
                                <tr>
                                    <td class="fw-bold">Phone Number:</td>
                                    <td>{{ $voucher->phone_number }}</td>
                                </tr>
                                @endif
                                @if($voucher->mac_address)
                                <tr>
                                    <td class="fw-bold">MAC Address:</td>
                                    <td><code>{{ $voucher->mac_address }}</code></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            @if($voucher->status === 'used' || $voucher->smsLogs->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Usage History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Voucher Generation -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Voucher Generated</h6>
                                <p class="timeline-description">
                                    Generated by {{ $voucher->user?->name ?? 'System' }}
                                </p>
                                <small class="timeline-date">{{ $voucher->created_at->format('M d, Y H:i:s') }}</small>
                            </div>
                        </div>

                        <!-- SMS Logs -->
                        @foreach($voucher->smsLogs as $smsLog)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">SMS Sent</h6>
                                <p class="timeline-description">
                                    Sent to {{ $smsLog->phone_number }}
                                    <br>Status: 
                                    @if($smsLog->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($smsLog->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </p>
                                <small class="timeline-date">{{ $smsLog->sent_at?->format('M d, Y H:i:s') ?? 'Pending' }}</small>
                            </div>
                        </div>
                        @endforeach

                        <!-- Voucher Usage -->
                        @if($voucher->used_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Voucher Used</h6>
                                <p class="timeline-description">
                                    Used by {{ $voucher->phone_number ?? 'Unknown user' }}
                                    @if($voucher->mac_address)
                                        <br>Device: <code>{{ $voucher->mac_address }}</code>
                                    @endif
                                </p>
                                <small class="timeline-date">{{ $voucher->used_at->format('M d, Y H:i:s') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Mobile Money Payments -->
            @if($voucher->mobileMoneyPayments->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Provider</th>
                                    <th>Phone</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($voucher->mobileMoneyPayments as $payment)
                                <tr>
                                    <td><code>{{ $payment->transaction_id }}</code></td>
                                    <td>{{ ucfirst($payment->provider) }}</td>
                                    <td>{{ $payment->phone_number }}</td>
                                    <td>${{ number_format($payment->amount, 2) }}</td>
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
                                        @endswitch
                                    </td>
                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions & QR Code -->
        <div class="col-md-4">
            <!-- QR Code -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">QR Code</h6>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <small class="text-muted">Scan to use voucher credentials</small>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="printVoucher()">
                            <i class="fas fa-print"></i> Print Voucher
                        </button>
                        
                        @if($voucher->status === 'generated' || $voucher->status === 'printed')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#sendSmsModal">
                            <i class="fas fa-sms"></i> Send via SMS
                        </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-primary" onclick="copyVoucherInfo()">
                            <i class="fas fa-copy"></i> Copy Info
                        </button>
                    </div>
                </div>
            </div>

            <!-- Plan Details -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Plan Details</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $voucher->voucherPlan->name }}</h6>
                    <p class="text-muted mb-3">{{ $voucher->voucherPlan->description }}</p>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-primary mb-1">{{ $voucher->voucherPlan->formatted_duration }}</h5>
                                <small class="text-muted">Duration</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-info mb-1">{{ $voucher->voucherPlan->formatted_data_limit }}</h5>
                            <small class="text-muted">Data Limit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send SMS Modal -->
@if($voucher->status === 'generated' || $voucher->status === 'printed')
<div class="modal fade" id="sendSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vouchers.send-sms', $voucher) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Send Voucher via SMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                               placeholder="+1234567890" required>
                        <div class="form-text">Include country code (e.g., +1234567890)</div>
                    </div>
                    <div class="alert alert-info">
                        <h6>SMS will contain:</h6>
                        <ul class="mb-0">
                            <li>Voucher Code: {{ $voucher->code }}</li>
                            <li>Password: {{ $voucher->password }}</li>
                            <li>Plan: {{ $voucher->voucherPlan->name }}</li>
                            <li>Duration: {{ $voucher->voucherPlan->formatted_duration }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send SMS</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR Code
    const qrData = JSON.stringify({
        code: '{{ $voucher->code }}',
        password: '{{ $voucher->password }}',
        plan: '{{ $voucher->voucherPlan->name }}',
        duration: '{{ $voucher->voucherPlan->formatted_duration }}',
        dataLimit: '{{ $voucher->voucherPlan->formatted_data_limit }}'
    });

    QRCode.toCanvas(document.getElementById('qrcode'), qrData, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    });
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    Copied to clipboard!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(() => document.body.removeChild(toast), 3000);
    });
}

function copyVoucherInfo() {
    const info = `Voucher Code: {{ $voucher->code }}
Password: {{ $voucher->password }}
Plan: {{ $voucher->voucherPlan->name }}
Duration: {{ $voucher->voucherPlan->formatted_duration }}
Data Limit: {{ $voucher->voucherPlan->formatted_data_limit }}
Price: ${{ number_format($voucher->price, 2) }}`;
    
    copyToClipboard(info);
}

function printVoucher() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("vouchers.print") }}';
    form.target = '_blank';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'voucher_ids[]';
    input.value = '{{ $voucher->id }}';
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-description {
    font-size: 13px;
    margin-bottom: 5px;
    color: #6c757d;
}

.timeline-date {
    font-size: 12px;
    color: #adb5bd;
}
</style>
@endpush
</x-layouts.app>
