@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#ECE9E9]">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-6 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
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

        <!-- Growth Graph Section -->
        <div class="bg-white shadow-lg rounded-lg p-6 mt-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Growth Graph</h2>
            <canvas id="growthChart" class="w-full h-64"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('growthChart').getContext('2d');
    const growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($months ),
            datasets: [{
                label: 'Monthly Registrations',
                data: @json($registrationCounts),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Registrations'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
