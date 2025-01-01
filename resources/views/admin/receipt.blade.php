<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Receipt</h2>
            <p>Member Name: {{ $member->first_name }} {{ $member->last_name }}</p>
            <p>Subscription: {{ $member->subscription->subscription_name }}</p>
            <p>Date: {{ $receipt->date }}</p>
            <p>Amount: ₱{{ number_format($receipt->amount, 2) }}</p>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>