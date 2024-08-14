@extends('layouts.main')

@section('content')
    <div class="p-6 bg-gray-100 min-h-screen">
        <div class="container mx-auto">
            <!-- Welcome Message -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}!</h1>
            </div>

            <!-- Dashboard Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Attendance Overview -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-700">Attendance Overview</h2>
                    <p class="text-gray-600 mt-2">Check-in/Check-out statistics for today.</p>
                    <!-- Example Data -->
                    <ul class="mt-4">
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">Today's Check-ins:</span>
                            <span class="font-bold text-gray-800">120</span>
                        </li>
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">Today's Check-outs:</span>
                            <span class="font-bold text-gray-800">115</span>
                        </li>
                    </ul>
                </div>

                <!-- Membership Overview -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-700">Membership Overview</h2>
                    <p class="text-gray-600 mt-2">Recent membership activities.</p>
                    <!-- Example Data -->
                    <ul class="mt-4">
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">New Members Today:</span>
                            <span class="font-bold text-gray-800">10</span>
                        </li>
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">Expired Memberships:</span>
                            <span class="font-bold text-gray-800">3</span>
                        </li>
                    </ul>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-xl font-semibold text-gray-700">Upcoming Events</h2>
                    <p class="text-gray-600 mt-2">Events and classes scheduled for this week.</p>
                    <!-- Example Data -->
                    <ul class="mt-4">
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">Yoga Class:</span>
                            <span class="font-bold text-gray-800">Monday, 10 AM</span>
                        </li>
                        <li class="flex justify-between border-b py-2">
                            <span class="text-gray-600">Spin Class:</span>
                            <span class="font-bold text-gray-800">Wednesday, 6 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
