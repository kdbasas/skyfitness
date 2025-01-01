<!-- resources/views/admin/receipt_pdf.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 300px; /* Standard receipt width */
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #000;
            height: 500px; /* Make the receipt taller */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .center {
            text-align: center;
            margin: 0;
        }
        .line {
            text-align: center;
            margin: 10px 0;
        }
        .content {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        .content span {
            width: 130px; /* Fix label width to make it consistent */
        }
        .content-value {
            width: 130px; /* Add space for the value on the right */
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <p class="center">********************************</p>
        <p class="center">ROXAS SKY FITNESS GYM</p>
        <p class="center">********************************</p>

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
            <span class="content-value">Php {{ number_format($member->amount, 2) }}</span>
        </div>

        <p class="line">--------------------------------</p>
        <p class="center">Thank you for your payment!</p>
        <p class="line">--------------------------------</p>
    </div>
</body>
</html>
