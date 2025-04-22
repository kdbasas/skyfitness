<!DOCTYPE html>
<html>
<head>
    <title>Renewals Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1A1363;
        }
        .report-details {
            margin-bottom: 20px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #1A1363;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Renewals Report</h1>
        <p>Generated on: {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Subscription ID</th>
                <th>Amount</th>
                <th>Date Paid</th>
                <th>Promo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $payment)
                <tr>
                    <td>{{ $payment->member ? $payment->member->first_name : 'N/A' }}</td>
                    <td>{{ $payment->member ? $payment->member->last_name : 'N/A' }}</td>
                    <td>{{ $payment->subscription_id }}</td>
                    <td>Php{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->date_paid)->format('F d, Y') }}</td>
                    <td>{{ $payment->promo ?? 'No Promo' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} Roxas Sky Fitness Gym. All Rights Reserved.</p>
    </div>

</body>
</html>
