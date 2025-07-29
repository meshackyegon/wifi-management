@section('title', 'SMS Logs')
@php
    use Illuminate\Support\Str;
@endphp
<x-layouts.app title="SMS Logs">

<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white shadow-lg border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h1 class="h2 mb-2 text-white">📊 SMS Analytics & History</h1>
                            <p class="mb-0 text-white-75">Track and monitor all your SMS communications</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex gap-2 justify-content-lg-end">
                                <a href="{{ route('sms.index') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-paper-plane"></i> Send SMS
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-arrow-left"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                                Total SMS
                            </div>
                            <div class="h4 mb-0 font-weight-bold">
                                {{ number_format($stats['total_sms']) }}
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-chart-line me-1"></i>
                                <small class="opacity-75">All time messages</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-sms fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                                Sent Successfully
                            </div>
                            <div class="h4 mb-0 font-weight-bold">
                                {{ number_format($stats['sent_sms']) }}
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-percentage me-1"></i>
                                <small class="opacity-75">
                                    {{ $stats['total_sms'] > 0 ? round(($stats['sent_sms'] / $stats['total_sms']) * 100, 1) : 0 }}% success rate
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                                Failed
                            </div>
                            <div class="h4 mb-0 font-weight-bold">
                                {{ number_format($stats['failed_sms']) }}
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <small class="opacity-75">Need attention</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-75">
                                Total Cost
                            </div>
                            <div class="h4 mb-0 font-weight-bold">
                                KES {{ number_format($stats['total_cost'], 2) }}
                            </div>
                            <div class="mt-2">
                                <i class="fas fa-coins me-1"></i>
                                <small class="opacity-75">Communication spend</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-white border-0">
            <div class="d-flex align-items-center">
                <div class="card-icon me-3">
                    <div class="bg-primary bg-gradient rounded-circle p-2">
                        <i class="fas fa-filter text-white"></i>
                    </div>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold">Advanced Filters</h6>
                    <p class="text-muted mb-0 small">Refine your search results</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sms.logs') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-semibold">
                            <i class="fas fa-info-circle text-primary me-1"></i>Status
                        </label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>
                                <i class="fas fa-check"></i> Sent
                            </option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>
                                <i class="fas fa-check-double"></i> Delivered
                            </option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>
                                <i class="fas fa-times"></i> Failed
                            </option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                <i class="fas fa-clock"></i> Pending
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="provider" class="form-label fw-semibold">
                            <i class="fas fa-satellite-dish text-primary me-1"></i>Provider
                        </label>
                        <select class="form-select" id="provider" name="provider">
                            <option value="">All Providers</option>
                            <option value="jambopay" {{ request('provider') === 'jambopay' ? 'selected' : '' }}>JamboPay</option>
                            <option value="twilio" {{ request('provider') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                            <option value="africas_talking" {{ request('provider') === 'africas_talking' ? 'selected' : '' }}>Africa's Talking</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="phone_number" class="form-label fw-semibold">
                            <i class="fas fa-phone text-primary me-1"></i>Phone Number
                        </label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" 
                               value="{{ request('phone_number') }}" placeholder="254722...">
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>From
                        </label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label fw-semibold">
                            <i class="fas fa-calendar-check text-primary me-1"></i>To
                        </label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('sms.logs') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SMS Logs Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">SMS Message History</h6>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportLogs()">
                        <i class="fas fa-download"></i> Export Logs
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('sms.index') }}">
                        <i class="fas fa-paper-plane"></i> Send New SMS
                    </a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if($smsLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Phone Number</th>
                                <th>Message</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Voucher</th>
                                <th>Sent At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($smsLogs as $log)
                                <tr>
                                    <td>
                                        <code class="text-primary">#{{ $log->id }}</code>
                                    </td>
                                    <td>{{ $log->phone_number }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $log->message }}">
                                            {{ Str::limit($log->message, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ strtoupper($log->provider) }}
                                        </span>
                                    </td>
                                    <td>
                                        @switch($log->status)
                                            @case('sent')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Sent
                                                </span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-double"></i> Delivered
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times"></i> Failed
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-end">
                                        @if($log->cost)
                                            KES {{ number_format($log->cost, 4) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->voucher)
                                            <a href="{{ route('vouchers.show', $log->voucher) }}" 
                                               class="text-primary">
                                                {{ $log->voucher->code }}
                                            </a>
                                            <br><small class="text-muted">{{ $log->voucher->voucherPlan->name }}</small>
                                        @else
                                            <span class="text-muted">Manual SMS</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->sent_at)
                                            {{ $log->sent_at->format('M d, Y H:i') }}
                                            <br><small class="text-muted">{{ $log->sent_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Not sent</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewMessage({{ $log->id }}, '{{ addslashes($log->message) }}')"
                                                    title="View Full Message">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($log->status === 'failed' && auth()->user()->isAdmin())
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        onclick="retryMessage({{ $log->id }})"
                                                        title="Retry SMS">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $smsLogs->firstItem() }} to {{ $smsLogs->lastItem() }} of {{ $smsLogs->total() }} results
                    </div>
                    {{ $smsLogs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No SMS logs found</h5>
                    <p class="text-muted">No SMS messages match your current filters.</p>
                    <a href="{{ route('sms.index') }}" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Your First SMS
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SMS Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="fullMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(id, message) {
    document.getElementById('fullMessage').textContent = message;
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}

function retryMessage(id) {
    if (confirm('Are you sure you want to retry sending this SMS?')) {
        // Implement retry functionality
        alert('Retry functionality will be implemented');
    }
}

function exportLogs() {
    alert('Export functionality will be implemented');
}
</script>

</x-layouts.app>
