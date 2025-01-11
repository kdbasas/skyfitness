@extends('layouts.gym_staff')

@section('content')
    <div class="receipt-container">
        <h2>Receipt for {{ $member->first_name }} {{ $member->last_name }}</h2>
        <a href="{{ asset('storage/' . $pdfFilename) }}" id="receipt-link" target="_blank">Download Receipt</a>
    </div>

    <script>
        window.onload = function() {
            const receiptLink = document.getElementById('receipt-link');
            if (receiptLink) {
                const printWindow = window.open(receiptLink.href, '_blank');
                printWindow.onload = function() {
                    printWindow.print();
                };
            }
        };
    </script>
@endsection
