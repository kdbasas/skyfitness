@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <h1 class="text-4xl font-bold">Attendance</h1>
        </div>

        <!-- QR Code Scanner -->
        <div id="qr-scanner" style="display: block;">
            <input type="text" id="qr-code-input" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" placeholder="Scan QR code here">
            <button id="scan-qr-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 mt-4">Submit</button>
        </div>

        <!-- Attendance Status -->
        <div id="attendance-status" style="display: none;">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Attendance Status</h2>
            <p id="attendance-message"></p>
        </div>

        <!-- Check-in/Check-out Buttons -->
        <div id="check-in-out-btns" style="display: none;">
            <button id="check-in-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 mt-4">Check In</button>
            <button id="check-out-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 mt-4">Check Out</button>
        </div>
    </div>
</div>

<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    var qrCodeInput = document.getElementById('qr-code-input');
    var scanQrBtn = document.getElementById('scan-qr-btn');
    var attendanceStatus = document.getElementById('attendance-status');
    var attendanceMessage = document.getElementById('attendance-message');
    var checkInOutBtns = document.getElementById('check-in-out-btns');
    var checkInBtn = document.getElementById('check-in-btn');
    var checkOutBtn = document.getElementById('check-out-btn');
    
    var memberId; // This will hold the member ID parsed from QR code

    scanQrBtn.addEventListener('click', function(event) {
        event.preventDefault();

        var qrCode = qrCodeInput.value;

        if (qrCode === '') {
            alert('Please scan a valid QR code.');
            return;
        }

        // Send AJAX request to generate attendance record
        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.generate") }}', // Correct route for generating attendance
            data: {
                qr_code: qrCode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                attendanceStatus.style.display = 'block';
                attendanceMessage.textContent = response.message;

                // Store the member ID if the response is valid
                if (response.memberId) {
                    memberId = response.memberId; // Set memberId from response
                }

                // Determine if the user is checked in or checked out
                if (response.attendanceStatus === 'checkedIn') {
                    checkInBtn.style.display = 'none'; // Hide Check In button
                    checkOutBtn.style.display = 'block'; // Show Check Out button
                } else {
                    checkInBtn.style.display = 'block'; // Show Check In button
                    checkOutBtn.style.display = 'none'; // Hide Check Out button
                }

                checkInOutBtns.style.display = 'block';
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                attendanceStatus.style.display = 'block';
                attendanceMessage.textContent = 'An error occurred: ' + xhr.responseJSON.message;
            }
        });
    });

    checkInBtn.addEventListener('click', function(event) {
        event.preventDefault();

        if (!memberId) {
            attendanceMessage.textContent = 'No member ID found. Please scan the QR code first.';
            return;
        }

        // Send AJAX request to check in
        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.check-in") }}', // Correct route for checking in
            data: {
                member_id: memberId, // Include member ID for check-in
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                attendanceMessage.textContent = response.message;
                checkInBtn.style.display = 'none'; // Hide Check In button
                checkOutBtn.style.display = 'block'; // Show Check Out button
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                attendanceMessage.textContent = 'An error occurred: ' + xhr.responseJSON.message;
            }
        });
    });

    checkOutBtn.addEventListener('click', function(event) {
    event.preventDefault();

    // Send AJAX request to check out
    $.ajax({
        type: 'POST',
        url: '{{ route("member.attendance.check-out") }}',
        data: {
            member_id: memberId, // Pass the memberId to the AJAX request
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log(response);
            attendanceMessage.textContent = response.message;
            checkOutBtn.style.display = 'none'; // Hide Check Out button
            checkInBtn.style.display = 'block'; // Show Check In button
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
            attendanceMessage.textContent = 'An error occurred: ' + xhr.responseText;
        }
    });
});
</script>

@endsection
