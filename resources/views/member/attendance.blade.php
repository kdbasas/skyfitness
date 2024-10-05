<!-- views/member/attendance.blade.php -->

<div class="flex min-h-screen">
    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6 bg-[#ECE9E9]">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 bg-transparent text-[#1A1363]"style="margin-top: -20px;">
            <h1 class="text-4xl font-bold">Attendance</h1>
        </div>

        <!-- Attendance Form -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Check-in/Check-out</h2>

            <div class="flex space-x-4 mt-4">
                <button id="check-in-btn" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600">Check-in</button>
                <button id="check-out-btn" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600">Check-out</button>
            </div>

            <!-- QR Code Scanner -->
            <div id="qr-scanner" class="hidden">
                <input type="text" id="qr-code-input" class="form-input mt-1 block w-full rounded-lg bg-gray-100 border-gray-300 focus:border-[#1A1363] focus:ring-[#1A1363]">
                <button id="scan-qr-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600">Scan QR Code</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Check-in button click event
    document.getElementById('check-in-btn').addEventListener('click', function() {
        // Activate QR code scanner
        document.getElementById('qr-scanner').classList.remove('hidden');
    });

    // Scan QR code button click event
    document.getElementById('scan-qr-btn').addEventListener('click', function() {
        // Get QR code input value
        var qrCode = document.getElementById('qr-code-input').value;

        // Send AJAX request to generate attendance record
        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.generate") }}',
            data: {
                qr_code: qrCode,
                check_in_out: 'Check-in'
            },
            success: function(response) {
                // Update attendance table
                console.log(response);
            }
        });
    });

    // Check-out button click event
    document.getElementById('check-out-btn').addEventListener('click', function() {
        // Activate QR code scanner
        document.getElementById('qr-scanner').classList.remove('hidden');
    });

    // Scan QR code button click event (for check-out)
    document.getElementById('scan-qr-btn').addEventListener('click', function() {
        // Get QR code input value
        var qrCode = document.getElementById('qr-code-input').value;

        // Send AJAX request to generate attendance record
        $.ajax({
            type: 'POST',
            url: '{{ route("member.attendance.generate") }}',
            data: {
                qr_code: qrCode,
                check_in_out: 'Check-out'
            },
            success: function(response) {
                // Update attendance table
                console.log(response);
            }
        });
    });
</script>