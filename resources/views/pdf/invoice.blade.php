<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-info, .billing-info {
            margin-bottom: 20px;
        }
        .company-info h3, .billing-info h3 {
            margin: 0;
        }
        table {
            width: 100%;
            line-height: 1.6;
            text-align: left;
        }
        table td {
            padding: 8px;
        }
        .table-header {
            background: #f4f4f4;
            font-weight: bold;
        }
        .table-total {
            font-weight: bold;
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice</h2>

        <!-- Company Information -->
        <div class="company-info">
            <h3>Three Lito's</h3>
            <p>Carmen.<br>Davao del Norte<br>(123) 456-7890</p>
        </div>

        <!-- Billing Information -->
        <div class="billing-info">
            <h3>Bill To: {{ $record->customer?->name }}</h3>
            <p>{{ $record->customer?->address }}</p>
        </div>

        <!-- Invoice Details -->
        <table>
            <tr class="table-header">
                <td>Description</td>
                <td>Quantity</td>
                <td>Unit Price</td>
                <td>Total</td>
            </tr>

            @foreach($record->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>&#8369; {{ $item->product->price }}</td>
                <td>&#8369; {{ $item->total_price }}</td>
            </tr>
            @endforeach
            <tr class="table-total">
                <td colspan="3" class="total">Total</td>
                <td>&#8369; {{ $record->total_amount }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <p style="text-align: center; margin-top: 20px;">Thank you for your business!</p>
    </div>
</body>
</html>
