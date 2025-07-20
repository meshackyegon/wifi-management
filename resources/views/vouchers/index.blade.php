@section('title', 'Vouchers')
<x-layouts.app title="Vouchers">

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">Vouchers</h1>
                <div class="d-flex gap-2">
                    @can('create', \App\Models\Voucher::class)
                        <a href="{{ route('vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Generate Vouchers
                        </a>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                            <i class="fas fa-file-upload"></i> Bulk Generate
                        </button>
                    @endcan
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('vouchers.export', 'csv') . '?' . request()->getQueryString() }}">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('vouchers.export', 'pdf') . '?' . request()->getQueryString() }}">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vouchers.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Code</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Enter voucher code">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="generated" {{ request('status') === 'generated' ? 'selected' : '' }}>Generated</option>
                            <option value="printed" {{ request('status') === 'printed' ? 'selected' : '' }}>Printed</option>
                            <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                            <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="plan_id" class="form-label">Plan</label>
                        <select class="form-select" id="plan_id" name="plan_id">
                            <option value="">All Plans</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="router_id" class="form-label">Router</label>
                        <select class="form-select" id="router_id" name="router_id">
                            <option value="">All Routers</option>
                            @foreach($routers as $router)
                                <option value="{{ $router->id }}" {{ request('router_id') == $router->id ? 'selected' : '' }}>
                                    {{ $router->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1.5">
                        <label for="date_from" class="form-label">From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1.5">
                        <label for="date_to" class="form-label">To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Vouchers List</h6>
            @if($vouchers->count() > 0)
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-success" onclick="printSelected()" id="printBtn" disabled>
                        <i class="fas fa-print"></i> Print Selected
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if($vouchers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="vouchersTable">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                </th>
                                <th>Code</th>
                                <th>Password</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Router</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vouchers as $voucher)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="voucher-checkbox" value="{{ $voucher->id }}" 
                                               onchange="updatePrintButton()">
                                    </td>
                                    <td>
                                        <code class="fw-bold">{{ $voucher->code }}</code>
                                    </td>
                                    <td>
                                        <code>{{ $voucher->password }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $voucher->voucherPlan->name }}</span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $voucher->voucherPlan->formatted_duration }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <strong>${{ number_format($voucher->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        @switch($voucher->status)
                                            @case('generated')
                                                <span class="badge bg-secondary">Generated</span>
                                                @break
                                            @case('printed')
                                                <span class="badge bg-primary">Printed</span>
                                                @break
                                            @case('sold')
                                                <span class="badge bg-warning">Sold</span>
                                                @break
                                            @case('used')
                                                <span class="badge bg-success">Used</span>
                                                @break
                                            @case('expired')
                                                <span class="badge bg-danger">Expired</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $voucher->router?->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $voucher->created_at->format('M d, Y H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $voucher->user?->name }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vouchers.show', $voucher) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="printVoucher({{ $voucher->id }})" title="Print">
                                                <i class="fas fa-print"></i>
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
                        Showing {{ $vouchers->firstItem() }} to {{ $vouchers->lastItem() }} of {{ $vouchers->total() }} results
                    </div>
                    <div>
                        {{ $vouchers->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No vouchers found</h5>
                    <p class="text-muted">Try adjusting your search criteria or generate new vouchers.</p>
                    @can('create', \App\Models\Voucher::class)
                        <a href="{{ route('vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Generate Your First Vouchers
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
@can('create', \App\Models\Voucher::class)
<div class="modal fade" id="bulkGenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vouchers.bulk-generate') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Generate Vouchers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Upload CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" 
                               accept=".csv,.txt" required>
                        <div class="form-text">
                            CSV format: plan_id, quantity, router_id (optional)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6>CSV Format Example:</h6>
                        <pre>plan_id,quantity,router_id
1,100,1
2,50,2
3,25,</pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.voucher-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(cb => cb.checked = !allChecked);
    document.getElementById('selectAllCheckbox').checked = !allChecked;
    updatePrintButton();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.voucher-checkbox');
    
    checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    updatePrintButton();
}

function updatePrintButton() {
    const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
    const printBtn = document.getElementById('printBtn');
    
    if (checkedBoxes.length > 0) {
        printBtn.disabled = false;
        printBtn.textContent = `Print Selected (${checkedBoxes.length})`;
    } else {
        printBtn.disabled = true;
        printBtn.innerHTML = '<i class="fas fa-print"></i> Print Selected';
    }
}

function printSelected() {
    const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
    const voucherIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (voucherIds.length === 0) {
        alert('Please select vouchers to print');
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("vouchers.print") }}';
    form.target = '_blank';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add voucher IDs
    voucherIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'voucher_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function printVoucher(voucherId) {
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
    input.value = voucherId;
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush
</x-layouts.app>
