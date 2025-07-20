<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vouchers Export - {{ config('app.name') }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .report-meta {
            font-size: 11px;
            color: #888;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            font-size: 10px;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-generated { background: #6c757d; color: white; }
        .status-printed { background: #007bff; color: white; }
        .status-sold { background: #ffc107; color: black; }
        .status-used { background: #28a745; color: white; }
        .status-expired { background: #dc3545; color: white; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #666; }
        .font-mono { font-family: 'Courier New', monospace; }

        .footer {
            position: fixed;
            bottom: 20mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; background: white; padding: 15px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
            ✖️ Close
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ config('app.name', 'WiFi Management System') }}</div>
        <div class="report-title">Vouchers Export Report</div>
        <div class="report-meta">
            Generated on {{ now()->format('F d, Y \a\t g:i A') }} | 
            Total Records: {{ $vouchers->count() }}
        </div>
    </div>

    <!-- Summary Statistics -->
    @php
        $totalVouchers = $vouchers->count();
        $totalRevenue = $vouchers->where('status', 'used')->sum('price');
        $usedVouchers = $vouchers->where('status', 'used')->count();
        $availableVouchers = $vouchers->whereIn('status', ['generated', 'printed'])->count();
        $statusCounts = $vouchers->groupBy('status')->map->count();
    @endphp

    <div class="summary-stats">
        <div class="stat-item">
            <div class="stat-value">{{ number_format($totalVouchers) }}</div>
            <div class="stat-label">Total Vouchers</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($usedVouchers) }}</div>
            <div class="stat-label">Used</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($availableVouchers) }}</div>
            <div class="stat-label">Available</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-label">Revenue</div>
        </div>
    </div>

    <!-- Status Breakdown -->
    @if($statusCounts->count() > 1)
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; margin-bottom: 15px; color: #555;">Status Breakdown</h3>
        <div style="display: flex; justify-content: space-around; background: #f8f9fa; padding: 15px; border-radius: 5px;">
            @foreach($statusCounts as $status => $count)
            <div class="text-center">
                <div style="font-size: 16px; font-weight: bold; color: #007bff;">{{ $count }}</div>
                <div style="font-size: 10px; color: #666; text-transform: capitalize;">{{ $status }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Vouchers Table -->
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Password</th>
                <th>Plan</th>
                <th>Price</th>
                <th>Status</th>
                <th>Router</th>
                <th>Created</th>
                <th>Used</th>
                <th>Generated By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vouchers as $voucher)
            <tr>
                <td class="font-mono">{{ $voucher->code }}</td>
                <td class="font-mono">{{ $voucher->password }}</td>
                <td>
                    {{ $voucher->voucherPlan->name }}
                    <br>
                    <span class="text-muted" style="font-size: 9px;">
                        {{ $voucher->voucherPlan->formatted_duration }}
                    </span>
                </td>
                <td class="text-right">${{ number_format($voucher->price, 2) }}</td>
                <td>
                    <span class="status-badge status-{{ $voucher->status }}">
                        {{ ucfirst($voucher->status) }}
                    </span>
                </td>
                <td>{{ $voucher->router?->name ?? 'N/A' }}</td>
                <td class="text-center">
                    {{ $voucher->created_at->format('m/d/Y') }}
                    <br>
                    <span class="text-muted" style="font-size: 8px;">
                        {{ $voucher->created_at->format('H:i') }}
                    </span>
                </td>
                <td class="text-center">
                    @if($voucher->used_at)
                        {{ $voucher->used_at->format('m/d/Y') }}
                        <br>
                        <span class="text-muted" style="font-size: 8px;">
                            {{ $voucher->used_at->format('H:i') }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $voucher->user?->name ?? 'System' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div>
            {{ config('app.name') }} - WiFi Management System | 
            {{ config('app.url') }} | 
            Support: {{ config('app.support_email', 'support@example.com') }}
        </div>
        <div style="margin-top: 5px;">
            This report contains confidential information. Distribution should be limited to authorized personnel only.
        </div>
    </div>

    <script>
        // Auto-adjust for printing
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
        });

        window.addEventListener('afterprint', function() {
            document.querySelectorAll('.no-print').forEach(el => el.style.display = 'block');
        });
    </script>
</body>
</html>
