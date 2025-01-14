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
            <h1 class="text-3xl font-bold mb-4 text-[#1A1363]">Report Analytics</h1>

            <form method="GET" action="{{ route('admin.reportAnalytics') }}" class="flex items-center space-x-4 mb-4">
                <label for="month" class="font-medium">Select Month:</label>
                <input type="month" name="month" id="month" class="border rounded p-2" value="{{ $selectedMonth ?? Carbon::now()->format('Y-m') }}">
                <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Generate Report</button>
            </form>

            <h3 class="text-xl font-semibold mb-2">Report for {{ Carbon\Carbon::parse($selectedMonth ?? Carbon::now()->format('Y-m'))->format('F Y') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Members Registered</h4>
                    <p class="text-2xl">{{ $memberRegistrations ?? 0 }}</p>
                </div>
                <div class="bg-blue-600 text-white p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold">Revenue of The Month:</h4>
                    <p class="text-2xl">${{ number_format($totalRevenue ?? 0, 2) }}</p>
                </div>                                         
            <!-- Graph Growth Section -->
            <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Graph Growth</h2>
                <canvas id="growthChart" width="100" height="50"></canvas>
            </div>
                    <!-- Add a canvas for the promo chart -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1A1363]">Promo Trends</h2>
            <canvas id="promoChart" width="100" height="50"></canvas>
        </div>

            <form method="GET" action="{{ route('admin.printReport') }}">
                <input type="hidden" name="month" value="{{ $selectedMonth ?? Carbon::now()->format('Y-m') }}">
                <button type="submit" class="bg-green-500 text-white rounded px-4 py-2 hover:bg-green-600">Print Report</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = {!! json_encode($months ?? []) !!};
    const registrationCounts = {!! json_encode($registrationCounts ?? []) !!};
    const promoLabels = {!! json_encode($promoLabels ?? []) !!};
    const promoCounts = {!! json_encode($promoCounts ?? []) !!};

    const ctx = document.getElementById('growthChart').getContext('2d');
    const growthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Member Registrations',
                data: registrationCounts,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const promoCtx = document.getElementById('promoChart').getContext('2d');
    const promoChart = new Chart(promoCtx, {
        type: 'pie',
        data: {
            labels: promoLabels,
            datasets: [{
                label: 'Promo Trends',
                data: promoCounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Promo Trends'
                }
            }
        }
    });
</script>
@push('styles')
<style>
    #promoChart {
        width: 150px;
        height: 150px;
    }
</style>
@endpush
@endpush
@endsection
