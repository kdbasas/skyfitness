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
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .report-container {
                box-shadow: none;
                border: none;
            }
            button {
                display: none;
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

        <!-- Summary Section -->
        <div class="summary">
            <p><strong>Members Registered:</strong> {{ $memberRegistrations ?? 0 }}</p>
            <p><strong>Total Revenue:</strong> ${{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>

        <!-- Age Trend Analysis -->
        @if(isset($ageTrend))
            <h3 class="text-xl font-semibold mb-2">Age Trend Analysis</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @foreach($ageTrend as $age => $count)
                    <div class="bg-orange-600 text-white p-6 rounded-lg shadow-lg">
                        <h4 class="text-lg font-semibold">{{ $age }} years old</h4>
                        <p class="text-2xl">{{ $count }} members</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Membership Types -->
        @if(isset($studentMembers) && isset($regularMembers))
            <h3 class="text-xl font-semibold mb-2">Membership Types</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-purple-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Student Members</h4>
                    <p class="text-2xl">{{ $studentMembers ?? 0 }} ({{ number_format($studentMembersPercentage ?? 0, 2) }}%)</p>
                </div>
                <div class="bg-pink-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Regular Members</h4>
                    <p class="text-2xl">{{ $regularMembers ?? 0 }} ({{ number_format($regularMembersPercentage ?? 0, 2) }}%)</p>
                </div>
            </div>
        @endif

        <!-- Revenue by Month -->
        @if(isset($revenueByMonth))
            <h3 class="text-xl font-semibold mb-2">Revenue by Month</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @foreach($revenueByMonth as $month => $revenue)
                    <div class="bg-teal-600 text-white p-6 rounded-lg shadow-lg">
                        <h4 class="text-lg font-semibold">{{ $month }}</h4>
                        <p class="text-2xl">${{ number_format($revenue, 2) }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Top 5 Most Popular Subscriptions -->
        @if(isset($topSubscriptions))
            <h3 class="text-xl font-semibold mb-2">Top 5 Most Popular Subscriptions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @foreach($topSubscriptions as $subscription)
                    <div class="bg-pink-600 text-white p-6 rounded-lg shadow-lg">
                        <h4 class="text-lg font-semibold">{{ $subscription->subscription_name }}</h4>
                        <p class="text-2xl">{{ $subscription->members_count }} members</p>
                    </div>
                @endforeach
            </div>
        @endif

        <button class="no-print" onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
