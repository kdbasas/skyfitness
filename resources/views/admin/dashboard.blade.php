@extends('layouts.app')

@section('content')
<div class="flex min-h-screen">
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
            <!-- Sales Percentage Pie Chart -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Sales Percentage</h2>
                <div class="w-full h-64">
                    <canvas id="salesPieChart"></canvas>
                </div>
            </div>

            <!-- Active Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Active Members</h2>
                <ul>
                    <li class="flex justify-between py-2 border-b border-gray-200">John Doe <span class="font-semibold text-green-500">Active</span></li>
                    <li class="flex justify-between py-2 border-b border-gray-200">Jane Smith <span class="font-semibold text-green-500">Active</span></li>
                </ul>
            </div>

            <!-- Inventory Overview -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Inventory Overview</h2>
                <div>
                    <p class="text-gray-600">Total Equipment: <span class="font-bold">0</span></p>
                    <p class="text-gray-600">Equipment in Use: <span class="font-bold">0</span></p>
                    <p class="text-gray-600">Equipment Available: <span class="font-bold">0</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Pie Chart
    const ctx = document.getElementById('salesPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Membership', 'Personal Training', 'Merchandise'],
            datasets: [{
                label: 'Sales Percentage',
                data: [50, 30, 20],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }],
        },
        options: {
            responsive: true,
        },
    });
</script>
@endpush
@endsection
