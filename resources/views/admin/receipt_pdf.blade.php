<!-- resources/views/admin/receipt_pdf.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Courier, monospace; /* Use a monospaced font for better alignment */
            font-size: 12px; /* Font size for the receipt */
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 2.28in; /* Set width to 58mm (2.28 inches) */
            height: auto; /* Let height adjust based on content */
            margin: 0 auto;
            padding: 10px; /* Padding around the receipt */
            border: 1px solid #000; /* Optional border for visual clarity */
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Align items to the top */
        }
        .left {
            text-align: left; /* Align text to the left */
            margin: 0;
        }
        .line {
            border-top: 1px solid #000; /* Solid line */
            margin: 10px 0; /* Margin for spacing */
        }
        .content {
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Space between label and value */
            margin: 5px 0; /* Margin for spacing */
        }
        .content span {
            width: 50%; /* Each label takes half the width */
        }
        .content-value {
            text-align: right; /* Align values to the right */
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            button {
                display: none; /* Hide the print button when printing */
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <p class="left">********************************</p>
        <p class="left">ROXAS SKY FITNESS GYM</p>
        <p class="left">********************************</p>

        <div class="content">
            <span>Member Name:</span>
            <span class="content-value">{{ $member->first_name }} {{ $member->last_name }}</span>
        </div>
        <div class="content">
            <span>Subscription:</span>
            <span class="content-value">{{ $member->subscription->subscription_name }}</span>
        </div>
        <div class="content">
            <span>Date:</span>
            <span class="content-value">{{ \Carbon\Carbon::parse($member->date_joined)->format('Y-m-d') }}</span>
        </div>
        <div class="content">
            <span>Amount:</span>
            <span class="content-value">₱{{ number_format($member->amount, 2) }}</span>
        </div>

        <div class="line"></div> <!-- Horizontal line -->
        <p class="left">Thank you for your payment!</p>
        <div class="line"></div> <!-- Horizontal line -->
        
        <div class="footer">
            <p class="left">********************************</p>
            <p class="left">Receipt No: {{ $member->member_id }}</p>
        </div>
    </div>
</body>
</html>