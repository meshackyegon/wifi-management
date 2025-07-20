<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Vouchers</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .voucher {
            width: 85mm;
            height: 54mm;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 8px;
            margin: 5mm;
            display: inline-block;
            vertical-align: top;
            page-break-inside: avoid;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .voucher-header {
            text-align: center;
            border-bottom: 1px solid #666;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .voucher-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        .voucher-subtitle {
            font-size: 10px;
            color: #7f8c8d;
            margin: 0;
        }

        .voucher-body {
            position: relative;
        }

        .credentials {
            background: #fff;
            border: 1px dashed #bdc3c7;
            border-radius: 4px;
            padding: 6px;
            margin: 4px 0;
            text-align: center;
        }

        .credential-label {
            font-size: 9px;
            color: #7f8c8d;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credential-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin: 2px 0;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .plan-info {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 10px;
        }

        .plan-detail {
            text-align: center;
            flex: 1;
        }

        .plan-label {
            color: #7f8c8d;
            margin: 0;
            font-size: 8px;
            text-transform: uppercase;
        }

        .plan-value {
            color: #2c3e50;
            font-weight: bold;
            margin: 1px 0 0 0;
            font-size: 10px;
        }

        .voucher-footer {
            border-top: 1px solid #bdc3c7;
            padding-top: 4px;
            margin-top: 6px;
            font-size: 8px;
            color: #95a5a6;
            text-align: center;
        }

        .price-tag {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .qr-code {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 20mm;
            height: 20mm;
        }

        .instructions {
            margin-top: 2px;
            font-size: 8px;
            color: #7f8c8d;
            line-height: 1.2;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Print controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        .close-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #333;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-tagline {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button class="print-btn" onclick="window.print()">
            🖨️ Print Vouchers
        </button>
        <button class="close-btn" onclick="window.close()">
            ✖️ Close
        </button>
        <div style="margin-top: 10px; font-size: 12px; color: #666;">
            Total Vouchers: {{ $vouchers->count() }}
        </div>
    </div>

    <!-- Company Header -->
    <div class="company-info no-print">
        <div class="company-name">{{ config('app.name', 'WiFi Management System') }}</div>
        <div class="company-tagline">Premium Internet Access Solutions</div>
    </div>

    <!-- Vouchers Grid -->
    <div class="vouchers-container">
        @foreach($vouchers as $voucher)
        <div class="voucher">
            <!-- Price Tag -->
            <div class="price-tag">${{ number_format($voucher->price, 2) }}</div>

            <!-- Header -->
            <div class="voucher-header">
                <h3 class="voucher-title">WiFi Voucher</h3>
                <p class="voucher-subtitle">{{ $voucher->voucherPlan->name }}</p>
            </div>

            <!-- Body -->
            <div class="voucher-body">
                <!-- Username/Code -->
                <div class="credentials">
                    <p class="credential-label">Username</p>
                    <p class="credential-value">{{ $voucher->code }}</p>
                </div>

                <!-- Password -->
                <div class="credentials">
                    <p class="credential-label">Password</p>
                    <p class="credential-value">{{ $voucher->password }}</p>
                </div>

                <!-- Plan Details -->
                <div class="plan-info">
                    <div class="plan-detail">
                        <p class="plan-label">Duration</p>
                        <p class="plan-value">{{ $voucher->voucherPlan->formatted_duration }}</p>
                    </div>
                    <div class="plan-detail">
                        <p class="plan-label">Data Limit</p>
                        <p class="plan-value">{{ $voucher->voucherPlan->formatted_data_limit }}</p>
                    </div>
                    @if($voucher->router)
                    <div class="plan-detail">
                        <p class="plan-label">Location</p>
                        <p class="plan-value">{{ Str::limit($voucher->router->location, 10) }}</p>
                    </div>
                    @endif
                </div>

                <!-- Instructions -->
                <div class="instructions">
                    1. Connect to WiFi network<br>
                    2. Open browser and go to login page<br>
                    3. Enter username and password above<br>
                    4. Enjoy your internet access!
                </div>
            </div>

            <!-- Footer -->
            <div class="voucher-footer">
                <div>{{ $voucher->created_at->format('M d, Y H:i') }}</div>
                <div>{{ config('app.url') }} | Support: {{ config('app.support_phone', '+1234567890') }}</div>
                @if($voucher->expires_at)
                <div>Expires: {{ $voucher->expires_at->format('M d, Y') }}</div>
                @endif
            </div>

            <!-- QR Code Placeholder -->
            <div class="qr-code" id="qr-{{ $voucher->id }}"></div>
        </div>
        @endforeach
    </div>

    <!-- QR Code Generation Script -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($vouchers as $voucher)
            // Generate QR code for voucher {{ $voucher->id }}
            const qrData{{ $voucher->id }} = JSON.stringify({
                username: '{{ $voucher->code }}',
                password: '{{ $voucher->password }}',
                plan: '{{ $voucher->voucherPlan->name }}',
                price: '{{ $voucher->price }}',
                url: '{{ config("app.url") }}'
            });

            QRCode.toCanvas(document.getElementById('qr-{{ $voucher->id }}'), qrData{{ $voucher->id }}, {
                width: 75,
                height: 75,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function(error) {
                if (error) {
                    console.error('QR Code generation error for voucher {{ $voucher->id }}:', error);
                    // Replace with text if QR generation fails
                    document.getElementById('qr-{{ $voucher->id }}').innerHTML = 
                        '<div style="font-size: 8px; text-align: center; padding: 10px;">QR Code</div>';
                }
            });
            @endforeach

            // Auto-print after QR codes are generated
            setTimeout(function() {
                // Mark vouchers as printed via AJAX
                fetch('{{ route("vouchers.mark-printed") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        voucher_ids: [{{ $vouchers->pluck('id')->implode(',') }}]
                    })
                }).catch(error => {
                    console.error('Error marking vouchers as printed:', error);
                });
            }, 1000);
        });

        // Print optimization
        window.addEventListener('beforeprint', function() {
            document.querySelector('.print-controls').style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            document.querySelector('.print-controls').style.display = 'block';
        });
    </script>
</body>
</html>
