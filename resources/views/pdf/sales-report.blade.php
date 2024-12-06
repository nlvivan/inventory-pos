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
            padding: 5px;
            color: #333;
        }
        .invoice-box {
            max-width: 1000px;
            margin: auto;
            padding: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-box h3 {
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
            padding: 4px;
        }
        .table-header {
            background: #f4f4f4;
        }
        .table-total {
            font-weight: bold;
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
        }
        .prepared-by {
            text-align: left; /* Align the content to the left */
            margin-top: 30px;
            font-size: 14px;
            font-weight: normal;
        }
        .prepared-by p {
            margin: 5px 0;
        }
        .prepared-by .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .prepared-by .role {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="invoice-box">

        <h2>Sales Report</h2>
        <h3>Period At</h3>
        <h3>{{ $from }} -  {{ $to }}</h3>

        <!-- Company Information -->
        <table>
            <tr class="table-header">
                <td>Transaction ID</td>
                <td>Items</td>
                <td>Amount</td>
            </tr>

            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ count($order->orderItems) }}</td>
                <td>&#8369; {{ $order->total_amount }}</td>
            </tr>
            @endforeach

            <tr class="table-total">
                <td colspan="2" class="total">Total</td>
                <td>&#8369; {{ $totalSales }}</td>
            </tr>
        </table>

        <!-- Prepared By Section -->
        <div class="prepared-by">
            <p>Prepared</p> 
            <p class="name">John Carlo Diano</p> 
            <p class="role">Owner</p>
        </div>

    </div>
</body>
</html>
