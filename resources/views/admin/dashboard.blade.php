@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#ECE9E9]">
    <!-- Sidebar -->
    @include('include.sidebar')

    
    <!-- Main Content -->
    <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center space-x-32 mb-6 p-6 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <!-- Center Title Container -->
            <div class="flex items-center justify-center flex-1">
                <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
                <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
            </div>

            <!-- Clock Container -->
            <div class="digital-clock flex-shrink-0">
                <div class="time-display">
                    <span id="hours">00</span>
                    <span class="colon">:</span>
                    <span id="minutes">00</span>
                    <span class="colon">:</span>
                    <span id="seconds">00</span>
                    <span id="session">AM</span>
                </div>
                <div class="date-display" id="date"></div>
            </div>
        </div>
        <!-- Welcome Message -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#1A1363]">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-gray-500">Here's what's happening at the gym today:</p>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Active Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-green-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Active Members</h2>
                <ul>
                    @foreach($activeMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $member->first_name }} {{ $member->last_name }} <span class="font-semibold text-green-500">Active</span></li>
                    @endforeach
                </ul>
            </div>

            <!-- Inactive Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-orange-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Inactive Members</h2>
                <ul>
                    @foreach($inactiveMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $member->first_name }} {{ $member->last_name }} <span class="font-semibold text-orange-500">Inactive</span></li>
                    @endforeach
                </ul>
            </div>

            <!-- Expired Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-red-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Expired Members</h2>
                <ul>
                    @foreach($expiredMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $member->first_name }} {{ $member->last_name }} <span class="font-semibold text-red-500">Expired</span></li>
                    @endforeach
                </ul>
            </div>
            <!-- Total Revenue -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-blue-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Total Revenue</h2>
                <p class="text-gray-600">Total Revenue: <span class="font-bold">${{ number_format($totalRevenue ?? 0, 2) }}</span></p>
            </div>

            <!-- Inventory Overview -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-yellow-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Inventory Overview</h2>
                <div>
                    <p class="text-gray-600">Total Equipment: <span class="font-bold">{{ $totalEquipment }}</span></p>
                    <p class="text-gray-600">Equipment Unavailable: <span class="font-bold">{{ $equipmentInUse }}</span></p>
                    <p class="text-gray-600">Equipment Available: <span class="font-bold">{{ $equipmentAvailable }}</span></p>
                </div>
            </div>

            <!-- Expiring Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-orange-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Expiring Members (5 days)</h2>
                <ul>
                    @foreach($expiringMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $member->first_name }} {{ $member->last_name }} <span class="font-semibold text-orange-500">Expires in {{ $member->days_until_expiration }} days</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">📈 Total Members as of {{ date('F Y') }}</h2>
            <div class="relative w-full" style="height: 400px;">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>
<style>
    .digital-clock {
        background: linear-gradient(145deg, #1f2937, #1f2937);
        padding: 20px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(26, 19, 99, 0.2),
                    inset 0 -2px 10px rgba(255, 255, 255, 0.1);
        color: white;
        font-family: 'Arial', sans-serif;
        min-width: 300px;
    }

    .time-display {
        font-size: 2.5rem;
        font-weight: bold;
        text-align: center;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }

    .colon {
        animation: blink 1s infinite;
    }

    .date-display {
        font-size: 1rem;
        text-align: center;
        margin-top: 5px;
        color: rgba(255, 255, 255, 0.8);
    }

    #session {
        font-size: 1rem;
        margin-left: 10px;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.3; }
        100% { opacity: 1; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();
        const session = hours >= 12 ? 'PM' : 'AM';

        // Convert to 12-hour format
        hours = hours % 12;
        hours = hours ? hours : 12;

        // Add leading zeros
        const formatTime = (time) => time.toString().padStart(2, '0');

        // Update time
        document.getElementById('hours').textContent = formatTime(hours);
        document.getElementById('minutes').textContent = formatTime(minutes);
        document.getElementById('seconds').textContent = formatTime(seconds);
        document.getElementById('session').textContent = session;

        // Update date
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        document.getElementById('date').textContent = now.toLocaleDateString('en-US', options);
    }

    // Update clock every second
    setInterval(updateClock, 1000);
    // Initial update
    updateClock();
    window.onload = function() {
    console.log("Total Members for Current Month:", {!! json_encode($totalMembers) !!});

    const ctx = document.getElementById('growthChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["{{ date('F Y') }}"], // X-axis: Current Month
            datasets: [{
                label: 'Total Members',
                data: [{!! json_encode($totalMembers) !!}], // Y-axis: Total Members Count
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5 // 👈 This ensures Y-axis increments by 5
                    }
                }
            }
        }
    });
};
</script>
@endsection
