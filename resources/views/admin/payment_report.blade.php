<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Report for {{ $selectedDate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            color: #1A1363;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #1A1363;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e0e0e0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Payment Report for {{ $selectedDate }}</h1>
    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Subscription Plan</th>
                <th>Promo</th>
                <th>Amount</th>
                <th>Date Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->member->first_name }} {{ $payment->member->last_name }}</td>
                    <td>{{ $payment->subscription->subscription_name }}</td>
                    <td>{{ $payment->promo }}</td>
                    <td>Php{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->date_paid->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y') }}</p>
    </div>
</body>
</html>