@extends('layouts.app')

@section('content')
<div class="flex min-h-screen bg-[#ECE9E9]">
    <!-- Sidebar -->
    @include('include.sidebarstaff')

    <!-- Main Content -->
    <div class="flex-1 ml-64 px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center justify-center mb-6 p-6 bg-transparent text-[#1A1363]" style="margin-top: -20px;">
            <img src="{{ asset('img/logosky 2.png') }}" alt="Gym Logo" class="w-40 h-30 mr-4">
            <h1 class="text-4xl font-bold">ROXAS SKY FITNESS GYM</h1>
        </div>

        <!-- Welcome Message -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-[#1A1363]">Welcome, {{ Auth::guard('gym_staff')->user()->first_name }}</h1>
            <p class="text-gray-500">Here's what's happening at the gym today:</p>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Member Management -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-green-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Member Management</h2>
                <ul>
                    @foreach($members as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $member->first_name }} {{ $member->last_name }} <span class="font-semibold text-green-500">Active</span></li>
                    @endforeach
                </ul>
            </div>

            <!-- Attendance Management -->
            <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300 border-l-4 border-blue-500">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Attendance Management</h2>
                <ul>
                    @foreach($attendance as $record)
                        <li class="flex justify-between py-2 border-b border-gray-200">{{ $record->member->first_name }} {{ $record->member->last_name }} <span class="font-semibold text-blue-500">{{ $record->date }}</span></li>
                    @endforeach
                </ul>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
