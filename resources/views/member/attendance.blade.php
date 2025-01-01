@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-[#F4F4F9]">
    <!-- Header Section -->
    <div class="flex flex-col items-center justify-center mb-6 p-4 bg-white shadow-md rounded-lg w-full max-w-md">
        <!-- Gym Logo and Name -->
        <img src="{{ asset('img/logosky 2.png') }}" alt="Roxas Sky Fitness Gym" class="h-16 mb-2">
        <h1 class="text-4xl font-extrabold text-[#1A1363] text-center">Roxas Sky Fitness Gym</h1>
        <h2 class="text-2xl font-medium text-gray-600 text-center">Attendance</h2>
    </div>

    <!-- QR Code Scanner Section -->
    <div id="qr-scanner" class="p-6 bg-white shadow-md rounded-lg w-full max-w-md">
        <input type="text" id="qr-code-input" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]" placeholder="Click here and scan your QR Code to the Scanner">
        <button id="scan-qr-btn" class="w-full px-4 py-2 bg-[#1A1363] text-white rounded-lg shadow-md hover:bg-[#16104d] mt-4 transition duration-200 ease-in-out">Submit</button>
    </div>

    <!-- Attendance Status Section -->
    <div id="attendance-status" style="display: none;" class="p-6 mt-6 bg-white shadow-md rounded-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Attendance Status</h2>
        <p id="attendance-message" class="text-lg text-gray-700"></p>
    </div>

    <!-- Check-in/Check-out Buttons Section -->
    <div id="check-in-out-btns" style="display: none;" class="p-6 mt-6 bg-white shadow-md rounded-lg w-full max-w-md flex justify-between">
        <button id="check-in-btn" class="w-5/12 px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition duration-200 ease-in-out">Check In</button>
        <button id="check-out-btn" class="w-5/12 px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition duration-200 ease-in-out">Check Out</button>
    </div>

    <!-- Done Button -->
    <div id="done-section" style="display: none;" class="p-6 mt-6 w-full max-w-md flex justify-center">
        <button id="done-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition duration-200 ease-in-out">Done</button>
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
    var doneBtn = document.getElementById('done-btn');
    var doneSection = document.getElementById('done-section');
    
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
            url: '{{ route("member.attendance.generate") }}',
            data: {
                qr_code: qrCode,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                attendanceStatus.style.display = 'block';
                attendanceMessage.textContent = response.message;

                if (response.memberId) {
                    memberId = response.memberId;
                }

                if (response.attendanceStatus === 'checkedIn') {
                    checkInBtn.style.display = 'none';
                    checkOutBtn.style.display = 'block';
                } else {
                    checkInBtn.style.display = 'block';
                    checkOutBtn.style.display = 'none';
                }

                checkInOutBtns.style.display = 'flex';
                doneSection.style.display = 'block'; // Show the Done button after an action
                // Clear the input field
            qrCodeInput.value = '';
            qrCodeInput.placeholder = '';
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

        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.check-in") }}',
            data: {
                member_id: memberId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                attendanceMessage.textContent = response.message;
                checkInBtn.style.display = 'none';
                checkOutBtn.style.display = 'block';
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                attendanceMessage.textContent = 'An error occurred: ' + xhr.responseJSON.message;
            }
        });
    });

    checkOutBtn.addEventListener('click', function(event) {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.check-out") }}',
            data: {
                member_id: memberId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                attendanceMessage.textContent = response.message;
                checkOutBtn.style.display = 'none';
                checkInBtn.style.display = 'block';
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                attendanceMessage.textContent = 'An error occurred: ' + xhr.responseText;
            }
        });
    });

    // Handle Done button click - Redirect to the login page
    doneBtn.addEventListener('click', function() {
        window.location.href = '{{ route("login") }}';
    });
</script>
@endsection
