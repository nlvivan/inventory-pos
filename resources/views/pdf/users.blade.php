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
            max-width: 1000px;
            margin: auto;
            padding: 10px;
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
    </style>
</head>
<body>
    <div class="invoice-box">
        <img src='{{$logo}}' style="width: 150px;"/>
        <div class="company-info">
            <h3>Three Lito's</h3>
            <p>Carmen.<br>Davao del Norte<br>(123) 456-7890</p>
        </div>

        <h2>Users</h2>

          <!-- Company Information -->
         
        <table>
            <tr class="table-header">
                <td>First Name</td>
                <td>Middle Name</td>
                <td>Last Name</td>
                <td>Email</td>
                <td>Joined At</td>
            </tr>

            @foreach($record as $item)
            <tr>
                <td>{{ $item->first_name }}</td>
                <td>{{ $item->last_name }}</td>
                <td>{{ $item->middle_name }}</td>
                <td>{{ $item->email }}</td>
                <td> {{ $item->created_at }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Footer -->
        <p style="text-align: center; margin-top: 20px;">Thank you for your business!</p>
    </div>
</body>
</html>
