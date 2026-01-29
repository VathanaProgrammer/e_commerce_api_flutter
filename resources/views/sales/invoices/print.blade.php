<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $transaction->invoice_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 30px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        .print-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 30px; }
        .business-name { font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 5px; }
        .invoice-title { font-size: 24px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .table th { background-color: #f8fafc !important; border-top: none; }
        .text-muted { color: #64748b !important; }
        
        .user-info-box {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .user-info-box { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print mb-4 p-3 bg-light border-bottom d-flex justify-content-between">
        <span class="text-muted">Invoice Preview</span>
        <button onclick="window.print()" style="display: none;" class="btn btn-primary btn-sm">
            <i class="bi bi-printer me-2"></i> Print Now
        </button>
    </div>

    <div class="print-header d-flex justify-content-between align-items-center">
        <div>
            <div class="business-name">{{ session('business.name', 'Codefy') }}</div>
            <div class="text-muted small">{{ session('business.address', 'Sen Sok') }}</div>
            <div class="text-muted small">Phone: {{ session('business.mobile', '017552602') }} | Email: {{ session('business.email', 'siengvathana1@gmail.com') }}</div>
        </div>
        <div class="text-end">
            <div class="invoice-title">INVOICE</div>
            <div class="fw-bold" style="font-size: 1.2rem;">{{ $transaction->invoice_no }}</div>
            <div class="small text-muted">Date: {{ $transaction->created_at->format('M d, Y H:i A') }}</div>
        </div>
    </div>

    <div class="user-info-box">
        <div class="row">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <h6 class="text-uppercase text-muted silver small mb-3">Customer Details</h6>
                <div class="fw-bold fs-5">{{ $transaction->user->first_name ?? 'Guest' }} {{ $transaction->user->last_name ?? '' }}</div>
                <div class="text-muted">{{ $transaction->user->email ?? '' }}</div>
                <div class="text-muted small">{{ $transaction->shipping_address }}</div>
            </div>
            <div class="col-12 col-md-6 text-md-end">
                <h6 class="text-uppercase text-muted small mb-3">Payment Summary</h6>
                <div class="fs-4 fw-bold text-primary">${{ number_format($transaction->total_sell_price, 2) }}</div>
                <div class="badge bg-success-subtle text-success px-3">{{ $transaction->status->label() }}</div>
            </div>
        </div>
    </div>

    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th width="50">#</th>
                <th>Item Description</th>
                <th>Variant</th>
                <th class="text-end">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->saleLines as $index => $line)
                @php
                    $variantDetails = $line->variant->attributeValues
                        ->map(fn($v) => $v->attribute->name . ': ' . $v->value)
                        ->implode(', ');
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $line->variant->product->name }}</div>
                        <div class="small text-muted">{{ $line->variant->sku }}</div>
                    </td>
                    <td class="small">{{ $variantDetails ?: '--' }}</td>
                    <td class="text-end">${{ number_format($line->price, 2) }}</td>
                    <td class="text-center">{{ $line->qty }}</td>
                    <td class="text-end fw-bold">${{ number_format($line->price * $line->qty, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">Subtotal</td>
                <td class="text-end">${{ number_format($transaction->total_sell_price + $transaction->discount_amount, 2) }}</td>
            </tr>
            @if($transaction->discount_amount > 0)
            <tr>
                <td colspan="5" class="text-end text-danger fw-bold">Discount</td>
                <td class="text-end text-danger">-${{ number_format($transaction->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="table-primary">
                <td colspan="5" class="text-end fw-bold fs-5">Grand Total</td>
                <td class="text-end fw-bold fs-5">${{ number_format($transaction->total_sell_price, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-5 pt-4 text-center border-top">
        <p class="text-muted small">Thank you for your business! If you have any questions, please contact us at {{ session('business.email', 'siengvathana1@gmail.com') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };

        window.onafterprint = function() {
             window.close();
        };
    </script>
</body>
</html>
