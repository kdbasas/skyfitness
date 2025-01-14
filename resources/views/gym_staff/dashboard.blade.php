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
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[#1A1363]">Welcome, {{ Auth::guard('gym_staff')->user()->first_name }}</h1>
            <p class="text-gray-600 mt-2">Here's what's happening at the gym today:</p>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Active Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-green-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Active Members</h2>
                <ul>
                    @forelse($activeMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-green-500 font-medium">Active</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No active members.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Expired Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-red-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Expired Members</h2>
                <ul>
                    @forelse($expiredMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-red-500 font-medium">Expired</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No expired members.</p>
                    @endforelse
                </ul>
            </div>

            <!-- Expiring Members -->
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-orange-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Expiring Members (5 Days)</h2>
                <ul>
                    @forelse($expiringMembers as $member)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $member->first_name }} {{ $member->last_name }}</span>
                            <span class="text-orange-500 font-medium">Expires in {{ $member->days_until_expiration }} days</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No members expiring within 5 days.</p>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Attendance Management -->
        <div class="mt-8">
            <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Attendance Management</h2>
                <ul>
                    @forelse($attendance as $record)
                        <li class="flex justify-between py-2 border-b border-gray-200">
                            <span>{{ $record->member->first_name }} {{ $record->member->last_name }}</span>
                            <span class="text-blue-500 font-medium">{{ $record->date }}</span>
                        </li>
                    @empty
                        <p class="text-gray-500">No attendance records available.</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
