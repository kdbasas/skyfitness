<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="color-adjust" content="exact">
    <meta name="print-color-adjust" content="exact">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Analytics for {{ $selectedMonth }}</title>
    <!-- Previous code remains the same until the style section -->
<style>
    :root {
        --primary-color: #1A1363;
        --secondary-color: #2c218a;
        --accent-color: #f8f9fa;
        --text-color: #333;
        --border-color: #dee2e6;
    }

    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        margin: 0;
        padding: 10px;
        background-color: #f9f9f9;
        color: var(--text-color);
        line-height: 1.4;
    }

    .report-container {
        max-width: 800px; /* Reduced from 1000px */
        margin: 0 auto;
        padding: 20px; /* Reduced from 30px */
        background: #fff;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        border-radius: 8px; /* Reduced from 12px */
    }

    .header {
        text-align: center;
        padding: 15px; /* Reduced from 20px */
        border-bottom: 2px solid var(--primary-color);
        margin-bottom: 20px; /* Reduced from 30px */
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }

    .logo-container img {
        width: 80px; /* Reduced from 120px */
        height: auto;
        margin-right: 15px;
    }

    .report-title {
        font-size: 24px; /* Reduced from 32px */
        color: var(--primary-color);
        margin: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-subtitle {
        font-size: 18px; /* Reduced from 24px */
        color: var(--secondary-color);
        margin: 8px 0;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px; /* Reduced from 25px */
        margin: 20px 0; /* Reduced from 30px */
    }

    .metric-card {
        background: linear-gradient(145deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px; /* Reduced from 25px */
        border-radius: 10px;
        text-align: center;
    }

    .metric-value {
        font-size: 28px; /* Reduced from 36px */
        font-weight: bold;
        margin: 8px 0;
    }

    .metric-label {
        font-size: 16px; /* Reduced from 18px */
        opacity: 0.9;
    }

    .section {
        background-color: var(--accent-color);
        padding: 15px; /* Reduced from 25px */
        border-radius: 8px;
        margin-bottom: 20px; /* Reduced from 30px */
        border: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 20px; /* Reduced from 24px */
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary-color);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 14px;
        background-color: white;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center; /* Changed to center for alignment */
    }

    th {
        background-color: #f2f2f2;
        color: var(--text-color); /* Changed from white to text color */
        font-weight: 600;
    }

    td {
        padding: 10px;
        font-size: 14px;
    }

    /* Add specific class for numeric values */
    .number-cell {
        text-align: center;
        font-weight: 500;
    }

    .signature-section {
        margin-top: 30px; /* Reduced from 50px */
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px; /* Reduced from 30px */
    }

    .signature-box {
        text-align: center;
    }

    .signature-line {
        width: 70%; /* Reduced from 80% */
        margin: 30px auto 8px; /* Reduced from 50px */
        border-top: 1px solid var(--text-color);
    }

    .footer {
        margin-top: 30px; /* Reduced from 40px */
        padding: 15px; /* Reduced from 20px */
        text-align: center;
        border-top: 1px solid var(--border-color);
        font-size: 12px; /* Reduced from 14px */
    }
    
    .button-container {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 20px auto;
    }

    .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            color: white;
        }

        .btn-back {
            background: #6c757d;
        }

        .btn-back:hover {
            background: #5a6268;
        }
    /* Add this to your style section */
    .btn-print {
            background: var(--primary-color);
        }

        .btn-print:hover {
            background: var(--secondary-color);
        }

    @media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    body {
        padding: 0;
        background: white !important;
        print-color-adjust: exact !important;
    }

    .report-container {
        width: 100%;
        max-width: none;
        padding: 15px;
        box-shadow: none;
    }

    /* Preserve colors for metric cards */
    .metric-card {
        background: linear-gradient(145deg, #1A1363, #2c218a) !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Preserve table colors */
    th {
        background-color: #f2f2f2 !important;
        color: #333 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    td {
        color: #333 !important;
    }

    /* Preserve text colors */
    .report-title {
        color: #1A1363 !important;
    }

    .report-subtitle {
        color: #2c218a !important;
    }

    .section-title {
        color: #1A1363 !important;
    }

    .section {
        background-color: #f8f9fa !important;
        page-break-inside: avoid;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .signature-section {
        page-break-inside: avoid;
    }

    .button-container {
        display: none !important;
    }

    /* Force background printing */
    :root {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    .print-btn {
        display: none !important;
    }
}
</style>
</head>
<body>
    <div class="report-container">
        <div class="header">
            <div class="logo-container">
                <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo">
                <div>
                    <h1 class="report-title">Roxas Sky Fitness Gym</h1>
                    <h2 class="report-subtitle">Monthly Analytics Report</h2>
                </div>
            </div>
            <p>Report Period: {{ Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</p>
        </div>

        <div class="dashboard-grid">
            <div class="metric-card">
                <div class="metric-value">${{ number_format($totalRevenue ?? 0, 2) }}</div>
                <div class="metric-label">Total Revenue</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">{{ $memberRegistrations ?? 0 }}</div>
                <div class="metric-label">New Registrations</div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">Membership Distribution</h3>
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
                            <td class="number-cell">{{ $promoTrends->total_members }}</td>
                            <td class="number-cell">{{ $promoTrends->student_members }}</td>
                            <td class="number-cell">{{ $promoTrends->regular_members }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>No member registration data available for {{ Carbon\Carbon::parse($selectedMonth)->format('F Y') }}.</p>
            @endif
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Prepared by</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>Approved by</p>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Roxas Sky Fitness Gym. All Rights Reserved.</p>
        </div>
        <div class="button-container">
            <a href="{{ route('admin.reportAnalytics') }}" class="btn btn-back">Back to Report</a>
            <button class="btn btn-print" onclick="window.print()">Print Report</button>
        </div>
    </div>
    <script>
        window.onbeforeprint = function() {
            document.body.style.WebkitPrintColorAdjust = 'exact';
            document.body.style.colorAdjust = 'exact';
        };
    </script>
</body>
</html>