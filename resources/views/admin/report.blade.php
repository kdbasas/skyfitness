@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#ECE9E9]">
    <!-- Sidebar -->
    @include('include.sidebar')

    <!-- Main Content -->
    <div class="flex-1 ml-64 p-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-4 text-[#1A1363]">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Report Analytics Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold mb-4 text-[#1A1363]">📊 Report Analytics</h1>

            <form method="GET" action="{{ route('admin.reportAnalytics') }}" class="flex items-center space-x-4 mb-6">
                <label for="month" class="font-medium text-lg">Select Month:</label>
                <input type="month" name="month" id="month" class="border-2 border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" value="{{ $selectedMonth ?? Carbon::now()->format('Y-m') }}">
                <button type="submit" class="bg-blue-600 text-white font-semibold rounded-lg px-4 py-2 transition hover:bg-blue-700">Sort</button>
            </form>

            <h3 class="text-xl font-semibold mb-4 text-gray-700">📅 Report for {{ Carbon\Carbon::parse($selectedMonth ?? Carbon::now()->format('Y-m'))->format('F Y') }}</h3>

            <!-- Report Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Members Registered</h4>
                    <p class="text-2xl">{{ $memberRegistrations ?? 0 }}</p>
                </div>
                <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Revenue of The Month:</h4>
                    <p class="text-2xl">${{ number_format($totalRevenue ?? 0, 2) }}</p>
                </div>                                                                          
            </div>
        </div>

        <!-- Graph Growth Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">📈 Growth of New Members (Daily)</h2>
            <div class="relative w-full" style="height: 400px;">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Promo Trends Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">🎟️ Promo Trends</h2>
            <div class="relative w-full" style="height: 400px;">
                <canvas id="promoChart"></canvas>
            </div>
        </div>

        <!-- Print Report Button -->
        <div class="flex justify-end">
            <form method="GET" action="{{ route('admin.printReport') }}">
                <input type="hidden" name="month" value="{{ $selectedMonth ?? Carbon::now()->format('Y-m') }}">
                <button type="submit" class="bg-green-500 text-white font-semibold rounded-lg px-6 py-3 transition hover:bg-green-700">
                    🖨️ Print Report
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const days = {!! json_encode($days ?? []) !!};
    const dailyCounts = {!! json_encode($dailyCounts ?? []) !!};
    const promoLabels = {!! json_encode($promoLabels ?? []) !!};
    const promoCounts = {!! json_encode($promoCounts ?? []) !!};

    // Growth Chart (Daily Registrations)
    const ctx = document.getElementById('growthChart').getContext('2d');
    const growthChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'New Members Per Day',
                data: dailyCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Day of the Month',
                    font: { size: 14, weight: 'bold' }
                }
            },
            y: {
                beginAtZero: true, // Start from zero
                ticks: {
                    stepSize: 5, // Set the step size to 5
                    callback: function(value) {
                        return value; // Display the value as is
                    }
                },
                title: {
                    display: true,
                    text: 'Number of Registrations',
                    font: { size: 14, weight: 'bold' }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return `Registrations: ${tooltipItem.raw}`; // Show exact number on hover
                    }
                }
            }
        }
    }
});

    // Promo Trends Chart
    const promoCtx = document.getElementById('promoChart').getContext('2d');
    const promoChart = new Chart(promoCtx, {
        type: 'doughnut',
        data: {
            labels: promoLabels,
            datasets: [{
                label: 'Promo Trends',
                data: promoCounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Promo Trends for the Selected Month' }
            }
        }
    });
</script>
@endpush
@endsection