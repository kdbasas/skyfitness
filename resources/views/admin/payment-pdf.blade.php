<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logosky 2.png') }}" alt="Gym Logo" class="logo">
        <h1>ROXAS SKY FITNESS GYM</h1>
        <h2>Payment Report</h2>
        <p>Date: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Subscription Plan</th>
                <th>Promo</th>
                <th>Amount</th>
                <th>Date Paid</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->member->first_name }} {{ $payment->member->last_name }}</td>
                    <td>{{ $payment->subscription->subscription_name }}</td>
                    <td>{{ $payment->promo }}</td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ Carbon\Carbon::parse($payment->date_paid)->format('M d, Y') }}</td>
                    <td>{{ $payment->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Amount: </strong>PHP{{ number_format($totalAmount, 2) }}</p>
    </div>

    <div class="footer">
        <p>Generated on: {{ $generatedAt }}</p>
    </div>
</body>
</html>