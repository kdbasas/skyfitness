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
            padding: 20px;
            background-color: #f9f9f9;
        }
        h1, h2 {
            text-align: center;
            color: #1A1363;
        }
        .report-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            width: 100px; /* Adjust the size as needed */
            height: auto;
        }
        .summary {
            margin-top: 30px;
            font-size: 18px;
        }
        .summary p {
            margin: 10px 0;
        }
        button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #1A1363;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0f0c5c;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            button {
                display: none;
            }
            .report-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="logo-container">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo">
            <h1>Roxas Sky Fitness Gym</h1>
        </div>
        <h2>Report Analytics for {{ Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</h2>

        <div class="summary">
            <p><strong>Members Registered:</strong> {{ $memberRegistrations }}</p>
            <p><strong>Total Revenue:</strong> ${{ number_format($totalRevenue, 2) }}</p>
        </div>

        <button class="no-print" onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
