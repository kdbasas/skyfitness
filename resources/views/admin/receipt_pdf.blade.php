<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body {
            font-family: Courier, monospace; /* Use a monospaced font for better alignment */
            font-size: 9px; /* Font size for the receipt */
            margin: 0;
            padding: 14;
        }
        .receipt {
            width: 58mm; /* Set width to 58mm */
            height: 210mm; /* Let height adjust based on content */
            margin: -1cm; /* Set margin to 0 on all sides, except for the left side */
            padding: 5px; /* Padding around the receipt */
            display: block;
            flex-direction: column;
            justify-content: flex-start; /* Align items to the top */
            align-items: center; /* Center items horizontally */
            position: absolute;
            left: 0; /* Set the left position to 0 */
            top: 0; /* Set the top position to 0 */
        }

        .left {
        text-align: left; /* Align text to the left */
        margin: 0 auto; /* Center the text horizontally */
        width: 70%; /* Make the text width match the line width */
        transform: translateX(-18%); /* Add a translation to the left to center the text */
    }
    .gym {
        text-align: left; /* Align text to the left */
        margin: 0 auto; /* Center the text horizontally */
        width: 70%; /* Make the text width match the line width */
        transform: translateX(-12%); /* Add a translation to the left to center the text */
    }
    .receiptnumber {
        text-align: left; /* Align text to the left */
        margin: 0 auto; /* Center the text horizontally */
        width: 70%; /* Make the text width match the line width */
        transform: translateX(-5%); /* Add a translation to the left to center the text */
    }
        .line {
            border-top: 1px solid #000; /* Solid line */
            margin: 10px 0; /* Margin for spacing */
            width: 70%; /* Make the line full width */
        }
        .content {
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Space between label and value */
            margin: 5px 0; /* Margin for spacing */
            width: 80%; /* Make content full width */
        }
        .content span {
            width: 20%; /* Each label takes half the width */
            text-align: center; /* Center text in each span */
        }
        .content-value {
            text-align: right; /* Align values to the right */
        }
        @media print {
    .receipt {
        width: 58mm; /* Set width to 58mm */
        height: 210mm; /* Let height adjust based on content */
        margin: 0 auto;
        padding: 0; /* Remove padding */
        border: 1px solid #000; /* Optional border for visual clarity */
        display: block;
        flex-direction: column;
        justify-content: flex-start; /* Align items to the top */
        align-items: center; /* Center items horizontally */
    }
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
        <p class="content">****************************</p>
        <p class="gym">     ROXAS SKY FITNESS GYM</p>
        <p class="content">****************************</p>

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
            <span class="content-value">Php{{ number_format($member->amount, 2) }}</span>
        </div>

        <div class="line"></div> <!-- Horizontal line -->
        <p class="left">Thank you for your payment!</p>
        <div class="line"></div> <!-- Horizontal line -->
        
        <div class="footer">
            <p class="content">****************************</p>
            <p class="receiptnumber">Receipt No: {{ $member->member_id }}</p>
        </div>
    </div>
</body>
</html>