<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Analytics for {{ $selectedMonth }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        h1, h2, h3 {
            text-align: center;
            color: #1A1363;
            margin-bottom: 20px;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 80px;
            margin-right: 15px;
        }
        .report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .print-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #1A1363;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
        }
        .print-btn:hover {
            background-color: #2c218a;
        }
        @media print {
            .print-btn {
                display: none;
            }
            .report-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Logo and Title Section -->
        <div class="logo-container">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo">
            <h1>Roxas Sky Fitness Gym</h1>
        </div>
        <h2>Report Analytics for {{ Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</h2>

        <!-- Summary Section -->
        <div class="summary">
            <h3>Summary of Report</h3>
            <table>
                <tr>
                    <th>Total Revenue</th>
                    <td>${{ number_format($totalRevenue ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Registered Members</th>
                    <td>{{ $memberRegistrations ?? 0 }}</td>
                </tr>
            </table>
        </div>

        <!-- Member Registration Details Section -->
        <div class="member-registration-details">
            <h3>Member Registration Details</h3>
            @if(isset($promoTrends))
                <table>
                    <thead>
                        <tr>
                            <th>Total Members</th>
                            <th>Student Members</th>
                            <th>Regular Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $promoTrends->total_members }}</td>
                            <td>{{ $promoTrends->student_members }}</td>
                            <td>{{ $promoTrends->regular_members }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>No member registration data available for {{ Carbon\Carbon::parse($selectedMonth)->format('F Y') }}.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ now()->format('M d, Y') }}</p>
        </div>

        <!-- Print Button -->
        <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
